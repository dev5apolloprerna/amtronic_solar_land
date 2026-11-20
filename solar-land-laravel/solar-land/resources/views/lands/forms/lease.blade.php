@php
  $t = fn ($type) => $readonly ? 'ro' : $type;
  $sv = $land->surveys;
  $fr = $land->farmers;
@endphp
@include('partials.form-open', ['action' => $L->exists ? route('leases.update', $L) : route('leases.store', $land), 'model' => $L])
  <div data-lease-form data-sqm="{{ config('solar.sqm_per_acre') }}">
  <fieldset><legend>Land parcel</legend>
    <x-field name="land_ref" label="Land reference no." type="ro" :value="$land->land_ref_no" span :lock="$L->exists && ! $readonly" />
    @unless ($readonly)
      <div class="fld span2"><div class="note-b"><b>{{ $fr->pluck('owner_name')->implode(', ') ?: 'No farmers added yet' }}</b> · Survey {{ $sv->pluck('survey_plot_no')->implode(', ') ?: '—' }} · {{ fmt_num($land->area_under_lease_acres) }} acres under lease · {{ inr($land->rate_per_acre) }} per acre, +{{ fmt_num($land->increment_pct) }}% every {{ $land->increment_after_yrs }} yrs</div></div>
    @endunless
    <x-field name="lessor_name" label="Lessor name" :type="$t('text')" :value="$L->lessor_name" req />
    <x-field name="lessor_pan" label="Lessor PAN" :type="$t('text')" :value="$L->lessor_pan" placeholder="ABCDE1234F" maxlength="10" style="text-transform:uppercase" />
    <x-field name="leased_area_acres" label="Leased area (acres)" :type="$t('number')" :value="$L->leased_area_acres !== null ? (float) $L->leased_area_acres : null" req
      :hint="$readonly ? null : ($L->exists && $areaUsed > 0 ? fmt_num($areaUsed).' ac already subleased; cannot go below that.' : 'At most '.fmt_num($land->area_under_lease_acres).' ac (area under lease).')" />
    <x-field name="leased_capacity_mw" label="Leased capacity (MW)" :type="$t('number')" :value="$L->leased_capacity_mw !== null ? (float) $L->leased_capacity_mw : null" req
      :hint="$readonly ? null : trim(($mwUsed > 0 ? fmt_num($mwUsed, 3).' MW already subleased at peak; cannot go below that. ' : 'Electricity capacity that can be subleased from this lease. ').($parkFree !== null ? fmt_num(max(0, $parkFree), 3).' MW of park '.$land->park->park_code.' is free for an active lease.' : ''))" />
    <x-field name="lease_status" label="Lease status" :type="$t('select')" :options="opts('lease_status')" :value="$L->lease_status" :blank="false" />
  </fieldset>
  <fieldset><legend>Lessor address</legend>
    <x-field name="lessor_address" label="House / street / village" :type="$t('text')" :value="$L->lessor_address ?: ($sv->first() ? 'At & Post '.$sv->first()->village : null)" span />
    @include('partials.location-fields', ['p' => 'lessor_', 'm' => $L, 'readonly' => $readonly, 'stateOpts' => $stateOpts])
    <x-field name="lessor_pincode" label="PIN code" :type="$t('text')" :value="$L->lessor_pincode" placeholder="364140" maxlength="6" inputmode="numeric" />
  </fieldset>
  <fieldset><legend>Dates &amp; registration</legend>
    <x-field name="atl_date" label="Agreement to lease (ATL) date" :type="$t('date')" :value="$L->atl_date">{{ $readonly ? fmt_date($L->atl_date) : '' }}</x-field>
    <x-field name="lease_date" label="Lease registration date" :type="$t('date')" :value="$L->lease_date">{{ $readonly ? fmt_date($L->lease_date) : '' }}</x-field>
    <x-field name="lease_start_date" label="Lease start date" :type="$t('date')" :value="$L->lease_start_date" req>{{ $readonly ? fmt_date($L->lease_start_date) : '' }}</x-field>
    <x-field name="lease_duration_mths" label="Lease period (months)" :type="$t('number')" :value="$L->lease_duration_mths" req
      :hint="$L->lease_duration_mths ? fmt_num($L->lease_duration_mths / 12).' years' : null" data-years />
    <x-field name="lease_end_date_ro" label="Lease end date" type="ro" id="f_lease_end_date">{{ fmt_date($L->lease_end_date) }}</x-field>
    <x-field name="uni_number" label="UNI number" :type="$t('text')" :value="$L->uni_number" />
    <x-field name="sro_doc_no" label="SRO document no." :type="$t('text')" :value="$L->sro_doc_no" />
    <x-field name="agreement_no" label="Agreement no." :type="$t('text')" :value="$L->agreement_no" />
    <x-field name="mutation_entry_no" label="Lease mutation entry (7/12)" :type="$t('text')" :value="$L->mutation_entry_no" />
  </fieldset>
  <fieldset><legend>Rent</legend>
    <x-field name="rent_rate_per_acre" label="Rent per acre (₹ / year)" :type="$t('number')" :value="$L->rent_rate_per_acre !== null ? (float) $L->rent_rate_per_acre : null" req>{{ $readonly ? inr($L->rent_rate_per_acre) : '' }}</x-field>
    <x-field name="rent_frequency" label="Rent frequency" :type="$t('select')" :options="opts('rent_frequency')" :value="$L->rent_frequency" :blank="false" />
    <x-field name="rent_amount_ro" label="Rent per instalment (year 1)" type="ro" id="f_rent_amount">{{ inr($L->rent_amount ?? \App\Services\RentScheduleService::instalment((float) $L->leased_area_acres, (float) $L->rent_rate_per_acre, $L->rent_frequency ?? 'Annual')) }}</x-field>
    <x-field name="security_deposit" label="Security deposit (₹)" :type="$t('number')" :value="$L->security_deposit !== null ? (float) $L->security_deposit : null">{{ $readonly ? inr($L->security_deposit) : '' }}</x-field>
    <x-field name="escalation_pct" label="Escalation (%)" :type="$t('number')" :value="$L->escalation_pct !== null ? (float) $L->escalation_pct : null" />
    <x-field name="escalation_every_yrs" label="Escalate every (years)" :type="$t('number')" :value="$L->escalation_every_yrs" />
  </fieldset>
  <fieldset><legend>Tax</legend>
    <x-field name="tds_applicable" label="TDS applicable (194-I)" :type="$t('yn')" :value="$L->tds_applicable" :blank="false" />
    <div data-show-if="tds_applicable=Yes" @if ($L->tds_applicable !== 'Yes') hidden @endif style="display:contents">
      <x-field name="tds_rate" label="TDS rate (%)" :type="$t('number')" :value="$L->tds_rate !== null ? (float) $L->tds_rate : null" />
    </div>
    <x-field name="gst_applicable" label="GST applicable" :type="$t('yn')" :value="$L->gst_applicable" :blank="false" />
  </fieldset>
  @if ($L->exists && ! $readonly)
    <p class="hint">Changes to each field are recorded in the audit trail. Changing area, rate, frequency, escalation, start, period or TDS regenerates the unpaid instalments{{ $hasPayments ? '; instalments that already have payments are kept' : '' }}.</p>
  @endif
  </div>
</form>
