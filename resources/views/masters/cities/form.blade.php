@php
  $states = \App\Models\State::stateOptions();
  $districts = $city->state_id ? \App\Models\District::where('state_id', $city->state_id)->orderBy('id')->pluck('district_name', 'id')->all() : [];
@endphp
@include('partials.form-open', ['action' => $city->exists ? route('cities.update', $city) : route('cities.store'), 'model' => $city])
  <fieldset>
    <x-field name="state_id" label="State" :type="$readonly ? 'ro' : 'select'" :options="$states" :value="$city->state_id" req
      data-cascade="#f_district_id" :data-cascade-url="route('lookup.districts').'?state_id='" />
    <x-field name="district_id" label="District" :type="$readonly ? 'ro' : 'select'" :options="$districts" :value="$city->district_id" req
      :hint="$readonly ? null : 'Choose a state first.'" />
    <x-field name="city_name" label="City / taluka name" :type="$readonly ? 'ro' : 'text'" :value="$city->city_name" req />
    <x-field name="city_type" label="Type" :type="$readonly ? 'ro' : 'select'" :options="opts('city_type')" :value="$city->city_type" :blank="false" />
  </fieldset>
  @if ($city->exists && ! $readonly)<p class="hint">Changes to each field are recorded in the audit trail with the old and new value.</p>@endif
</form>
