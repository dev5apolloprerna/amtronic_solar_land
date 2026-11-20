@include('partials.form-open', ['action' => $district->exists ? route('districts.update', $district) : route('districts.store'), 'model' => $district])
  <fieldset>
    <x-field name="state_id" label="State" :type="$readonly ? 'ro' : 'select'" :options="$states" :value="$district->state_id" req />
    <x-field name="district_name" label="District name" :type="$readonly ? 'ro' : 'text'" :value="$district->district_name" req />
  </fieldset>
  @if ($district->exists && ! $readonly)<p class="hint">Changes to each field are recorded in the audit trail with the old and new value.</p>@endif
</form>
