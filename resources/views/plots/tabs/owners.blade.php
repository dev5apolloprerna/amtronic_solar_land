{{-- Landowners & shares: versioned ownership with 100% validation and rent allocation preview --}}
@php($draft = $versions->firstWhere('status', 'Draft'))
<div class="tabhead">
  <div><span class="tbl-name">PLOT_OWNERSHIP_VERSIONS · PLOT_OWNERSHIP_SHARES</span><h2>Landowners &amp; shares</h2>
    <p>Add every owner named on the 7/12 extract with their rent share. A draft may total less than 100% but never more. Finalising needs exactly 100% and keeps the previous version as history.</p></div>
  <div class="actions">
    @if (! $draft && can_do('ownership', 'add'))
      <button class="btn pri" type="button" data-confirm-url="{{ route('ownership.draft', $plot) }}" data-title="{{ $versions->isEmpty() ? 'Start ownership' : 'Change ownership' }}"
        data-message="{{ $versions->isEmpty() ? 'Creates a draft where you add landowners and their shares.' : 'Creates a new draft version copied from the current owners. The current version stays in force until the draft is finalised.' }}"
        data-ok="Create draft">{{ $versions->isEmpty() ? 'Add landowners' : 'Change ownership' }}</button>
    @endif
  </div>
</div>

@if ($versions->count())
  <div class="ver-list">
    @foreach ($versions as $v)
      <a class="{{ $selected && $selected->id === $v->id ? 'on' : '' }}" href="{{ route('plots.show', [$plot, 'tab' => 'owners', 'ver' => $v->id]) }}">v{{ $v->version_no }} · {{ $v->status }}{{ $v->effective_from ? ' from '.fmt_date($v->effective_from) : '' }}</a>
    @endforeach
  </div>
@endif

@if ($selected)
  @php($sum = (float) $selected->shares->sum('share_percent'))
  @php($valid = abs($sum - 100) < 0.0001)
  <section class="panel">
    <h3><span>Version {{ $selected->version_no }} {{ chip($selected->status) }}
      <span class="sub" style="font-weight:400">{{ $selected->effective_from ? 'Effective '.fmt_date($selected->effective_from).($selected->effective_to ? ' to '.fmt_date($selected->effective_to) : ' onwards') : 'Not yet in force' }}{{ $selected->change_reason ? ' · '.$selected->change_reason : '' }}{{ $selected->finaliser ? ' · finalised by '.$selected->finaliser->username : '' }}</span></span>
      <span class="actions">
        @if ($selected->isDraft())
          @if (can_do('ownership', 'add'))<button class="btn sm" type="button" data-drawer="{{ route('ownership.shares.create', $selected) }}">Add landowner</button>@endif
          @if (can_do('ownership', 'edit'))<button class="btn sm pri" type="button" data-drawer="{{ route('ownership.finalise.form', $selected) }}" @disabled(! $valid)>Finalise</button>@endif
          @if (can_do('ownership', 'delete'))<button class="lnk red" type="button" data-delete-url="{{ route('ownership.discard', $selected) }}" data-label="draft v{{ $selected->version_no }}" data-message="Discards this draft and its shares. Finalised versions are not affected.">Discard draft</button>@endif
        @endif
      </span>
    </h3>
    <div class="pb">
      <div class="tiles">
        <div class="tile"><small>Owners</small><b>{{ $selected->shares->count() }}</b></div>
        <div class="tile"><small>Total share</small><b style="color:{{ $valid ? 'var(--ok)' : 'var(--bad)' }}">{{ fmt_num($sum, 4) }}%</b><span>{{ $valid ? 'Allocation equals 100%' : fmt_num(100 - $sum, 4).'% left to allocate' }}</span></div>
      </div>
      @if ($selected->isDraft() && ! $valid)
        <div class="err">Owner shares must add up to exactly 100% before this version can be finalised. Currently {{ fmt_num($sum, 4) }}%.</div>
      @endif
    </div>
    <div class="tbl-wrap"><table>
      <thead><tr><th>Landowner</th><th>PAN</th><th>Bank</th><th class="num">Share %</th><th></th></tr></thead>
      <tbody>
      @forelse ($selected->shares->sortByDesc('share_percent') as $s)
        <tr>
          <td><b>{{ $s->party->party_name }}</b><span class="sub mono">{{ $s->party->party_code }} · {{ $s->party->mobile }}</span></td>
          <td><span class="mono">{{ $s->party->pan }}</span></td>
          <td><span class="mono">{{ $s->party->maskedBankAccount() }}</span><span class="sub">{{ $s->party->bank_name_branch }}</span></td>
          <td class="num"><b>{{ fmt_num($s->share_percent, 4) }}%</b></td>
          <td class="act">
            @if ($selected->isDraft())
              @if (can_do('ownership', 'edit'))<button class="lnk" type="button" data-drawer="{{ route('ownership.shares.edit', $s) }}">Edit</button>@endif
              @if (can_do('ownership', 'delete'))<button class="lnk red" type="button" data-delete-url="{{ route('ownership.shares.destroy', $s) }}" data-label="{{ $s->party->party_name }}">Remove</button>@endif
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="empty">No landowners in this version yet. Use “Add landowner”.</td></tr>
      @endforelse
      <tr class="tot"><td>Total</td><td></td><td></td><td class="num">{{ fmt_num($sum, 4) }}%</td><td></td></tr>
      </tbody>
    </table></div>
  </section>
