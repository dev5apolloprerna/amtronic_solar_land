<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Models\District;
use App\Models\LandSurvey;
use App\Models\Lease;
use App\Models\Park;
use App\Models\State;
use Illuminate\Http\Request;

/** CITY_MASTER: cities, talukas and tehsils */
class CityController extends Controller
{
    public function index(Request $request)
    {
        $q = City::with(['state', 'district'])->withCount('lands')
            ->when($request->q, fn ($q, $s) => $q->where('city_name', 'like', "%$s%"))
            ->when($request->state, fn ($q, $v) => $q->where('state_id', $v))
            ->when($request->district, fn ($q, $v) => $q->where('district_id', $v))
            ->orderBy('id');

        if ($e = $this->maybeExport('city', 'City / Taluka Master', ['ID', 'City / taluka', 'Type', 'District', 'State', 'Land parcels'],
            $q->get()->map(fn ($r) => ['CT'.str_pad((string) $r->id, 3, '0', STR_PAD_LEFT), $r->city_name, $r->city_type, $r->district->district_name, $r->state->state_name, $r->lands_count]))) {
            return $e;
        }

        return view('masters.cities.index', [
            'rows' => $q->paginate(config('solar.per_page'))->withQueryString(),
            'total' => City::count(),
            'states' => State::stateOptions(),
            'districts' => District::when($request->state, fn ($q, $v) => $q->where('state_id', $v))->orderBy('district_name')->pluck('district_name', 'id')->all(),
        ]);
    }

    public function create()
    {
        return $this->drawer('Add city / taluka', 'CITY_MASTER', 'masters.cities.form', ['city' => new City(['city_type' => 'Taluka'])], module: 'city');
    }

    public function store(CityRequest $request)
    {
        City::create($request->validated() + ['city_type' => 'Taluka']);

        return $this->ok('City / Taluka saved');
    }

    public function edit(City $city)
    {
        $ro = ! $this->canEdit('city');

        return $this->drawer(($ro ? 'View' : 'Edit').' city / taluka', 'CITY_MASTER · '.$city->city_name, 'masters.cities.form', compact('city'), $ro, 'city');
    }

    public function update(CityRequest $request, City $city)
    {
        $city->update($request->validated());

        return $this->ok('City / Taluka saved');
    }

    public function destroy(City $city)
    {
        if (LandSurvey::where('city_id', $city->id)->exists() || Park::where('city_id', $city->id)->exists() || Lease::where('lessor_city_id', $city->id)->exists() || $city->lands()->exists()) {
            return $this->refuse("{$city->city_name} is used by parks, survey numbers or lessor addresses.");
        }
        $city->delete();

        return $this->ok("{$city->city_name} deleted");
    }
}
