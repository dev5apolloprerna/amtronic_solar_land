<?php

namespace App\Http\Controllers;

use App\Http\Requests\DistrictRequest;
use App\Models\District;
use App\Models\LandSurvey;
use App\Models\Park;
use App\Models\State;
use Illuminate\Http\Request;

/** DISTRICT_MASTER */
class DistrictController extends Controller
{
    public function index(Request $request)
    {
        $q = District::with('state')->withCount(['cities', 'lands'])
            ->when($request->q, fn ($q, $s) => $q->where('district_name', 'like', "%$s%"))
            ->when($request->state, fn ($q, $v) => $q->where('state_id', $v))
            ->orderBy('id');

        if ($e = $this->maybeExport('district', 'District Master', ['ID', 'District', 'State', 'Cities / talukas', 'Land parcels'],
            $q->get()->map(fn ($r) => ['DT'.str_pad((string) $r->id, 3, '0', STR_PAD_LEFT), $r->district_name, $r->state->state_name, $r->cities_count, $r->lands_count]))) {
            return $e;
        }

        return view('masters.districts.index', ['rows' => $q->paginate(config('solar.per_page'))->withQueryString(), 'total' => District::count(), 'states' => State::stateOptions()]);
    }

    public function create()
    {
        return $this->drawer('Add district', 'DISTRICT_MASTER', 'masters.districts.form', ['district' => new District(), 'states' => State::stateOptions()], module: 'district');
    }

    public function store(DistrictRequest $request)
    {
        District::create($request->validated());

        return $this->ok('District saved');
    }

    public function edit(District $district)
    {
        $ro = ! $this->canEdit('district');

        return $this->drawer(($ro ? 'View' : 'Edit').' district', 'DISTRICT_MASTER · '.$district->district_name, 'masters.districts.form',
            ['district' => $district, 'states' => State::stateOptions()], $ro, 'district');
    }

    public function update(DistrictRequest $request, District $district)
    {
        $district->update($request->validated());

        return $this->ok('District saved');
    }

    public function destroy(District $district)
    {
        if ($district->cities()->exists()) {
            return $this->refuse("{$district->district_name} has cities or talukas. Delete them first.");
        }
        if (LandSurvey::where('district_id', $district->id)->exists() || Park::where('district_id', $district->id)->exists() || $district->lands()->exists()) {
            return $this->refuse("{$district->district_name} is used by parks or survey numbers.");
        }
        $district->delete();

        return $this->ok("{$district->district_name} deleted");
    }
}