@else
  <div class="note-b">No landowners recorded for this survey number yet. Use “Add landowners” to start.</div>
@endif

@if ($preview)
  <section class="panel">
    <h3>Landowner-wise rent allocation · {{ $preview->lease->lease_no ?? '' }} <span class="sub">Instalment {{ $preview->installment_no }} due {{ fmt_date($preview->due_date) }}</span></h3>
    <div class="pb">
      <div class="tiles">
        <div class="tile"><small>Rent / instalment</small><b>{{ inr($preview->rent_amount) }}</b></div>
        <div class="tile"><small>TDS</small><b>{{ inr($preview->tds_amount) }}</b></div>
        <div class="tile"><small>Net payable</small><b>{{ inr($preview->net_payable) }}</b></div>
      </div>
      @if ($preview->allocations->isEmpty())<div class="err">No finalised ownership is in force on {{ fmt_date($preview->due_date) }}, so this instalment cannot be split or paid yet.</div>@endif
    </div>
    @if ($preview->allocations->count())
      <div class="tbl-wrap"><table>
        <thead><tr><th>Landowner</th><th class="num">Share</th><th class="num">Gross rent</th><th class="num">TDS</th><th class="num">Net payable</th><th class="num">Paid</th></tr></thead>
        <tbody>
          @foreach ($preview->allocations->sortByDesc('share_percent') as $a)
            <tr><td>{{ $a->party->party_name }}</td><td class="num">{{ fmt_num($a->share_percent, 4) }}%</td><td class="num">{{ inr($a->gross_amount, 2) }}</td><td class="num">{{ inr($a->tds_amount, 2) }}</td><td class="num"><b>{{ inr($a->net_amount, 2) }}</b></td><td class="num">{{ inr($a->paid_amount, 2) }}</td></tr>
          @endforeach
          <tr class="tot"><td>Total</td><td class="num">{{ fmt_num($preview->allocations->sum('share_percent'), 4) }}%</td><td class="num">{{ inr($preview->allocations->sum('gross_amount'), 2) }}</td><td class="num">{{ inr($preview->allocations->sum('tds_amount'), 2) }}</td><td class="num">{{ inr($preview->allocations->sum('net_amount'), 2) }}</td><td class="num">{{ inr($preview->allocations->sum('paid_amount'), 2) }}</td></tr>
        </tbody>
      </table></div>
    @endif
    <div class="pb"><span class="hint">The last owner in the list receives the rounding remainder so the split adds up exactly. Payments are recorded in the Payments tab.</span></div>
  </section>
@endif
