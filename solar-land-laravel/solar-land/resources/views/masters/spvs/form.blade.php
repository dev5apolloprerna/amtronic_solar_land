@include('partials.form-open', ['action' => $spv->exists ? route('spvs.update', $spv) : route('spvs.store'), 'model' => $spv])
  <fieldset>
    <x-field name="state_id" label="State" :type="$readonly ? 'ro' : 'select'" :options="$states" :value="$spv->state_id" req
      data-cascade="#f_park_id" :data-cascade-url="route('lookup.parks').'?state_id='" />
    <x-field name="park_id" label="Park" :type="$readonly ? 'ro' : 'select'" :options="$parks" :value="$spv->park_id" req hint="Only parks within the selected state are shown." />
    <x-field name="spv_code" label="SPV code" :type="$readonly ? 'ro' : 'text'" :value="$spv->spv_code" req placeholder="PGP10" :lock="$spv->exists"
      hint="Locked after save because land reference numbers use it." />
    @unless ($readonly)
      <div class="fld span2" data-park-note>
        <div class="note-b">@if ($spv->park)Location from park: {{ $spv->park->city->city_name }}, {{ $spv->park->district->district_name }}, {{ $spv->park->state->state_name }}@else Choose the park this SPV belongs to. Its state, district and city come from the park.@endif</div>
      </div>
    @endunless
    <x-field name="spv_name" label="SPV name" :type="$readonly ? 'ro' : 'text'" :value="$spv->spv_name" req />
    <x-field name="technology" label="Technology" :type="$readonly ? 'ro' : 'select'" :options="opts('technology')" :value="$spv->technology" :blank="false" />
    <x-field name="capacity_mwp" label="Capacity (MWp)" :type="$readonly ? 'ro' : 'number'" :value="$spv->capacity_mwp" />
    <x-field name="status" label="Status" :type="$readonly ? 'ro' : 'select'" :options="opts('spv_status')" :value="$spv->status" :blank="false" />
  </fieldset>
  @if ($spv->exists && ! $readonly)<p class="hint">Changes to each field are recorded in the audit trail with the old and new value.</p>@endif
</form>
