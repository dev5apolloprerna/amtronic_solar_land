<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/** Sign in by username or email. Locks the account after N wrong passwords (config solar.max_login_attempts). */
class LoginController extends Controller
{
    public function __construct(private AuditService $audit) {}

    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate(['username' => 'required|string', 'password' => 'required|string'], [
            'username.required' => 'Enter your username.', 'password.required' => 'Enter your password.',
        ]);
        $login = trim($data['username']);
        $user = User::with('role')->where('username', $login)->orWhere('email', $login)->first();
        $max = (int) config('solar.max_login_attempts', 5);

        if (! $user) {
            $this->audit->log('session', 'LOGIN_FAILED', null, $login, 'password', null, 'Unknown username', username: $login, force: true);

            return back()->withInput($request->only('username'))->withErrors(['username' => 'Username or password is incorrect.']);
        }
        if ($user->is_locked) {
            $this->audit->log('session', 'LOGIN_FAILED', $user->id, $user->username, 'password', null, 'Attempt on locked account', $user, force: true);

            return back()->withInput($request->only('username'))->withErrors(['username' => 'This account is locked after too many wrong passwords. Ask the Super Admin to unlock it.']);
        }
        if (! $user->is_active) {
            $this->audit->log('session', 'LOGIN_FAILED', $user->id, $user->username, 'password', null, 'Inactive account', $user, force: true);

            return back()->withInput($request->only('username'))->withErrors(['username' => 'This account is deactivated. Contact the Super Admin.']);
        }
        if (! Hash::check($data['password'], $user->password)) {
            $user->failed_attempts++;
            $left = $max - $user->failed_attempts;
            if ($left <= 0) {
                $user->is_locked = true;
            }
            $user->saveQuietly();
            $msg = $left <= 0 ? "Wrong password ($max of $max), account locked" : "Wrong password ({$user->failed_attempts} of $max)";
            $this->audit->log('session', 'LOGIN_FAILED', $user->id, $user->username, 'password', null, $msg, $user, force: true);

            return back()->withInput($request->only('username'))->withErrors(['username' => $left <= 0
                ? 'Wrong password. The account is now locked. Ask the Super Admin to unlock it.'
                : "Username or password is incorrect. $left attempt".($left === 1 ? '' : 's').' left before the account is locked.']);
        }

        $previous = $user->last_login_at;
        $user->forceFill(['failed_attempts' => 0, 'last_login_at' => now(), 'last_login_ip' => $request->ip()])->saveQuietly();
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $this->audit->log('session', 'LOGIN', $user->id, $user->username, null, null, 'Signed in', $user, force: true);

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Welcome, '.$user->name)
            ->with('sub', $user->role->name.' · previous sign-in '.($previous ? $previous->format('d M Y, H:i') : 'never'));
    }

    public function logout(Request $request)
    {
        $u = $request->user();
        $this->audit->log('session', 'LOGOUT', $u->id, $u->username, null, null, 'Signed out', force: true);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have signed out.');
    }
}
