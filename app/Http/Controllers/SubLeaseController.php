<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LandAccess;
use App\Http\Requests\SubleaseRequest;
use App\Models\Land;
use App\Models\Lease;
use App\Models\Sublease;
use App\Services\CapacityService;
use Illuminate\Http\Request;

/** SUBLEASE_MASTER · area (acres) and capacity (MW) carved out of a lease */
class SubleaseController extends Controller
{
    use LandAccess;

    public function __construct(private CapacityService $cap) {}

    public function create(Request $request, Land $land)
    {
        $this->guardLand($land);
        $leases = $land->leases()->whereIn('lease_status', ['Active', 'Draft'])->orderBy('id')->get();
        if ($leases->isEmpty()) {
            return $this->refuse($land->land_ref_no.' has no active lease to sublease from. Add a lease first.');
        }
        $L = $leases->firstWhere('id', (int) $request->lease_id) ?? $leases->firstWhere('lease_status', 'Active') ?? $leases->first();
        $s = new Sublease(['lease_id' => $L->id, 'status' => 'Active', 'sublease_start' => $L->lease_start_date, 'sublease_end' => $L->lease_end_date,
            'sublease_area_acres' => max(0, $this->cap->areaFree($L)) ?: null, 'capacity_mw' => max(0, $this->cap->mwFree($L, $L->lease_start_date, $L->lease_end_date)),
            'lender_noc_obtained' => 'No']);

        return $this->drawer('Add sublease', 'SUBLEASE_MASTER · '.$land->land_ref_no, 'lands.forms.sublease', $this->formData($s, $land, $leases), module: 'sublease');
    }

    public function store(SubleaseRequest $request, Land $land)
    {
        $this->guardLand($land);
        $d = $request->validated();
        $L = Lease::findOrFail($d['lease_id']);
        abort_unless($L->land_id === $land->id, 422, 'That lease is not on this parcel.');
        $s = Sublease::create($d + ['land_id' => $land->id]);
        $this->syncLandStatus($land);

        return $this->ok("{$s->code} saved · ".fmt_num($this->cap->areaFree($L)).' ac and '.CapacityService::fmt($this->cap->mwFree($L, now()->startOfDay(), now()->startOfDay())).' MW of '.$L->code.' still free today', $this->tabUrl($land, 'sublease'));
    }

    public function edit(Sublease $sublease)
    {
        $land = $sublease->land;
        $this->guardLand($land);
        $ro = ! $this->canEdit('sublease');

        return $this->drawer(($ro ? 'View' : 'Edit').' sublease', 'SUBLEASE_MASTER · '.$sublease->code, 'lands.forms.sublease', $this->formData($sublease, $land, collect([$sublease->lease])), $ro, 'sublease');
    }

    public function update(SubleaseRequest $request, Sublease $sublease)
    {
        $this->guardLand($sublease->land);
        $sublease->update($request->validated());
        $this->syncLandStatus($sublease->land);

        return $this->ok('Sublease saved', $this->tabUrl($sublease->land, 'sublease'));
    }

    public function destroy(Sublease $sublease)
    {
        $land = $sublease->land;
        $this->guardLand($land);
        if ($land->documents()->where('linked_entity_type', 'Sublease')->where('linked_entity_id', $sublease->id)->exists()) {
            return $this->refuse("{$sublease->code} has documents linked to it. Delete those documents first.");
        }
        $sublease->delete();
        $this->syncLandStatus($land);

        return $this->ok("{$sublease->code} deleted", $this->tabUrl($land, 'sublease'));
    }

    private function formData(Sublease $s, Land $land, $leases): array
    {
        $info = $leases->mapWithKeys(fn ($L) => [$L->id => [
            'code' => $L->code, 'area' => (float) $L->leased_area_acres, 'mw' => (float) $L->leased_capacity_mw,
            'areaFree' => $this->cap->areaFree($L, $s->id), 'mwFree' => $this->cap->mwFree($L, $L->lease_start_date, $L->lease_end_date, $s->id),
            'start' => $L->lease_start_date->toDateString(), 'end' => $L->lease_end_date->toDateString(),
        ]]);

        return ['s' => $s, 'land' => $land->loadMissing('compliance'), 'leaseInfo' => $info,
            'leaseOpts' => $leases->mapWithKeys(fn ($L) => [$L->id => $L->code.' · '.fmt_num($L->leased_area_acres).' ac · '.CapacityService::fmt((float) $L->leased_capacity_mw).' MW · '.$L->lease_status])->all()];
    }

    /** Leased ↔ Subleased follows whether an active sublease exists (Mortgaged / Dropped are left alone). */
    private function syncLandStatus(Land $land): void
    {
        $land->refresh();
        $has = $land->subleases()->where('status', 'Active')->exists();
        if ($has && $land->land_status === 'Leased') {
            $land->update(['land_status' => 'Subleased']);
        } elseif (! $has && $land->land_status === 'Subleased') {
            $land->update(['land_status' => 'Leased']);
        }
    }
}
