@include('partials.form-open', ['action' => $park->exists ? route('parks.update', $park) : route('parks.store'), 'model' => $park])
  <fieldset>
    <legend>Park</legend>
    <x-field name="park_code" label="Park code" :value="$park->park_code" req placeholder="BHV" maxlength="6"
      hint="2–6 letters or digits. Used in Land Reference Numbers, so it locks after saving." :lock="$readonly || $park->exists" />
    <x-field name="park_name" label="Park name" :value="$park->park_name" req :lock="$readonly" />
    <x-field name="status" label="Status" type="select" :options="opts('status')" :value="$park->status" req :lock="$readonly" :blank="false" />
  </fieldset>
  <fieldset>
    <legend>Location</legend>
    @include('partials.location-fields', ['model' => $park, 'ro' => $readonly])
    <x-field name="address" label="Address" type="textarea" :value="$park->address" span :lock="$readonly" />
  </fieldset>
  <fieldset>
    <legend>Land &amp; capacity</legend>
    <x-field name="total_land_area" label="Total land area" type="number" :value="$park->total_land_area" req :lock="$readonly" />
    <x-field name="land_unit" label="Land unit" type="select" :options="opts('land_unit')" :value="$park->land_unit" req :lock="$readonly" :blank="false" />
    <x-field name="installed_capacity_mw" label="Total installed capacity (MW)" type="number" :value="$park->installed_capacity_mw" req :lock="$readonly"
      :hint="$park->exists ? 'Active leases commit '.fmt_mw($minMw ?? 0).' at peak; capacity cannot go below that.' : null" />
    @if ($park->exists)
      <x-field name="leased_ro" label="Leased today / free today" type="ro">{{ fmt_mw($leasedToday) }} leased · {{ fmt_mw((float) $park->installed_capacity_mw - $leasedToday) }} free</x-field>
    @endif
    <x-field name="description" label="Description" type="textarea" :value="$park->description" span :lock="$readonly" />
  </fieldset>
</form>
