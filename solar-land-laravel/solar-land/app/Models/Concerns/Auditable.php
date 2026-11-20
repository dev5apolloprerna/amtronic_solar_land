<?php

namespace App\Models\Concerns;

use App\Services\AuditService;

/**
 * Writes CREATE / UPDATE (one row per changed field) / DELETE rows to audit_logs.
 * The model sets $auditModule and may override auditLabel().
 */
trait Auditable
{
    /** Fields never written to the audit trail in clear text. */
    protected static array $auditMasked = ['password', 'remember_token', 'bank_ac'];

    /** Fields ignored entirely. */
    protected static array $auditIgnored = ['created_at', 'updated_at', 'updated_by', 'created_by', 'deleted_at', 'last_login_at', 'last_login_ip', 'failed_attempts'];

    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            app(AuditService::class)->log($model->auditModuleKey(), 'CREATE', $model->getKey(), $model->auditLabel(), null, null, 'New record');
        });

        static::updated(function ($model) {
            $audit = app(AuditService::class);
            foreach ($model->getChanges() as $field => $new) {
                if (in_array($field, static::$auditIgnored, true)) {
                    continue;
                }
                $old = $model->getOriginal($field);
                if (in_array($field, static::$auditMasked, true)) {
                    [$old, $new] = ['(hidden)', '(changed)'];
                }
                $audit->log($model->auditModuleKey(), 'UPDATE', $model->getKey(), $model->auditLabel(), $field, self::auditString($old), self::auditString($new));
            }
        });

        static::deleted(function ($model) {
            app(AuditService::class)->log($model->auditModuleKey(), 'DELETE', $model->getKey(), $model->auditLabel(), null, $model->getTable().' #'.$model->getKey(), null);
        });
    }

    protected static function auditString($value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if ($value instanceof \BackedEnum) {
            return (string) $value->value;
        }
        if (is_array($value)) {
            return json_encode($value);
        }

        return $value === null ? null : (string) $value;
    }

    public function auditModuleKey(): string
    {
        return property_exists($this, 'auditModule') ? $this->auditModule : $this->getTable();
    }

    public function auditLabel(): string
    {
        return method_exists($this, 'getCodeAttribute') ? $this->code : (string) $this->getKey();
    }
}
