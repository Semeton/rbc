<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Notification\Jobs\GenerateNotificationsJob;
use App\Notification\Jobs\CleanupExpiredNotificationsJob;
use App\Enums\NotificationType;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule notification generation tasks
Schedule::job(new GenerateNotificationsJob(NotificationType::PENDING_ATC))
    ->dailyAt('09:00')
    ->name('generate-pending-atc-notifications')
    ->description('Generate pending ATC notifications');

Schedule::job(new GenerateNotificationsJob(NotificationType::OVERDUE_BALANCE))
    ->dailyAt('10:00')
    ->name('generate-overdue-balance-notifications')
    ->description('Generate overdue balance notifications');

Schedule::job(new GenerateNotificationsJob(NotificationType::MAINTENANCE_REMINDER))
    ->weeklyOn(1, '09:00') // Monday at 9 AM
    ->name('generate-maintenance-reminder-notifications')
    ->description('Generate maintenance reminder notifications');

Schedule::job(new GenerateNotificationsJob(NotificationType::PAYMENT_REMINDER))
    ->dailyAt('11:00')
    ->name('generate-payment-reminder-notifications')
    ->description('Generate payment reminder notifications');

Schedule::job(new GenerateNotificationsJob(NotificationType::SYSTEM_ALERT))
    ->dailyAt('12:00')
    ->name('generate-system-alert-notifications')
    ->description('Generate system alert notifications');

// Schedule notification cleanup
Schedule::job(new CleanupExpiredNotificationsJob())
    ->dailyAt('02:00')
    ->name('cleanup-expired-notifications')
    ->description('Clean up expired notifications');
