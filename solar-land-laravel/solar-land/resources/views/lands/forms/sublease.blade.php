@php($t = fn ($type) => $readonly ? 'ro' : $type)
@include('partials.form-open', ['action' => $s->exists ? route('subleases.update', $s) : route('subleases.store', $land), 'model' => $s])
  <fieldset data-sublease-form data-leases='@json($leaseInfo)' data-exclude="{{ $s->id }}">
    @if ($s->exists || $readonly)
      <x-field name="lease_ro" label="Parent lease" type="ro" :value="$leaseOpts[$s->lease_id] ?? ''" span :lock="! $readonly" />
      <input type="hidden" data-lease-id value="{{ $s->lease_id }}">
    @else
      <x-field name="lease_id" label="Parent lease" type="select" :options="$leaseOpts" :value="$s->lease_id" req span :blank="false" data-lease-id />
    @endif
    @unless ($readonly)<div class="fld span2" data-cap-panel></div>@endunless
    <x-field name="land_ro" label="Land ref no." type="ro" :value="$land->land_ref_no" />
    <x-field name="status" label="Status" :type="$t('select')" :options="opts('sublease_status')" :value="$s->status" :blank="false" />
    <x-field name="sublessee_name" label="Sublessee name" :type="$t('text')" :value="$s->sublessee_name" req />
    <x-field name="sublessee_pan" label="Sublessee PAN" :type="$t('text')" :value="$s->sublessee_pan" maxlength="10" style="text-transform:uppercase" />
    <x-field name="sublease_area_acres" label="Sublease area (acres)" :type="$t('number')" :value="$s->sublease_area_acres !== null ? (float) $s->sublease_area_acres : null" req />
    <x-field name="capacity_mw" label="Sublease capacity (MW)" :type="$t('number')" :value="$s->capacity_mw !== null ? (float) $s->capacity_mw : null" req
      :hint="$readonly ? null : 'Active subleases running on the same day cannot exceed the lease MW.'" />
    <x-field name="sublease_rent" label="Sublease rent (₹ / year)" :type="$t('number')" :value="$s->sublease_rent !== null ? (float) $s->sublease_rent : null">{{ $readonly ? inr($s->sublease_rent) : '' }}</x-field>
    <x-field name="agreement_no" label="Agreement no." :type="$t('text')" :value="$s->agreement_no" />
    <x-field name="sublease_start" label="Sublease start" :type="$t('date')" :value="$s->sublease_start" req>{{ $readonly ? fmt_date($s->sublease_start) : '' }}</x-field>
    <x-field name="sublease_end" label="Sublease end" :type="$t('date')" :value="$s->sublease_end" req>{{ $readonly ? fmt_date($s->sublease_end) : '' }}</x-field>
    <x-field name="lender_noc_obtained" label="Lender NOC obtained" :type="$t('yn')" :value="$s->lender_noc_obtained" :blank="false"
      :hint="! $readonly && $land->compliance?->mortgage_created === 'Yes' ? 'Parcel is mortgaged to '.$land->compliance->lender_name.'. An active sublease needs the lender’s NOC.' : null" />
  </fieldset>
  @if ($s->exists && ! $readonly)<p class="hint">Changes to each field are recorded in the audit trail with the old and new value.</p>@endif
</form>
