<?php

declare(strict_types=1);

namespace App\Notification\Jobs;

use App\Notification\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupExpiredNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job
     */
    public function handle(NotificationService $notificationService): void
    {
        try {
            $count = $notificationService->cleanupExpiredNotifications();
            
            if ($count > 0) {
                Log::info("Cleaned up {$count} expired notifications");
            }
        } catch (\Exception $e) {
            Log::error("Failed to cleanup expired notifications", [
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle a job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Notification cleanup job failed", [
            'error' => $exception->getMessage(),
        ]);
    }
}
