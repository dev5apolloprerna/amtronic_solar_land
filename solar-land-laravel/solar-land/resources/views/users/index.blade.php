@extends('layouts.app')
@section('title', 'Users & Roles')
@push('styles')<link rel="stylesheet" href="{{ asset('css/modules/users.css') }}">@endpush
@push('scripts')<script src="{{ asset('js/modules/users.js') }}"></script>@endpush
@section('content')
  <x-page-head title="Users & roles" table="USER_MASTER · ROLE_SPV_MAPPING" desc="Only the Super Admin sees this page. Add users, give each a role and SPV access, and decide per role who can view, add, edit, delete and export in every module.">
    @if ($tab === 'users')<button class="btn pri" type="button" data-drawer="{{ route('users.create') }}">Add user</button>
    @else<button class="btn" type="button" data-drawer="{{ route('roles.create') }}">Add role</button>@endif
  </x-page-head>
  <div class="tabs">
    <a class="{{ $tab === 'users' ? 'on' : '' }}" href="{{ route('users.index') }}" style="text-decoration:none;padding:9px 14px;border-bottom:2px solid {{ $tab === 'users' ? 'var(--accent)' : 'transparent' }};margin-bottom:-1px;color:{{ $tab === 'users' ? 'var(--ink)' : 'var(--muted)' }};font-weight:600">Users</a>
    <a class="{{ $tab === 'roles' ? 'on' : '' }}" href="{{ route('users.index', ['tab' => 'roles']) }}" style="text-decoration:none;padding:9px 14px;border-bottom:2px solid {{ $tab === 'roles' ? 'var(--accent)' : 'transparent' }};margin-bottom:-1px;color:{{ $tab === 'roles' ? 'var(--ink)' : 'var(--muted)' }};font-weight:600">Roles &amp; permissions ({{ $roles->count() }})</a>
  </div>

  @if ($tab === 'users')
    <x-filter-bar placeholder="Search username, name or email…" :total="$total" :shown="$rows->total()">
      <input type="hidden" name="tab" value="users">
      <x-filter-select name="role_id" label="All roles" :options="$roles->pluck('name', 'id')->all()" />
    </x-filter-bar>
    <div class="tbl-wrap"><table>
      <thead><tr><th>Username</th><th>Name</th><th>Role</th><th>SPV access</th><th>Active</th><th>Last login</th><th></th></tr></thead>
      <tbody>
      @foreach ($rows as $u)
        <tr>
          <td><span class="ref">{{ $u->username }}</span></td>
          <td>{{ $u->name }}<span class="sub">{{ $u->email }}</span></td>
          <td>{{ chip($u->role->name, 'info') }}</td>
          <td>{{ $u->isSuper() ? 'All SPVs' : ($u->spvs->pluck('spv_code')->implode(', ') ?: 'None') }}</td>
          <td>{{ chip($u->is_active ? 'Yes' : 'No') }} @if ($u->is_locked){{ chip('Locked', 'bad') }}@endif</td>
          <td>{{ $u->last_login_at ? fmt_ts($u->last_login_at) : 'Never' }}<span class="sub mono">{{ $u->last_login_ip }}</span></td>
          <td class="act">
            <button class="lnk" type="button" data-history="{{ route('history', ['user', $u->id]) }}">History</button>
            <button class="lnk" type="button" data-drawer="{{ route('users.edit', $u) }}">Edit</button>
            @if ($u->id !== auth()->id())<button class="lnk red" type="button" data-delete-url="{{ route('users.destroy', $u) }}" data-label="{{ $u->username }}">Delete</button>@endif
          </td>
        </tr>
      @endforeach
      </tbody>
    </table></div>
    <x-pager :p="$rows" />
  @else
    <div class="role-pick">
      @foreach ($roles as $r)
        <a class="{{ $r->id === $role->id ? 'on' : '' }}" href="{{ route('users.index', ['tab' => 'roles', 'role' => $r->id]) }}">{{ $r->name }} · {{ $r->users_count }}</a>
      @endforeach
    </div>
    @php($m = $role->matrix())
    <section class="panel">
      <h3><span>{{ $role->name }}<span class="sub" style="font-weight:400">{{ $role->description }}</span></span>
        <span class="actions">@unless ($role->is_super)
          <button class="lnk" type="button" data-drawer="{{ route('roles.edit', $role) }}">Rename</button>
          <button class="lnk red" type="button" data-delete-url="{{ route('roles.destroy', $role) }}" data-label="role {{ $role->name }}">Delete role</button>
        @endunless</span></h3>
      @if ($role->is_super)<div class="pb"><div class="note-b">Super Admin has every permission in every module and sees all SPVs. These cannot be changed.</div></div>@endif
      <form action="{{ route('roles.permissions', $role) }}" method="POST" data-ajax id="permForm">
        @csrf @method('PUT')
        <div class="tbl-wrap"><table class="mx">
          <thead><tr><th>Module</th>@foreach (config('solar.actions') as $a)<th>{{ ucfirst($a) }}</th>@endforeach</tr></thead>
          <tbody>
          @foreach (config('solar.modules') as $mod => $meta)
            <tr data-mod="{{ $mod }}"><td>{{ $meta['label'] }}<span class="sub mono">{{ strtoupper($meta['table']) }}</span></td>
              @foreach (config('solar.actions') as $a)
                @if (in_array($a, $meta['na'], true))<td><span class="na">n/a</span></td>
                @else<td><input type="checkbox" name="perm[{{ $mod }}][{{ $a }}]" value="1" data-act="{{ $a }}" aria-label="{{ $mod }} {{ $a }}" @checked($m[$mod][$a]) @disabled($role->is_super)></td>@endif
              @endforeach
            </tr>
          @endforeach
          </tbody>
        </table></div>
        @unless ($role->is_super)
          <div class="pb" style="flex-direction:row;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;border-top:1px solid var(--line-2)">
            <span class="hint" id="permHint">Turning off View hides the module, or the tab inside a parcel, for this role. Each change is logged to the audit trail.</span>
            <span class="actions"><a class="btn" href="{{ route('users.index', ['tab' => 'roles', 'role' => $role->id]) }}">Discard</a><button class="btn pri" type="submit">Save permissions</button></span>
          </div>
        @endunless
      </form>
    </section>
  @endif
@endsection
