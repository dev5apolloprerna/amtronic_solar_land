{{-- Left module rail. $navCounts comes from App\View\Composers\NavComposer --}}
@php
  $groups = [
    'Overview' => [['dashboard', 'Dashboard', 'dashboard', null]],
    'Land' => [['plot', 'Plots / Survey Nos.', 'plots.index', 'plot']],
    'Leasing' => [
      ['lease', 'Leases', 'leases.index', 'lease'],
      ['schedule', 'Rent Schedule', 'schedules.index', 'schedule'],
      ['payment', 'Payment Register', 'payments.index', 'payment'],
      ['sublease', 'Sub-Leases', 'subleases.index', 'sublease'],
    ],
    'Records' => [
      ['compliance', 'Compliance Tracker', 'compliance.index', 'compliance'],
      ['document', 'Documents', 'documents.index', 'document'],
    ],
    'Masters' => [
      ['party', 'Party Master', 'parties.index', 'party'],
      ['park', 'Park Master', 'parks.index', 'park'],
      ['spv', 'SPV Master', 'spvs.index', 'spv'],
      ['state', 'States', 'states.index', 'state'],
      ['city', 'Cities', 'cities.index', 'city'],
      ['taluka', 'Talukas', 'talukas.index', 'taluka'],
    ],
    'Reports' => [['report', 'Reports', 'reports.index', null]],
    'Administration' => [
      ['user', 'Users & Roles', 'users.index', null],
      ['audit', 'Audit Trail', 'audit.index', null],
      ['setting', 'Settings', 'settings.index', null],
    ],
  ];
  $u = auth()->user();
@endphp
<aside class="nav" id="nav">
  <div class="brand"><div class="logo">LOGO</div><div><b>Solar Land Lease</b><small>Land &amp; capacity management</small></div></div>
  @foreach ($groups as $group => $items)
    @php($visible = array_filter($items, fn ($i) => $i[0] === 'dashboard' || ($i[0] === 'user' ? $u->isSuper() : $u->hasPermission($i[0], 'view'))))
    @if (count($visible))
      <h6>{{ $group }}</h6>
      @foreach ($visible as [$key, $label, $route, $countKey])
        <a class="nav-link {{ request()->routeIs(\Illuminate\Support\Str::before($route, '.').'*') ? 'on' : '' }}" href="{{ route($route) }}">
          <span class="lbl">{{ $label }}</span>
          <span class="cnt">{{ $countKey ? ($navCounts[$countKey] ?? '') : '' }}</span>
        </a>
      @endforeach
    @endif
  @endforeach
</aside>
