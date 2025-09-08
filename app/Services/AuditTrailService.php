<?php

namespace App\Services;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

class AuditTrailService
{
    /**
     * Log an audit trail entry
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): AuditTrail {
        return AuditTrail::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }

    /**
     * Log a model action
     */
    public static function logModelAction(
        string $action,
        string $modelName,
        mixed $model,
        ?int $userId = null
    ): AuditTrail {
        $description = self::generateModelDescription($action, $modelName, $model);

        return self::log($action, $modelName, $description, $userId);
    }

    /**
     * Generate description for model actions
     */
    private static function generateModelDescription(string $action, string $modelName, mixed $model): string
    {
        $identifier = self::getModelIdentifier($model);

        return match ($action) {
            'created' => "{$modelName} '{$identifier}' was created",
            'updated' => "{$modelName} '{$identifier}' was updated",
            'deleted' => "{$modelName} '{$identifier}' was deleted",
            'restored' => "{$modelName} '{$identifier}' was restored",
            default => "{$modelName} '{$identifier}' was {$action}",
        };
    }

    /**
     * Get model identifier for description
     */
    private static function getModelIdentifier(mixed $model): string
    {
        if (is_object($model)) {
            // Try common identifier fields
            if (isset($model->name)) {
                return $model->name;
            }
            if (isset($model->email)) {
                return $model->email;
            }
            if (isset($model->registration_number)) {
                return $model->registration_number;
            }
            if (isset($model->atc_number)) {
                return $model->atc_number;
            }
            if (isset($model->id)) {
                return "ID: {$model->id}";
            }
        }

        return 'Unknown';
    }

    /**
     * Log user login
     */
    public static function logLogin(int $userId, bool $success = true): AuditTrail
    {
        $action = $success ? 'login_success' : 'login_failed';
        $description = $success ? 'User logged in successfully' : 'User login failed';

        return self::log($action, 'Authentication', $description, $userId);
    }

    /**
     * Log user logout
     */
    public static function logLogout(int $userId): AuditTrail
    {
        return self::log('logout', 'Authentication', 'User logged out', $userId);
    }

    /**
     * Log password change
     */
    public static function logPasswordChange(int $userId): AuditTrail
    {
        return self::log('password_change', 'Authentication', 'User changed password', $userId);
    }

    /**
     * Log data export
     */
    public static function logDataExport(string $module, string $format, int $recordCount, ?int $userId = null): AuditTrail
    {
        $description = "Data exported from {$module} in {$format} format ({$recordCount} records)";

        return self::log('data_export', $module, $description, $userId);
    }

    /**
     * Log bulk operations
     */
    public static function logBulkOperation(string $action, string $module, int $recordCount, ?int $userId = null): AuditTrail
    {
        $description = "Bulk {$action} operation on {$module} ({$recordCount} records)";

        return self::log("bulk_{$action}", $module, $description, $userId);
    }

    /**
     * Clean up old audit trails
     */
    public static function cleanup(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        return AuditTrail::where('created_at', '<', $cutoffDate)->delete();
    }

    /**
     * Get audit trail statistics
     */
    public static function getStatistics(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $stats = AuditTrail::where('created_at', '>=', $startDate)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->pluck('count', 'action')
            ->toArray();

        $moduleStats = AuditTrail::where('created_at', '>=', $startDate)
            ->selectRaw('module, COUNT(*) as count')
            ->groupBy('module')
            ->pluck('count', 'module')
            ->toArray();

        return [
            'actions' => $stats,
            'modules' => $moduleStats,
            'total_entries' => array_sum($stats),
            'period_days' => $days,
        ];
    }
}
