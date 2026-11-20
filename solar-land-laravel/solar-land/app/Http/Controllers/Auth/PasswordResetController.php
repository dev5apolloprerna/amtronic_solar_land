<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/** Forgot / reset password using Laravel's password broker (mail settings in .env). */
class PasswordResetController extends Controller
{
    public function request()
    {
        return view('auth.forgot');
    }

    public function email(Request $request)
    {
        $request->validate(['login' => 'required|string'], ['login.required' => 'Enter your username or email.']);
        $user = User::where('username', $request->login)->orWhere('email', $request->login)->first();
        if ($user) {
            Password::sendResetLink(['email' => $user->email]);
            app(AuditService::class)->log('session', 'UPDATE', $user->id, $user->username, 'password_reset', null, 'Reset link requested', $user, force: true);
        }

        // Same answer whether or not the account exists.
        return back()->with('status', 'If an account matches '.$request->login.', a reset link has been sent to its registered email. The link expires in 60 minutes.');
    }

    public function reset(Request $request, string $token)
    {
        return view('auth.reset', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()],
        ]);
        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60), 'is_locked' => false, 'failed_attempts' => 0])->save();
            event(new PasswordReset($user));
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Your password has been changed. Sign in with the new password.')
            : back()->withErrors(['email' => __($status)]);
    }
}
