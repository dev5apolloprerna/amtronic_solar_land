@include('partials.form-open', ['action' => $park->exists ? route('parks.update', $park) : route('parks.store'), 'model' => $park])
  <fieldset>
    <x-field name="park_code" label="Park code" :type="$readonly ? 'ro' : 'text'" :value="$park->park_code" req placeholder="BHV" maxlength="10" :lock="$park->exists"
      hint="3 letters. Used in land reference numbers." />
    <x-field name="park_name" label="Park name" :type="$readonly ? 'ro' : 'text'" :value="$park->park_name" req />
    @include('partials.location-fields', ['m' => $park])
    <x-field name="total_area_acres" label="Total area (acres)" :type="$readonly ? 'ro' : 'number'" :value="$park->total_area_acres" />
    <x-field name="installed_capacity_mw" label="Installed capacity (MW)" :type="$readonly ? 'ro' : 'number'" :value="$park->installed_capacity_mw"
      :hint="$park->exists ? fmt_num($leasedToday ?? 0, 3).' MW is leased today. Capacity cannot go below that.' : 'Total MW of the park. Active leases in this park can never exceed it.'" />
  </fieldset>
  @if ($park->exists && ! $readonly)<p class="hint">Changes to each field are recorded in the audit trail with the old and new value.</p>@endif
</form>
