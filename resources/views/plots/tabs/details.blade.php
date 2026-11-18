<section class="inline-card" id="inlineHost">
  <div class="inline-bar"><h2>{{ $editing ? 'Edit survey details' : 'Survey details' }}</h2>
    <div class="actions">
      @unless ($editing)
        <button class="lnk" type="button" data-history="{{ route('history', ['plot', $plot->id]) }}">History</button>
        @if (can_do('plot', 'edit'))<a class="btn" href="{{ route('plots.show', [$plot, 'edit' => 1]) }}">Edit survey details</a>@endif
      @endunless
    </div>
  </div>
  @if ($editing)
    <form id="plotForm" action="{{ route('plots.update', $plot) }}" method="POST" data-ajax novalidate>
      @csrf @method('PUT')
      <div id="inlineBody">@include('plots._form')</div>
      <div class="inline-foot"><span class="hint" style="flex:1;align-self:center">Each changed field is recorded in the audit trail. The Land Reference Number does not change.</span>
        <a class="btn" href="{{ route('plots.show', $plot) }}">Cancel</a><button class="btn pri" type="submit">Save changes</button></div>
    </form>
  @else
    <dl class="kv">
      <div><dt>Village</dt><dd>{{ $plot->village }}</dd></div>
      <div><dt>Survey / sub survey</dt><dd>{{ $plot->survey_no }} / {{ $plot->sub_survey_no ?: '—' }}</dd></div>
      <div><dt>Total area</dt><dd>{{ fmt_num($plot->total_area, 4) }} {{ $plot->land_unit }} ({{ fmt_num($plot->area_acres, 4) }} ac)</dd></div>
      <div><dt>Area under lease</dt><dd>{{ fmt_num($plot->area_under_lease_acres, 4) }} ac</dd></div>
      <div><dt>DLR area</dt><dd>{{ $plot->dlr_area_sqm ? fmt_num($plot->dlr_area_sqm).' sq.m' : '—' }}</dd></div>
      <div><dt>Rate per acre</dt><dd>{{ inr($plot->rate_per_acre) }} / year</dd></div>
      <div><dt>Increment</dt><dd>+{{ fmt_num($plot->increment_pct) }}% every {{ $plot->increment_after_yrs }} yrs</dd></div>
      <div><dt>Title report</dt><dd>{{ chip($plot->title_report) }}</dd></div>
      <div><dt>Title DD</dt><dd>{{ chip($plot->title_dd_status) }}</dd></div>
      <div><dt>Lessor capacity verified</dt><dd>{{ chip($plot->lessor_capacity_verified) }}</dd></div>
      <div><dt>Encumbrance</dt><dd>{{ chip($plot->encumbrance_status) }}</dd></div>
      <div><dt>NA application / status</dt><dd>{{ $plot->na_application }} · {{ chip($plot->na_status) }}</dd></div>
      <div><dt>Lien (Boja)</dt><dd>{{ $plot->lien_details ?: '—' }}</dd></div>
      <div><dt>Created</dt><dd>{{ fmt_ts($plot->created_at) }}</dd></div>
    </dl>
  @endif
</section>
