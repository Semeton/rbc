<?php

namespace App\Jobs;

use App\Services\AuditTrailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupAuditTrailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private int $daysToKeep = 90
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $deletedCount = AuditTrailService::cleanup($this->daysToKeep);

            Log::info("Audit trail cleanup completed. Deleted {$deletedCount} old records.");

            // Log the cleanup action itself
            AuditTrailService::log(
                'cleanup',
                'System',
                "Audit trail cleanup completed. Deleted {$deletedCount} records older than {$this->daysToKeep} days."
            );
        } catch (\Exception $e) {
            Log::error('Audit trail cleanup failed: '.$e->getMessage());

            throw $e;
        }
    }
}
