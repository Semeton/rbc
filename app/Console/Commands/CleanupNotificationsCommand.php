<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notification\Jobs\CleanupExpiredNotificationsJob;
use App\Notification\Services\NotificationService;
use Illuminate\Console\Command;

class CleanupNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:cleanup 
                            {--sync : Run synchronously instead of queuing}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up expired notifications';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $sync = $this->option('sync');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            // Show what would be deleted
            $expiredCount = \App\Models\Notification::expired()->count();
            $this->info("Found {$expiredCount} expired notifications that would be deleted");
            
            if ($expiredCount > 0) {
                $this->line("Expired notifications:");
                $expiredNotifications = \App\Models\Notification::expired()
                    ->latest('created_at')
                    ->limit(10)
                    ->get();
                
                foreach ($expiredNotifications as $notification) {
                    $this->line("  - {$notification->title} (expired: {$notification->expires_at->format('Y-m-d H:i:s')})");
                }
                
                if ($expiredCount > 10) {
                    $this->line("  ... and " . ($expiredCount - 10) . " more");
                }
            }
            
            return 0;
        }

        if ($sync) {
            // Run synchronously
            $count = $notificationService->cleanupExpiredNotifications();
            $this->info("Cleaned up {$count} expired notifications");
        } else {
            // Queue the job
            CleanupExpiredNotificationsJob::dispatch();
            $this->info("Queued notification cleanup job");
        }

        return 0;
    }
}