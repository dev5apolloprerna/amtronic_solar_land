<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditService
{
    /** When true, model events do not write rows (used for bulk generation, which logs one summary row instead). */
    private static bool $muted = false;

    public static function muted(callable $fn)
    {
        $was = self::$muted;
        self::$muted = true;
        try {
            return $fn();
        } finally {
            self::$muted = $was;
        }
    }

    public function log(string $module, string $action, $recordId = null, ?string $label = null, ?string $field = null, ?string $old = null, ?string $new = null, ?User $as = null, ?string $username = null, bool $force = false): ?AuditLog
    {
        if (self::$muted && ! $force) {
            return null;
        }
        $user = $as ?? auth()->user();

        return AuditLog::create([
            'user_id' => $user?->id,
            'username' => $username ?? $user?->username ?? 'system',
            'ip_address' => request()?->ip(),
            'module' => $module,
            'table_name' => config("solar.modules.$module.table", $module),
            'record_id' => $recordId === null ? null : (string) $recordId,
            'record_label' => $label === null ? null : mb_substr($label, 0, 120),
            'action' => $action,
            'field_name' => $field,
            'old_value' => $old,
            'new_value' => $new,
        ]);
    }

    public function export(string $module, int $rows, string $format): ?AuditLog
    {
        return $this->log($module, 'EXPORT', null, config("solar.modules.$module.label", $module), null, null, "$rows rows to $format", force: true);
    }
}
