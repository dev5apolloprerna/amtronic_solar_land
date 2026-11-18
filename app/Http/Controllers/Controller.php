<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\ExportService;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /** JSON for the right-hand drawer: title, small sub-title and rendered form HTML. */
    protected function drawer(string $title, string $sub, string $view, array $data = [], bool $readonly = false, string $module = '', ?string $saveLabel = null): JsonResponse
    {
        return response()->json([
            'title' => $title,
            'sub' => $sub,
            'module' => $module,
            'readonly' => $readonly,
            'saveLabel' => $saveLabel,
            'html' => view($view, $data + ['readonly' => $readonly])->render(),
        ]);
    }

    /** Success JSON consumed by app.js: shows a toast and reloads / redirects. */
    protected function ok(string $message, ?string $redirect = null, ?string $sub = null): JsonResponse
    {
        $sub ??= ($id = AuditLog::query()->latest('id')->value('id')) ? 'Logged to audit trail as AUD'.str_pad((string) $id, 5, '0', STR_PAD_LEFT) : null;

        return response()->json(['message' => $message, 'redirect' => $redirect, 'sub' => $sub]);
    }

    /** Refusal with a readable reason (422). */
    protected function refuse(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['message' => $message, 'errors' => ['_' => [$message]]], $status);
    }

    protected function canEdit(string $module): bool
    {
        return (bool) auth()->user()?->hasPermission($module, 'edit');
    }

    /** Excel/PDF download when ?format=xlsx|pdf is present. */
    protected function maybeExport(string $module, string $title, array $headings, iterable $rows)
    {
        $format = request('format');
        if (! in_array($format, ['xlsx', 'pdf'], true)) {
            return null;
        }
        abort_unless(auth()->user()->hasPermission($module, 'export'), 403, 'Your role does not allow export here.');

        return app(ExportService::class)->download($module, $title, $headings, $rows, $format);
    }
}
