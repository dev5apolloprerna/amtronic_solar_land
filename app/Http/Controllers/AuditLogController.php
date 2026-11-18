<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

/** Read-only audit trail. Entries cannot be edited or deleted. */
class AuditLogController extends Controller
{
    public const ACTIONS = ['CREATE', 'UPDATE', 'DELETE', 'EXPORT', 'LOGIN', 'LOGIN_FAILED', 'LOGOUT'];

    public function index(Request $request)
    {
        $q = AuditLog::query()
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('record_label', 'like', "%$s%")->orWhere('field_name', 'like', "%$s%")
                ->orWhere('old_value', 'like', "%$s%")->orWhere('new_value', 'like', "%$s%")))
            ->when($request->module, fn ($q, $v) => $q->where('module', $v))
            ->when($request->action, fn ($q, $v) => $q->where('action', $v))
            ->when($request->user, fn ($q, $v) => $q->where('username', $v))
            ->when($request->from, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->to, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest('id');

        if ($export = $this->maybeExport('audit', 'Audit Trail', ['ID', 'When', 'User', 'IP', 'Table', 'Record', 'Action', 'Field', 'Old value', 'New value'],
            $q->limit(50000)->get()->map(fn ($a) => ['AUD'.str_pad((string) $a->id, 5, '0', STR_PAD_LEFT), $a->created_at->format('Y-m-d H:i:s'), $a->username, $a->ip_address,
                $a->table_name, $a->record_label ?? $a->record_id, $a->action, $a->field_name, $a->old_value, $a->new_value]))) {
            return $export;
        }

        return view('audit.index', [
            'rows' => $q->paginate(50)->withQueryString(),
            'modules' => AuditLog::query()->distinct()->orderBy('module')->pluck('module')->mapWithKeys(fn ($m) => [$m => config("solar.modules.$m.table", $m)])->all(),
            'users' => User::withTrashed()->orderBy('username')->pluck('username', 'username')->all(),
        ]);
    }

    /** History modal for one record. */
    public function record(string $module, string $id)
    {
        $user = auth()->user();
        abort_unless(in_array($module, ['user', 'role'], true) ? $user->isSuper() : $user->hasPermission($module, 'view'), 403);
        $rows = AuditLog::where('module', $module)->where('record_id', $id)->latest('id')->limit(200)->get();

        return response()->json([
            'title' => 'History · '.($rows->first()->record_label ?? $module.' #'.$id),
            'html' => $rows->isEmpty()
                ? '<p class="hint" style="margin:0">No changes recorded for this record since the system went live.</p>'
                : view('audit._table', ['rows' => $rows])->render(),
        ]);
    }
}
