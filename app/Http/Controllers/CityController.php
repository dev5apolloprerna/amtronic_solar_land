<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Models\Park;
use App\Models\State;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $q = City::with('state')->withCount(['talukas', 'plots'])
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('code', 'like', "%$s%")))
            ->when($request->state_id, fn ($q, $v) => $q->where('state_id', $v))
            ->orderBy('name');

        if ($export = $this->maybeExport('city', 'City Master', ['City', 'Code', 'State', 'Talukas', 'Survey numbers', 'Status'],
            $q->get()->map(fn ($c) => [$c->name, $c->code, $c->state->name, $c->talukas_count, $c->plots_count, $c->status]))) {
            return $export;
        }

        return view('masters.cities.index', [
            'rows' => $q->paginate(config('solar.per_page'))->withQueryString(),
            'total' => City::count(),
            'states' => State::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create()
    {
        return $this->drawer('Add city', 'CITIES', 'masters.cities.form', $this->formData(new City(['status' => 'Active'])), module: 'city');
    }

    public function store(CityRequest $request)
    {
        $city = City::create($request->validated());

        return $this->ok("City {$city->name} saved");
    }

    public function edit(City $city)
    {
        $ro = ! $this->canEdit('city');

        return $this->drawer(($ro ? 'View' : 'Edit').' city', 'CITIES · '.$city->name, 'masters.cities.form', $this->formData($city), $ro, 'city');
    }

    public function update(CityRequest $request, City $city)
    {
        $city->update($request->validated());

        return $this->ok("City {$city->name} saved");
    }

    public function destroy(City $city)
    {
        if ($city->talukas()->exists()) {
            return $this->refuse("{$city->name} has talukas. Delete them first.");
        }
        if ($city->plots()->withTrashed()->exists() || Park::where('city_id', $city->id)->exists()) {
            return $this->refuse("{$city->name} is used by parks or survey numbers.");
        }
        $city->delete();

        return $this->ok("{$city->name} deleted");
    }

    private function formData(City $city): array
    {
        return ['city' => $city, 'states' => State::where('status', 'Active')->orderBy('name')->get()->mapWithKeys(fn ($s) => [$s->id => "$s->name ($s->code)"])];
    }
}
