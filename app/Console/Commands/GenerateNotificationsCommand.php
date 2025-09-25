<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\NotificationType;
use App\Notification\Jobs\GenerateNotificationsJob;
use App\Notification\Services\NotificationGeneratorService;
use Illuminate\Console\Command;

class GenerateNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:generate 
                            {--type= : Generate notifications for a specific type (pending_atc, overdue_balance, maintenance_reminder, transaction_alert, payment_reminder, system_alert)}
                            {--sync : Run synchronously instead of queuing}';

    /**
     * The console command description.
     */
    protected $description = 'Generate system notifications based on business rules';

    /**
     * Execute the console command.
     */
    public function handle(NotificationGeneratorService $generatorService): int
    {
        $type = $this->option('type');
        $sync = $this->option('sync');

        if ($type) {
            // Validate notification type
            try {
                $notificationType = NotificationType::from($type);
            } catch (\ValueError $e) {
                $this->error("Invalid notification type: {$type}");
                $this->line("Available types: " . implode(', ', NotificationType::getAllTypes()));
                return 1;
            }

            if ($sync) {
                // Run synchronously
                $count = $generatorService->generateNotificationsByType($notificationType);
                $this->info("Generated {$count} notifications of type: {$notificationType->getDisplayName()}");
            } else {
                // Queue the job
                GenerateNotificationsJob::dispatch($notificationType);
                $this->info("Queued notification generation job for type: {$notificationType->getDisplayName()}");
            }
        } else {
            if ($sync) {
                // Run all notifications synchronously
                $results = $generatorService->generateAllNotifications();
                $totalCount = array_sum($results);
                
                $this->info("Generated {$totalCount} total notifications:");
                foreach ($results as $type => $count) {
                    if ($count > 0) {
                        $this->line("  - " . NotificationType::from($type)->getDisplayName() . ": {$count}");
                    }
                }
            } else {
                // Queue the job for all types
                GenerateNotificationsJob::dispatch();
                $this->info("Queued notification generation job for all types");
            }
        }

        return 0;
    }
}