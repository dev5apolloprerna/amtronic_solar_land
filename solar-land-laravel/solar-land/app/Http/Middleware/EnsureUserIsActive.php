<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Signs out a user who was deactivated or locked while signed in. */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $u = $request->user();
        if ($u && (! $u->is_active || $u->is_locked)) {
            Auth::logout();
            $request->session()->invalidate();

            return redirect()->route('login')->withErrors(['username' => 'Your account is inactive or locked. Contact the Super Admin.']);
        }

        return $next($request);
    }
}
