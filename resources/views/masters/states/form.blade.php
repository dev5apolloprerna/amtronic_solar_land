@include('partials.form-open', ['action' => $state->exists ? route('states.update', $state) : route('states.store'), 'model' => $state])
  <fieldset>
    <x-field name="state_name" label="State name" :value="$state->state_name" req :type="$readonly ? 'ro' : 'text'" />
    <x-field name="state_code" label="State code" :value="$state->state_code" req placeholder="GJ" maxlength="2" :lock="$state->exists" :type="$readonly ? 'ro' : 'text'"
      hint="2 letters. Locked after save because land reference numbers use it." />
  </fieldset>
  @if ($state->exists && ! $readonly)<p class="hint">Changes to each field are recorded in the audit trail with the old and new value.</p>@endif
</form>
