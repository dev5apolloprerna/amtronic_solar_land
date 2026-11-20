@include('partials.form-open', ['action' => $user->exists ? route('users.update', $user) : route('users.store'), 'model' => $user])
  <fieldset>
    <x-field name="username" label="Username" :value="$user->username" req :lock="$user->exists" />
    <x-field name="name" label="Full name" :value="$user->name" req />
    <x-field name="email" label="Email" type="email" :value="$user->email" req />
    <x-field name="role_id" label="Role" type="select" :options="$roleOpts" :value="$user->role_id" req hint="The role decides which modules and actions this user gets." />
    <x-field name="password" label="{{ $user->exists ? 'New password (leave blank to keep)' : 'Password' }}" type="password" :req="! $user->exists" autocomplete="new-password" hint="At least 8 characters with upper and lower case letters and a number." />
    <x-field name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" />
    <x-field name="spv_ids" label="SPV access" type="checks" :options="$spvOpts" :value="$spvIds" span hint="Users only see land, lease and payment records for these SPVs. Super Admin sees all." />
    <x-field name="is_active" label="Active" type="select" :options="['1' => 'Yes', '0' => 'No']" :value="$user->is_active ? '1' : '0'" req :blank="false" hint="Inactive users cannot sign in." />
    <x-field name="is_locked" label="Account locked" type="select" :options="['0' => 'No', '1' => 'Yes']" :value="$user->is_locked ? '1' : '0'" :blank="false" :hint="'Locks after '.config('solar.max_login_attempts').' wrong passwords. Set to No to unlock.'" />
    @if ($user->exists)<x-field name="ll_ro" label="Last login" type="ro" :value="$user->last_login_at ? fmt_ts($user->last_login_at).' from '.$user->last_login_ip : 'Never'" />@endif
  </fieldset>
</form>
