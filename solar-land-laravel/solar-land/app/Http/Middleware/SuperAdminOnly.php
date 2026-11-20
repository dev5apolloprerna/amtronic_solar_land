<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isSuper()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Only the Super Admin can manage users and roles.'], 403);
            }
            abort(403, 'Only the Super Admin can manage users and roles.');
        }

        return $next($request);
    }
}
