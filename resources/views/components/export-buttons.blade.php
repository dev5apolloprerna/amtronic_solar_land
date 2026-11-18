{{-- Excel / PDF export buttons keeping the current filters --}}
@props(['route', 'module', 'params' => []])
@if (can_do($module, 'export'))
  <a class="btn" href="{{ route($route, array_merge(request()->query(), $params, ['format' => 'xlsx'])) }}">Export Excel</a>
  <a class="btn" href="{{ route($route, array_merge(request()->query(), $params, ['format' => 'pdf'])) }}">Export PDF</a>
@endif
