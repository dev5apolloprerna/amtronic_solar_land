{{-- Cascading State → City → Taluka selects. Needs $stateOpts, $cityOpts, $talukaOpts; optional $model, $prefix, $req, $ro --}}
@php($p = $prefix ?? '')
@php($m = $model ?? null)
<x-field :name="$p.'state_id'" label="State" type="select" :options="$stateOpts" :value="$m?->{$p.'state_id'}" :req="$req ?? true" :lock="$ro ?? false"
  data-cascade="#f_{{ $p }}city_id" data-cascade-url="{{ route('lookup.cities') }}?state_id=" />
<x-field :name="$p.'city_id'" label="City" type="select" :options="$cityOpts" :value="$m?->{$p.'city_id'}" :req="$req ?? true" :lock="$ro ?? false"
  data-cascade="#f_{{ $p }}taluka_id" data-cascade-url="{{ route('lookup.talukas') }}?city_id=" hint="Choose a state first." />
<x-field :name="$p.'taluka_id'" label="Taluka" type="select" :options="$talukaOpts" :value="$m?->{$p.'taluka_id'}" :req="$req ?? true" :lock="$ro ?? false" hint="Choose a city first." />
