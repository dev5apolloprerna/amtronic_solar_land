@include('partials.form-open', ['action' => $state->exists ? route('states.update', $state) : route('states.store'), 'model' => $state])
  <fieldset>
    <x-field name="name" label="State name" :value="$state->name" req :lock="$readonly" />
    <x-field name="code" label="State code" :value="$state->code" req placeholder="GJ" hint="2 letters. Locked once a survey number uses it." :lock="$readonly || $state->codeLocked()" maxlength="2" />
    <x-field name="status" label="Status" type="select" :options="opts('status')" :value="$state->status" req :lock="$readonly" :blank="false" />
  </fieldset>
</form>
