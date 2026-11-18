{{-- Survey number fields, used on the create page and in the inline edit of the workspace --}}
@php($isNew = ! $plot->exists)
<fieldset>
  <legend>Park, SPV &amp; location</legend>
  @if ($isNew)
    <x-field name="park_id" label="Park" type="select" :options="$parks" :value="$plot->park_id" req
      hint="Choosing a park fills its state, city and taluka. You can change them for this survey number." data-plot-park="1" />
  @else
    <x-field name="park_ro" label="Park" type="ro" :value="$plot->park->park_code.' · '.$plot->park->park_name" lock />
  @endif
  <x-field name="spv_id" label="SPV" type="select" :options="$spvOpts" :value="$plot->spv_id" req hint="Only SPVs of the selected park appear." />
  @if ($isNew)
    @include('partials.location-fields', ['model' => $plot])
  @else
    <x-field name="loc_ro" label="State / City / Taluka" type="ro" :value="$plot->state->name.' / '.$plot->city->name.' / '.$plot->taluka->name" lock />
  @endif
  <x-field name="village" label="Village" :value="$plot->village" req />
  <x-field name="survey_no" label="Survey number" :value="$plot->survey_no" req placeholder="125" />
  <x-field name="sub_survey_no" label="Sub survey number (optional)" :value="$plot->sub_survey_no" placeholder="2" />
  <div class="fld span2">
    <label>Land Reference Number</label>
    <div class="ro" id="landRefPreview">
      @if ($isNew)<span class="hint">Fills once park, state, city and taluka are set</span>@else<span class="ref">{{ $plot->land_ref_no }}</span><span class="lock">LOCKED</span>@endif
    </div>
    <small class="hint">Format {{ app(\App\Services\LandRefService::class)->format() }}. System-generated on save and never changes, even if the location is edited later.</small>
  </div>
</fieldset>
<fieldset>
  <legend>Area</legend>
  <x-field name="total_area" label="Total land area" type="number" :value="$plot->total_area" req />
  <x-field name="land_unit" label="Land unit" type="select" :options="opts('land_unit')" :value="$plot->land_unit" req :blank="false" />
  <x-field name="area_under_lease_acres" label="Area under lease (acres)" type="number" :value="$plot->area_under_lease_acres" req hint="Cannot exceed the survey number area." />
  <x-field name="dlr_area_sqm" label="DLR area (sq.m)" type="number" :value="$plot->dlr_area_sqm" />
  <div class="fld span2"><small class="hint" id="acresHint"></small></div>
</fieldset>
<fieldset>
  <legend>Rate</legend>
  <x-field name="rate_per_acre" label="Rate per acre (₹ / year)" type="number" :value="$plot->rate_per_acre" req />
  <x-field name="increment_after_yrs" label="Increment after (years)" type="number" :value="$plot->increment_after_yrs" req hint="The rate goes up once every this many years." />
  <x-field name="increment_pct" label="Increment (%)" type="number" :value="$plot->increment_pct" req />
</fieldset>
<fieldset>
  <legend>Due diligence</legend>
  <x-field name="title_report" label="Title report received" type="yn" :value="$plot->title_report" req :blank="false" />
  <x-field name="title_dd_status" label="Title DD status" type="select" :options="opts('title_dd_status')" :value="$plot->title_dd_status" req :blank="false" />
  <x-field name="lessor_capacity_verified" label="Lessor capacity verified" type="yn" :value="$plot->lessor_capacity_verified" req :blank="false" />
  <x-field name="encumbrance_status" label="Encumbrance status" type="select" :options="opts('encumbrance_status')" :value="$plot->encumbrance_status" req :blank="false" />
  <x-field name="lien_details" label="Lien (Boja) details" type="textarea" :value="$plot->lien_details" span />
</fieldset>
<fieldset>
  <legend>NA conversion &amp; status</legend>
  <x-field name="na_application" label="NA application filed" type="yn" :value="$plot->na_application" req :blank="false" />
  <x-field name="na_status" label="NA status" type="select" :options="opts('na_status')" :value="$plot->na_status" req :blank="false" />
  <x-field name="land_status" label="Land status" type="select" :options="opts('land_status')" :value="$plot->land_status" req :blank="false" />
</fieldset>
