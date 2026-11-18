<?php

namespace App\Http\Controllers;

use App\Enums\LandUnit;
use App\Http\Requests\ParkRequest;
use App\Models\Park;
use App\Services\CapacityService;
use App\Support\LocationOptions;
use Illuminate\Http\Request;

class ParkController extends Controller
{
    public function __construct(private CapacityService $capacity) {}

    public function index(Request $request)
    {
        $q = Park::with(['state', 'city', 'taluka', 'spvs'])->withCount('plots')
            ->withSum(['leases as leased_mw' => fn ($l) => $l->where('status', 'Active')], 'leased_capacity_mw')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('park_name', 'like', "%$s%")->orWhere('park_code', 'like', "%$s%")))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->orderBy('park_code');

        if ($export = $this->maybeExport('park', 'Park Master', ['Code', 'Park', 'Taluka', 'City', 'State', 'Land area', 'Unit', 'Installed MW', 'Leased MW (active)', 'SPVs', 'Survey nos.', 'Status'],
            $q->get()->map(fn ($p) => [$p->park_code, $p->park_name, $p->taluka->name, $p->city->name, $p->state->name, (float) $p->total_land_area, $p->land_unit,
                (float) $p->installed_capacity_mw, (float) $p->leased_mw, $p->spvs->pluck('spv_code')->implode(', '), $p->plots_count, $p->status]))) {
            return $export;
        }

        return view('masters.parks.index', ['rows' => $q->paginate(config('solar.per_page'))->withQueryString(), 'total' => Park::count()]);
    }

    public function create()
    {
        $park = new Park(['status' => 'Active', 'land_unit' => 'Acre']);

        return $this->drawer('Add park', 'PARKS', 'masters.parks.form', ['park' => $park] + LocationOptions::for($park), module: 'park');
    }

    public function store(ParkRequest $request)
    {
        $data = $request->validated();
        $park = new Park($data);
        $park->park_code = $data['park_code'];
        $park->total_land_acres = LandUnit::acres($data['land_unit'], (float) $data['total_land_area']);
        $park->save();

        return $this->ok("Park {$park->park_code} saved");
    }

    public function edit(Park $park)
    {
        $ro = ! $this->canEdit('park');

        return $this->drawer(($ro ? 'View' : 'Edit').' park', 'PARKS · '.$park->park_code, 'masters.parks.form',
            ['park' => $park, 'leasedToday' => $this->capacity->leasedTodayInPark($park), 'minMw' => $this->capacity->minimumParkMw($park)] + LocationOptions::for($park), $ro, 'park');
    }

    public function update(ParkRequest $request, Park $park)
    {
        $data = $request->validated();
        $park->fill($data);
        $park->total_land_acres = LandUnit::acres($data['land_unit'], (float) $data['total_land_area']);
        $park->save();

        return $this->ok("Park {$park->park_code} saved");
    }

    public function destroy(Park $park)
    {
        if ($park->spvs()->exists()) {
            return $this->refuse("{$park->park_code} has SPVs under it. Delete or move the SPVs first.");
        }
        if ($park->plots()->withTrashed()->exists()) {
            return $this->refuse("{$park->park_code} has survey numbers.");
        }
        $park->delete();

        return $this->ok("Park {$park->park_code} deleted");
    }
}
