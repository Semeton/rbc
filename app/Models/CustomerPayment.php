<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Notification\Services\NotificationService;
use App\Enums\NotificationType;

class CustomerPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the customer for this payment
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by customer
     */
    public function scopeByCustomer(Builder $query, int $customerId): void
    {
        $query->where('customer_id', $customerId);
    }

    /**
     * Scope to filter by bank
     */
    public function scopeByBank(Builder $query, string $bankName): void
    {
        $query->where('bank_name', 'like', "%{$bankName}%");
    }

    /**
     * Scope to filter by amount range
     */
    public function scopeByAmountRange(Builder $query, float $minAmount, float $maxAmount): void
    {
        $query->whereBetween('amount', [$minAmount, $maxAmount]);
    }

    /**
     * Get the formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    /**
     * Scope to get recent payments
     */
    public function scopeRecent(Builder $query, int $days = 30): void
    {
        $query->where('payment_date', '>=', now()->subDays($days));
    }

    /**
     * Scope to search payments by notes
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('notes', 'like', "%{$search}%")
                ->orWhere('bank_name', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Boot method to add model events
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($payment) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'module' => 'Payment',
                'description' => "Payment of {$payment->amount} for customer '{$payment->customer->name}' was created",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Trigger payment notification
            static::triggerPaymentNotification($payment);
        });

        static::updated(function ($payment) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'module' => 'Payment',
                'description' => "Payment of {$payment->amount} for customer '{$payment->customer->name}' was updated",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        static::deleted(function ($payment) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'module' => 'Payment',
                'description' => "Payment of {$payment->amount} for customer '{$payment->customer->name}' was deleted",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }

    /**
     * Trigger notifications for payment events
     */
    private static function triggerPaymentNotification(CustomerPayment $payment): void
    {
        $notificationService = app(NotificationService::class);

        // High-value payment alert (>₦1,000,000)
        if ($payment->amount > 1000000) {
            $notificationService->createSystemNotification(
                NotificationType::TRANSACTION_ALERT,
                "High-Value Payment Received",
                "High-value payment received: ₦" . number_format($payment->amount, 2) . " from customer {$payment->customer->name}.",
                [
                    'payment_id' => $payment->id,
                    'customer_id' => $payment->customer->id,
                    'customer_name' => $payment->customer->name,
                    'amount' => $payment->amount,
                    'payment_date' => $payment->payment_date->toDateString(),
                    'bank_name' => $payment->bank_name,
                ],
                now()->addDays(3) // Expire in 3 days
            );
        }
    }
}
