<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Route middleware: perm:<module>,<action>  e.g. perm:lease,edit */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $module, string $action = 'view'): Response
    {
        $user = $request->user();
        if (! $user || ! $user->hasPermission($module, $action)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your role does not allow '.$action.' in '.config("solar.modules.$module.label", $module).'.'], 403);
            }
            abort(403, 'Your role does not allow this action.');
        }

        return $next($request);
    }
}
