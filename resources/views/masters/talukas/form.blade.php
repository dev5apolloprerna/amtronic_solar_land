@include('partials.form-open', ['action' => $taluka->exists ? route('talukas.update', $taluka) : route('talukas.store'), 'model' => $taluka])
  @php($locked = $taluka->codeLocked())
  <fieldset>
    @if ($readonly || $locked)
      <x-field name="state_ro" label="State" type="ro" :value="$taluka->city?->state?->name" />
      <x-field name="city_ro" label="City" type="ro" :value="$taluka->city?->name" :lock="$locked" />
    @else
      <x-field name="state_pick" label="State" type="select" :options="$states" :value="$stateId" req data-cascade="#f_city_id" data-cascade-url="{{ route('lookup.cities') }}?state_id=" />
      <x-field name="city_id" label="City" type="select" :options="$cities" :value="$taluka->city_id" req hint="Choose a state first." />
    @endif
    <x-field name="name" label="Taluka name" :value="$taluka->name" req :lock="$readonly" />
    <x-field name="code" label="Taluka code" :value="$taluka->code" req placeholder="TLJ" hint="2–5 letters, unique within the city. Locked once a survey number uses it." :lock="$readonly || $locked" maxlength="5" />
    <x-field name="status" label="Status" type="select" :options="opts('status')" :value="$taluka->status" req :lock="$readonly" :blank="false" />
  </fieldset>
</form>
