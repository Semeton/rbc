<?php

declare(strict_types=1);

namespace App\Notification\Jobs;

use App\Enums\NotificationType;
use App\Notification\Services\NotificationGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly ?NotificationType $type = null
    ) {}

    /**
     * Execute the job
     */
    public function handle(NotificationGeneratorService $generatorService): void
    {
        try {
            if ($this->type) {
                // Generate notifications for a specific type
                $count = $generatorService->generateNotificationsByType($this->type);
                Log::info("Generated {$count} notifications of type: {$this->type->value}");
            } else {
                // Generate all notifications
                $results = $generatorService->generateAllNotifications();
                $totalCount = array_sum($results);
                
                Log::info("Generated notifications", [
                    'total' => $totalCount,
                    'by_type' => $results,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to generate notifications", [
                'error' => $e->getMessage(),
                'type' => $this->type?->value,
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle a job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Notification generation job failed", [
            'error' => $exception->getMessage(),
            'type' => $this->type?->value,
        ]);
    }
}
