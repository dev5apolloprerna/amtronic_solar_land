{{-- Land Master tab: inline card. View mode shows read-only values; edit mode (?edit=1 or new parcel) shows the form. --}}
@php
  $m = $form;
  $isNew = ! $land;
  $ro = ! $editing;
  $t = fn ($type) => $ro ? 'ro' : $type;
@endphp
<section class="inline-card" id="inlineHost">
  <div class="inline-bar">
    <h2>{{ $isNew ? 'Land details' : ($editing ? 'Edit land details' : 'Land details') }}</h2>
    <div class="actions">
      @if ($land && ! $editing)
        <button class="lnk" type="button" data-history="{{ route('history', ['land', $land->id]) }}">History</button>
        @if (can_do('land', 'edit'))<a class="btn" href="{{ route('lands.show', [$land, 'edit' => 1]) }}">Edit land details</a>@endif
        @if (can_do('land', 'delete'))<button class="btn danger" type="button" data-delete-url="{{ route('lands.destroy', $land) }}" data-label="{{ $land->land_ref_no }}">Delete</button>@endif
      @endif
    </div>
  </div>
  <form id="landForm" data-ajax action="{{ $isNew ? route('lands.store') : route('lands.update', $land) }}" method="POST" novalidate
        data-ref-url="{{ route('lookup.land-ref') }}" data-park-url="{{ url('lookup/park') }}" data-spv-url="{{ route('lookup.spvs') }}">
    @csrf
    @if (! $isNew) @method('PUT') @endif
    <div id="inlineBody">
      <fieldset><legend>Park &amp; SPV</legend>
        <x-field name="park_id" label="Park (optional)" :type="$t('select')" :options="$parkOpts" :value="$m->park_id" blank="No park (NOP)"
          hint="Optional. Select a park to automatically fill State / District / City. Leave blank for manual location.">{{ $ro ? ($m->park ? $m->park->park_code.' · '.$m->park->park_name : 'No park (NOP)') : '' }}</x-field>
        @include('partials.location-fields', ['m' => $m, 'readonly' => $ro, 'stateOpts' => $stateOpts, 'stateHint' => $ro ? null : 'Select manually when no park is selected.'])
        <x-field name="spv_id" label="SPV" :type="$t('select')" :options="$spvOpts" :value="$m->spv_id" req
          :hint="$ro ? null : 'Select an SPV; if a park is selected, only its SPVs appear.'">{{ $ro ? $m->spv?->spv_code.' · '.$m->spv?->spv_name : '' }}</x-field>
      </fieldset>
      <fieldset><legend>Reference</legend>
        <x-field name="parcel_code" label="Parcel code" :type="$t('text')" :value="$m->parcel_code" req placeholder="A1" :lock="! $isNew && ! $ro" />
        <x-field name="ref_year" label="Reference year" :type="$t('number')" :value="$m->ref_year" req :lock="! $isNew && ! $ro" />
        <div class="fld span2">
          <label>Land reference no.</label>
          <div class="ro" id="landRef">@if ($m->land_ref_no)<span class="ref">{{ $m->land_ref_no }}</span>@else<span class="hint">Fills once SPV, state, parcel and year are set</span>@endif</div>
          @unless ($ro)<small class="hint">Format: SPV-STATE-PARK-PARCEL-YEAR (NOP without park). System-generated and immutable.</small>@endunless
        </div>
      </fieldset>
      <fieldset><legend>Area</legend>
        <x-field name="area_sqm_712" label="Area as per 7/12 (sq.m)" :type="$t('number')" :value="$m->area_sqm_712" req :hint="$ro ? null : 'Total of all survey numbers of this parcel.'" />
        <x-field name="area_acres_712" label="Area as per 7/12 (acres)" type="ro" :value="$m->area_acres_712 !== null ? fmt_num($m->area_acres_712) : null" id="f_area_acres_712" :hint="$ro ? null : 'Calculated: sq.m ÷ 4,046.86'" />
        <x-field name="area_under_lease_acres" label="Actual area under lease (acres)" :type="$t('number')" :value="$m->area_under_lease_acres" req />
        <x-field name="dlr_sqm" label="DLR area (sq.m)" :type="$t('number')" :value="$m->dlr_sqm" />
      </fieldset>
      <fieldset><legend>Rate</legend>
        <x-field name="rate_per_acre" label="Rate per acre (₹ / year)" :type="$t('number')" :value="$m->rate_per_acre" req>{{ $ro ? inr($m->rate_per_acre) : '' }}</x-field>
        <x-field name="increment_after_yrs" label="Increment after (years)" :type="$t('number')" :value="$m->increment_after_yrs" req :hint="$ro ? null : 'The rate goes up once every this many years.'" />
        <x-field name="increment_pct" label="Increment (%)" :type="$t('number')" :value="$m->increment_pct !== null ? (float) $m->increment_pct : null" req id="f_increment_pct"
          :hint="$m->rate_per_acre && $m->increment_after_yrs ? 'From year '.($m->increment_after_yrs + 1).': '.inr(round($m->rate_per_acre * (1 + $m->increment_pct / 100))).' per acre' : null" />
      </fieldset>
      <fieldset><legend>Due diligence</legend>
        <x-field name="title_report" label="Title report received" :type="$t('yn')" :value="$m->title_report" />
        <x-field name="title_dd_status" label="Title DD status" :type="$t('select')" :options="opts('title_dd_status')" :value="$m->title_dd_status" />
        <x-field name="lessor_capacity_verified" label="Lessor capacity verified" :type="$t('yn')" :value="$m->lessor_capacity_verified" />
        <x-field name="encumbrance_status" label="Encumbrance status" :type="$t('select')" :options="opts('encumbrance_status')" :value="$m->encumbrance_status" />
        <x-field name="lien_details" label="Lien (Boja) details" :type="$t('textarea')" :value="$m->lien_details" span />
      </fieldset>
      <fieldset><legend>NA conversion &amp; status</legend>
        <x-field name="na_application" label="NA application filed" :type="$t('yn')" :value="$m->na_application" />
        <x-field name="na_status" label="NA status" :type="$t('select')" :options="opts('na_status')" :value="$m->na_status" />
        <x-field name="land_status" label="Land status" :type="$t('select')" :options="opts('land_status')" :value="$m->land_status" req />
      </fieldset>
    </div>
    @if ($editing)
      <div class="inline-foot">
        <span class="hint" style="flex:1;align-self:center">{{ $land ? 'Each changed field is recorded in the audit trail.' : 'Saving creates the land reference number and writes to the audit trail.' }}</span>
        <a class="btn" href="{{ $land ? route('lands.show', $land) : route('lands.index') }}">Cancel</a>
        <button class="btn pri" type="submit">{{ $land ? 'Save changes' : 'Save land parcel' }}</button>
      </div>
    @endif
  </form>
</section>
