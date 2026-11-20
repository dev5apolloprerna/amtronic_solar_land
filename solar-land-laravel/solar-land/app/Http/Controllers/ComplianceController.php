<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LandAccess;
use App\Http\Requests\ComplianceRequest;
use App\Models\Compliance;
use App\Models\Land;

/** COMPLIANCE_TRACKER · one record per parcel (mortgage, original documents, lender NOC, PDC items) */
class ComplianceController extends Controller
{
    use LandAccess;

    public function create(Land $land)
    {
        $this->guardLand($land);
        if ($land->compliance()->exists()) {
            return $this->edit($land->compliance);
        }
        $c = new Compliance(['land_id' => $land->id, 'mortgage_created' => 'No', 'original_docs_custody' => 'With company', 'lender_noc_status' => 'Not required',
            'pdc_open_items' => 0, 'escalation_level' => 'L0 · None']);

        return $this->drawer('Add compliance record', 'COMPLIANCE_TRACKER · '.$land->land_ref_no, 'lands.forms.compliance', ['c' => $c, 'land' => $land], module: 'compliance');
    }

    public function store(ComplianceRequest $request, Land $land)
    {
        $this->guardLand($land);
        Compliance::create(['land_id' => $land->id] + $request->payload());
        $this->syncMortgaged($land);

        return $this->ok('Compliance record saved', $this->tabUrl($land, 'compliance'));
    }

    public function edit(Compliance $compliance)
    {
        $this->guardLand($compliance->land);
        $ro = ! $this->canEdit('compliance');

        return $this->drawer(($ro ? 'View' : 'Edit').' compliance record', 'COMPLIANCE_TRACKER · '.$compliance->code, 'lands.forms.compliance', ['c' => $compliance, 'land' => $compliance->land], $ro, 'compliance');
    }

    public function update(ComplianceRequest $request, Compliance $compliance)
    {
        $this->guardLand($compliance->land);
        $compliance->update($request->payload());
        $this->syncMortgaged($compliance->land);

        return $this->ok('Compliance record saved', $this->tabUrl($compliance->land, 'compliance'));
    }

    public function destroy(Compliance $compliance)
    {
        $land = $compliance->land;
        $this->guardLand($land);
        $compliance->delete();

        return $this->ok("{$compliance->code} deleted", $this->tabUrl($land, 'compliance'));
    }

    /** A mortgage on the parcel is shown as land status "Mortgaged" (template behaviour for leased parcels). */
    private function syncMortgaged(Land $land): void
    {
        $land->refresh();
        if ($land->compliance?->mortgage_created === 'Yes' && in_array($land->land_status, ['Leased', 'Subleased'], true)) {
            $land->update(['land_status' => 'Mortgaged']);
        }
    }
}
