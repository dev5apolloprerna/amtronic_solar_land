{{-- Left menu, same groups as the template + Reports. $navCounts: App\View\Composers\NavComposer; $landNav: LandController (inside a parcel). --}}
@php
  $groups = [
    'Overview' => [['dashboard', 'Dashboard', 'dashboard', 'dashboard']],
    'Land' => [['land', 'Land Master', 'lands.index', 'lands']],
    'Masters' => [
      ['state', 'State Master', 'states.index', 'states'],
      ['district', 'District Master', 'districts.index', 'districts'],
      ['city', 'City / Taluka Master', 'cities.index', 'cities'],
      ['park', 'Park Master', 'parks.index', 'parks'],
      ['spv', 'SPV Master', 'spvs.index', 'spvs'],
    ],
    'Reports' => [['report', 'Reports', 'reports.index', 'reports']],
    'Administration' => [
      ['user', 'Users & Roles', 'users.index', 'users'],
      ['audit', 'Audit Trail', 'audit.index', 'audit'],
    ],
  ];
  $u = auth()->user();
  $ln = $landNav ?? null;
  $inParcel = request()->routeIs('lands.show', 'lands.create');
@endphp
<aside class="nav" id="nav">
  <div class="brand"><div class="logo">LOGO</div><div><b>Leased Land</b><small>Data management system</small></div></div>
  @foreach ($groups as $group => $items)
    @php($visible = array_filter($items, fn ($i) => $i[0] === 'dashboard' || ($i[0] === 'user' ? $u->isSuper() : $u->hasPermission($i[0], 'view'))))
    @if (count($visible))
      <h6>{{ $group }}</h6>
      @foreach ($visible as [$key, $label, $route, $prefix])
        @php($on = $key === 'dashboard' ? request()->routeIs('dashboard') : (request()->routeIs($prefix.'.*') || ($key === 'user' && request()->routeIs('roles.*'))))
        <a class="nav-link {{ $on && ! ($key === 'land' && $inParcel) ? 'on' : '' }}" href="{{ route($route) }}">
          <span class="lbl">{{ $label }}</span><span class="cnt">{{ $key === 'dashboard' ? '' : ($navCounts[$key] ?? '') }}</span>
        </a>
        @if ($key === 'land' && $inParcel && $ln)
          <div class="subnav">
            <span class="cur-ref">{{ $ln['land']?->land_ref_no ?? 'New parcel (unsaved)' }}</span>
            @foreach ($ln['tabs'] as $t => [$tl])
              <a class="nav-link {{ $ln['tab'] === $t ? 'on' : '' }}" href="{{ $ln['land'] ? route('lands.show', [$ln['land'], 'tab' => $t]) : route('lands.create') }}">
                <span class="lbl">{{ $tl }}</span>@if ($t !== 'land' && $ln['land'])<span class="cnt">{{ $ln['counts'][$t] ?? 0 }}</span>@endif
              </a>
            @endforeach
          </div>
        @endif
      @endforeach
    @endif
  @endforeach
</aside>
