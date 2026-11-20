@extends('layouts.app')
@section('title', 'Dashboard')
@push('styles')<link rel="stylesheet" href="{{ asset('css/modules/dashboard.css') }}">@endpush
@section('content')
  <div class="ph"><div><h1>Land portfolio</h1><p>As of {{ fmt_date(now()) }}. Figures cover the SPVs you have access to. Click a land reference number to open that parcel.</p></div></div>
  <div class="tiles">
    <div class="tile"><small>Land parcels</small><b>{{ $d['parcels'] }}</b><span>{{ fmt_num($d['identified']) }} acres identified</span></div>
    <div class="tile"><small>Leased area</small><b>{{ fmt_num($d['leased']) }}<span style="font-size:14px"> ac</span></b><span>{{ $d['activeLeases'] }} active leases</span></div>
    <div class="tile"><small>Subleased</small><b>{{ $d['leased'] ? round($d['subbed'] / $d['leased'] * 100) : 0 }}%</b><span>{{ fmt_num($d['subbed']) }} of {{ fmt_num($d['leased']) }} acres</span></div>
    <div class="tile {{ $d['overdue']->isNotEmpty() ? 'bad' : '' }}"><small>Overdue rent</small><b>{{ $d['overdue']->count() }}</b><span>{{ inr($d['overdue']->sum('net_payable')) }} net payable</span></div>
    <div class="tile {{ $d['openPdc'] ? 'warn' : '' }}"><small>Open compliance items</small><b>{{ $d['openPdc'] }}</b><span>across {{ $d['pdcParcels'] }} parcels</span></div>
  </div>
  <div class="tiles mw-tiles">
    <div class="tile"><small>Installed capacity</small><b>{{ fmt_num($d['mw']['installed'], 3) }}<span> MW</span></b><span>across all parks</span></div>
    <div class="tile"><small>Leased capacity</small><b>{{ fmt_num($d['mw']['leased'], 3) }}<span> MW</span></b><span>{{ fmt_num(max(0, $d['mw']['unleased']), 3) }} MW of parks not leased</span></div>
    <div class="tile"><small>Subleased today</small><b>{{ fmt_num($d['mw']['sub'], 3) }}<span> MW</span></b><span>active subleases</span></div>
    <div class="tile"><small>Remaining on leases</small><b>{{ fmt_num($d['mw']['free'], 3) }}<span> MW</span></b><span>free to sublease today</span></div>
  </div>
  <div class="grid2">
    <section class="panel"><h3>Rent due in the next 90 days and overdue</h3>
      <div class="tbl-wrap"><table><thead><tr><th>Land ref no.</th><th>Due date</th><th>Net payable</th><th>Status</th><th></th></tr></thead><tbody>
      @forelse ($d['due'] as $s)
        <tr>
          <td><a class="reflink" href="{{ route('lands.show', [$s->land, 'tab' => 'schedule']) }}">{{ $s->land->land_ref_no }}</a><span class="sub">{{ $s->code }} · {{ $s->lease->code }}</span></td>
          <td>{{ fmt_date($s->due_date) }}</td>
          <td class="num">{{ inr($s->net_payable) }}</td>
          <td>{{ chip($s->status()) }}</td>
          <td class="act">@if (can_do('payment', 'add'))<button class="lnk" type="button" data-drawer="{{ route('payments.create', [$s->land, 'schedule_id' => $s->id]) }}">Record payment</button>@endif</td>
        </tr>
      @empty
        <tr><td colspan="5" class="empty">Nothing due in the next 90 days.</td></tr>
      @endforelse
      </tbody></table></div>
    </section>
    <section class="panel"><h3>Sublease use by lease</h3><div class="pb">
      @forelse ($d['use'] as $x)
        <div class="util">
          <div class="util-h"><a class="reflink" href="{{ route('lands.show', [$x['lease']->land, 'tab' => 'sublease']) }}">{{ $x['lease']->land->land_ref_no }}</a><span>{{ fmt_num($x['used']) }} / {{ fmt_num($x['total']) }} ac · {{ fmt_num($x['mwUsed'], 3) }} / {{ fmt_num($x['mw'], 3) }} MW</span></div>
          <div class="bar"><span class="seg cur" style="width:{{ $x['total'] ? min(100, $x['used'] / $x['total'] * 100) : 0 }}%"></span></div>
        </div>
      @empty
        <p class="hint">No active leases.</p>
      @endforelse
    </div></section>
  </div>
  @if ($d['parks']->isNotEmpty())
    <section class="panel"><h3>Park capacity (MW)</h3><div class="pb">
      @foreach ($d['parks'] as $p)
        <div class="util">
          <div class="util-h"><span><b>{{ $p['park']->park_code }}</b> · {{ $p['park']->park_name }}</span><span>{{ fmt_num($p['leased'], 3) }} leased of {{ fmt_num($p['installed'], 3) }} MW · {{ fmt_num(max(0, $p['installed'] - $p['leased']), 3) }} MW free</span></div>
          <div class="bar"><span class="seg cur" style="width:{{ $p['installed'] ? min(100, $p['leased'] / $p['installed'] * 100) : 0 }}%"></span></div>
        </div>
      @endforeach
    </div></section>
  @endif
  @if (can_do('audit'))
    <section class="panel"><h3>Recent changes <a class="lnk" href="{{ route('audit.index') }}">Open audit trail</a></h3>
      @include('audit._table', ['rows' => $recent])
    </section>
  @endif
@endsection
