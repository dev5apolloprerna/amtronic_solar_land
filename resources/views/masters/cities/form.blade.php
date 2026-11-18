@include('partials.form-open', ['action' => $city->exists ? route('cities.update', $city) : route('cities.store'), 'model' => $city])
  @php($locked = $city->codeLocked())
  <fieldset>
    <x-field name="state_id" label="State" type="select" :options="$states" :value="$city->state_id" req :lock="$readonly || $locked" />
    <x-field name="name" label="City name" :value="$city->name" req :lock="$readonly" />
    <x-field name="code" label="City code" :value="$city->code" req placeholder="BHV" hint="2–5 letters, unique within the state. Locked once a survey number uses it." :lock="$readonly || $locked" maxlength="5" />
    <x-field name="status" label="Status" type="select" :options="opts('status')" :value="$city->status" req :lock="$readonly" :blank="false" />
  </fieldset>
</form>
