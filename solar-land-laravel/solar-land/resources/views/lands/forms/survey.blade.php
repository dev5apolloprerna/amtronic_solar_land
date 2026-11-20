@php($t = fn ($type) => $readonly ? 'ro' : $type)
@include('partials.form-open', ['action' => $s->exists ? route('surveys.update', $s) : route('surveys.store', $land), 'model' => $s])
  <fieldset>
    <x-field name="land_ref" label="Land reference no." type="ro" :value="$land->land_ref_no" span :lock="$s->exists && ! $readonly" />
    @include('partials.location-fields', ['m' => $s, 'readonly' => $readonly, 'stateOpts' => $stateOpts])
    <x-field name="village" label="Village" :type="$t('text')" :value="$s->village" req />
    <x-field name="survey_plot_no" label="Survey / plot no." :type="$t('text')" :value="$s->survey_plot_no" req placeholder="112/2" />
    <x-field name="area_sqm" label="Area as per 7/12 (sq.m)" :type="$t('number')" :value="$s->area_sqm" req data-sqm-to="#f_area_acres" />
    <x-field name="area_acres" label="Area (acres)" type="ro" :value="$s->area_acres !== null ? fmt_num($s->area_acres) : null" :hint="$readonly ? null : 'Calculated: sq.m ÷ 4,046.86'" />
  </fieldset>
  @if ($s->exists && ! $readonly)<p class="hint">Changes to each field are recorded in the audit trail with the old and new value.</p>@endif
</form>
