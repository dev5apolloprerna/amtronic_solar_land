{{-- Heading of a parcel tab: table name, title, description, export + add buttons, then the search bar. --}}
@php($module = config("solar.land_tabs.$tab.1"))
<div class="tabhead">
  <div><span class="tbl-name">{{ $table }}</span><h2>{{ config("solar.land_tabs.$tab.0") }}</h2><p>{{ $desc }}</p></div>
  <div class="actions">
    @if (can_do($module, 'export') && $total)
      <a class="btn" href="{{ route('lands.export', [$land, $tab, 'format' => 'xlsx'] + request()->only('q', 'lease', 'status')) }}">Export Excel</a>
      <a class="btn" href="{{ route('lands.export', [$land, $tab, 'format' => 'pdf'] + request()->only('q', 'lease', 'status')) }}">Export PDF</a>
    @endif
    @if (($addUrl ?? null) && can_do($module, 'add'))<button class="btn pri" type="button" data-drawer="{{ $addUrl }}">{{ $addLabel }}</button>@endif
  </div>
</div>
