{{-- State → District → City / Taluka cascade. $p: field prefix ('' or 'lessor_'), $m: model, $readonly, optional $stateOpts/$districtOpts/$cityOpts --}}
@php
  $p ??= '';
  $sid = old($p.'state_id', $m->{$p.'state_id'});
  $did = old($p.'district_id', $m->{$p.'district_id'});
  $stateOpts ??= \App\Models\State::stateOptions();
  $districtOpts = $sid ? \App\Models\District::where('state_id', $sid)->orderBy('id')->pluck('district_name', 'id')->all() : [];
  $cityOpts = $did ? \App\Models\City::where('district_id', $did)->orderBy('id')->get()->mapWithKeys(fn ($c) => [$c->id => "$c->city_name · $c->city_type"])->all() : [];
  $ro = ($readonly ?? false) || ($lockAll ?? false);
@endphp
<x-field :name="$p.'state_id'" label="State" :type="$ro ? 'ro' : 'select'" :options="$stateOpts" :value="$sid" req
  data-cascade="#f_{{ $p }}district_id" :data-cascade-url="route('lookup.districts').'?state_id='" :hint="$stateHint ?? null" />
<x-field :name="$p.'district_id'" label="District" :type="$ro ? 'ro' : 'select'" :options="$districtOpts" :value="$did" req
  data-cascade="#f_{{ $p }}city_id" :data-cascade-url="route('lookup.cities').'?district_id='" />
<x-field :name="$p.'city_id'" label="City / Taluka" :type="$ro ? 'ro' : 'select'" :options="$cityOpts" :value="old($p.'city_id', $m->{$p.'city_id'})" req />
