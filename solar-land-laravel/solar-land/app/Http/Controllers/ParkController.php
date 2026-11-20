<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParkRequest;
use App\Models\Park;
use App\Services\CapacityService;
use Illuminate\Http\Request;

/** PARK_MASTER */
class ParkController extends Controller
{
    public function __construct(private CapacityService $cap) {}

    public function index(Request $request)
    {
        $q = Park::with(['state', 'district', 'city', 'spvs'])->withCount('lands')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('park_code', 'like', "%$s%")->orWhere('park_name', 'like', "%$s%")))
            ->orderBy('id');

        if ($e = $this->maybeExport('park', 'Park Master', ['Code', 'Park name', 'City / taluka', 'District', 'State', 'Area (ac)', 'Installed MW', 'Leased MW today', 'SPVs', 'Parcels'],
            $q->get()->map(fn ($p) => [$p->park_code, $p->park_name, $p->city->city_name, $p->district->district_name, $p->state->state_name, (float) $p->total_area_acres,
                (float) $p->installed_capacity_mw, $this->cap->parkLeasedToday($p), $p->spvs->pluck('spv_code')->implode(', '), $p->lands_count]))) {
            return $e;
        }

        return view('masters.parks.index', ['rows' => $q->paginate(config('solar.per_page'))->withQueryString(), 'total' => Park::count(), 'cap' => $this->cap]);
    }

    public function create()
    {
        return $this->drawer('Add park', 'PARK_MASTER', 'masters.parks.form', ['park' => new Park()], module: 'park');
    }

    public function store(ParkRequest $request)
    {
        $d = $request->validated();
        $p = new Park($d);
        $p->park_code = $d['park_code'];
        $p->save();

        return $this->ok('Park saved');
    }

    public function edit(Park $park)
    {
        $ro = ! $this->canEdit('park');

        return $this->drawer(($ro ? 'View' : 'Edit').' park', 'PARK_MASTER · '.$park->park_code, 'masters.parks.form', ['park' => $park, 'leasedToday' => $this->cap->parkLeasedToday($park)], $ro, 'park');
    }

    public function update(ParkRequest $request, Park $park)
    {
        $park->update($request->validated());

        return $this->ok('Park saved');
    }

    public function destroy(Park $park)
    {
        if ($park->spvs()->exists()) {
            return $this->refuse("{$park->park_code} has SPVs under it. Delete or move the SPVs first.");
        }
        if ($park->lands()->exists()) {
            return $this->refuse("{$park->park_code} has land parcels.");
        }
        $park->delete();

        return $this->ok("{$park->park_code} deleted");
    }
}
