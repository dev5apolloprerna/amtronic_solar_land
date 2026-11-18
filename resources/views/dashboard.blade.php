@extends('layouts.app')
@section('title', 'Dashboard')
@push('styles')<link rel="stylesheet" href="{{ asset('css/modules/dashboard.css') }}">@endpush
@section('content')
  <div class="ph"><div><h1>Solar land portfolio</h1><p>As of {{ now()->format('d M Y') }}. Figures cover the SPVs you have access to. Click a Land Reference Number to open that survey number.</p></div></div>

  <div class="tiles">
    <div class="tile"><small>Solar parks</small><b>{{ $m['parks'] }}</b><span>{{ $m['plots'] }} survey numbers</span></div>
    <div class="tile"><small>Total land area</small><b>{{ fmt_num($m['land_acres']) }}<span style="font-size:14px"> ac</span></b><span>{{ fmt_num($m['survey_acres']) }} ac in survey records</span></div>
    <div class="tile"><small>Installed capacity</small><b>{{ fmt_num($m['installed_mw'], 3) }}<span style="font-size:14px"> MW</span></b><span>{{ fmt_mw($m['unleased_mw']) }} not leased</span></div>
    <div class="tile"><small>Leased capacity</small><b>{{ fmt_num($m['leased_mw'], 3) }}<span style="font-size:14px"> MW</span></b><span>{{ $m['active_leases'] }} active leases</span></div>
    <div class="tile"><small>Sub-leased capacity</small><b>{{ fmt_num($m['sub_mw'], 3) }}<span style="font-size:14px"> MW</span></b><span>{{ $m['active_subleases'] }} active sub-leases</span></div>
    <div class="tile"><small>Available capacity</small><b>{{ fmt_num($m['available_mw'], 3) }}<span style="font-size:14px"> MW</span></b><span>leased minus sub-leased, today</span></div>
    <div class="tile"><small>Landowners</small><b>{{ $m['landowners'] }}</b><span>with current finalised shares</span></div>
    <div class="tile"><small>Active leases</small><b>{{ $m['active_leases'] }}</b><span>{{ $m['expired_leases'] }} expired</span></div>
    <div class="tile {{ $m['expired_leases'] ? 'warn' : '' }}"><small>Expired leases</small><b>{{ $m['expired_leases'] }}</b></div>
    <div class="tile"><small>Active sub-leases</small><b>{{ $m['active_subleases'] }}</b></div>
    <div class="tile {{ $m['pending_count'] ? 'bad' : '' }}"><small>Pending rent payments</small><b>{{ $m['pending_count'] }}</b><span>{{ inr($m['pending_amount']) }} due and unpaid</span></div>
  </div>

  <div class="grid2">
    <section class="panel"><h3>Rent due in the next 90 days and overdue @if (can_do('schedule'))<a class="lnk" href="{{ route('schedules.index', ['status' => 'Overdue']) }}">All overdue</a>@endif</h3>
      <div class="tbl-wrap"><table>
        <thead><tr><th>Land ref no.</th><th>Due date</th><th class="num">Balance</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse ($due as $s)
          <tr>
            <td><a class="reflink" href="{{ route('plots.show', [$s->plot_id, 'tab' => 'schedule']) }}">{{ $s->plot->land_ref_no }}</a><span class="sub">{{ $s->auditLabel() }} · {{ $s->lease->lease_no }}</span></td>
            <td>{{ fmt_date($s->due_date) }}</td>
            <td class="num">{{ inr($s->remaining(), 2) }}</td>
            <td>{{ chip($s->displayStatus()) }}</td>
            <td class="act">@if ($s->allocations->isNotEmpty() && can_do('payment', 'add'))<button class="lnk" type="button" data-drawer="{{ route('payments.create', ['schedule_id' => $s->id]) }}">Record payment</button>@endif</td>
          </tr>
        @empty
          <tr><td colspan="5" class="empty">Nothing due in the next 90 days.</td></tr>
        @endforelse
        </tbody>
      </table></div>
    </section>

    <section class="panel"><h3>Capacity by park</h3><div class="pb">
      @forelse ($parks as $p)
        @php($t = $p['installed'])
        <div class="util">
          <div class="util-h"><span><b>{{ $p['park']->park_code }}</b> · {{ $p['park']->park_name }}</span><span>{{ fmt_num($p['leased'], 3) }} leased · {{ fmt_num($p['sub'], 3) }} sub-leased / {{ fmt_mw($t) }}</span></div>
          <div class="bar" role="img" aria-label="{{ $p['park']->park_code }}: {{ fmt_mw($p['leased']) }} leased of {{ fmt_mw($t) }}">
            <span class="seg used" style="width:{{ $t > 0 ? min(100, $p['sub'] / $t * 100) : 0 }}%"></span>
            <span class="seg cur" style="width:{{ $t > 0 ? max(0, min(100, ($p['leased'] - $p['sub']) / $t * 100)) : 0 }}%"></span>
          </div>
        </div>
      @empty
        <p class="hint">No parks yet.</p>
      @endforelse
      <span class="hint">Grey = sub-leased today, blue = leased but not sub-leased, empty = installed but not leased.</span>
    </div></section>
  </div>

  <section class="panel"><h3>Sub-lease use by lease (top 10 by MW)</h3><div class="pb">
    @forelse ($leaseUse as $x)
      <div class="util">
        <div class="util-h"><a class="reflink" href="{{ route('plots.show', [$x['lease']->plot_id, 'tab' => 'subleases']) }}">{{ $x['lease']->plot->land_ref_no }}</a>
          <span>{{ $x['lease']->lease_no }} · {{ fmt_num($x['used'], 3) }} / {{ fmt_mw($x['total']) }} · {{ fmt_mw($x['total'] - $x['used']) }} remaining</span></div>
        <div class="bar"><span class="seg cur" style="width:{{ $x['total'] > 0 ? min(100, $x['used'] / $x['total'] * 100) : 0 }}%"></span></div>
      </div>
    @empty
      <p class="hint">No active leases.</p>
    @endforelse
  </div></section>

  @if ($recent->count())
    <section class="panel"><h3>Recent changes <a class="lnk" href="{{ route('audit.index') }}">Open audit trail</a></h3>@include('audit._table', ['rows' => $recent])</section>
  @endif
@endsection
