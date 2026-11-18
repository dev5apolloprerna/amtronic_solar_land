<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Spv;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Users & roles (Super Admin only). */
class UserController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'users');
        $roles = Role::with('permissions')->withCount('users')->orderByDesc('is_super')->orderBy('name')->get();
        $data = ['tab' => $tab, 'roles' => $roles];

        if ($tab === 'roles') {
            $data['role'] = $roles->firstWhere('id', $request->integer('role')) ?? $roles->firstWhere('is_super', false) ?? $roles->first();
        } else {
            $data['rows'] = User::with(['role', 'spvs'])
                ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('username', 'like', "%$s%")->orWhere('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")))
                ->when($request->role_id, fn ($q, $v) => $q->where('role_id', $v))
                ->orderBy('username')->paginate(config('solar.per_page'))->withQueryString();
            $data['total'] = User::count();
        }

        return view('users.index', $data);
    }

    public function create()
    {
        return $this->drawer('Add user', 'USERS', 'users.form', $this->formData(new User(['is_active' => true])), module: 'user');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $user = User::create(collect($data)->except('spv_ids')->all());
        $this->syncSpvs($user, $data['spv_ids'] ?? []);

        return $this->ok("User {$user->username} created");
    }

    public function edit(User $user)
    {
        return $this->drawer('Edit user', 'USERS · '.$user->username, 'users.form', $this->formData($user), module: 'user');
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
        if ($user->id === auth()->id() && ! Role::find($data['role_id'])?->is_super) {
            return $this->refuse('You cannot remove your own Super Admin role.');
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        if (! $data['is_locked'] && $user->is_locked) {
            $data['failed_attempts'] = 0; // unlocking resets the counter
        }
        $user->update(collect($data)->except('spv_ids')->all());
        $this->syncSpvs($user, $data['spv_ids'] ?? []);

        return $this->ok("User {$user->username} saved");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return $this->refuse('You cannot delete the account you are signed in with.');
        }
        $user->delete();

        return $this->ok("User {$user->username} deleted");
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'username' => [$user ? 'prohibited' : 'required', 'regex:/^[a-z0-9][a-z0-9._-]*$/', 'max:60', Rule::unique('users', 'username')],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user?->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'spv_ids' => ['nullable', 'array'],
            'spv_ids.*' => ['integer', 'exists:spvs,id'],
            'is_active' => ['required', 'boolean'],
            'is_locked' => ['nullable', 'boolean'],
        ], ['username.regex' => 'Username may contain lower-case letters, digits, dots, dashes and underscores.']);
        $data['is_locked'] = (bool) ($data['is_locked'] ?? false);

        return $data;
    }

    private function syncSpvs(User $user, array $ids): void
    {
        $before = $user->spvs()->pluck('spv_code')->sort()->implode(', ');
        $user->spvs()->sync($ids);
        $after = Spv::whereIn('id', $ids)->pluck('spv_code')->sort()->implode(', ');
        if ($before !== $after) {
            app(AuditService::class)->log('user', 'UPDATE', $user->id, $user->username, 'spv_access', $before, $after);
        }
    }

    private function formData(User $user): array
    {
        return [
            'user' => $user,
            'roleOpts' => Role::orderBy('name')->pluck('name', 'id')->all(),
            'spvOpts' => Spv::orderBy('spv_code')->get()->mapWithKeys(fn ($s) => [$s->id => "$s->spv_code · $s->spv_name"])->all(),
            'spvIds' => $user->exists ? $user->spvs()->pluck('spvs.id')->all() : [],
        ];
    }
}
