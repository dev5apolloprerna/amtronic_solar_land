<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComplianceRequest;
use App\Models\ComplianceRecord;
use App\Models\Plot;
use Illuminate\Http\Request;

/** One compliance record per survey number: mortgage, CERSAI, custody of originals, lender NOC, open PDC items. */
class ComplianceController extends Controller
{
    public function index(Request $request)
    {
        $q = ComplianceRecord::visibleTo()->with(['plot.park', 'plot.spv'])->withCount('documents')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('lender_name', 'like', "%$s%")->orWhere('cersai_reg_no', 'like', "%$s%")
                ->orWhereHas('plot', fn ($p) => $p->where('land_ref_no', 'like', "%$s%"))))
            ->when($request->mortgage_created, fn ($q, $v) => $q->where('mortgage_created', $v))
            ->when($request->escalation_level, fn ($q, $v) => $q->where('escalation_level', $v))
            ->when($request->boolean('open'), fn ($q) => $q->where('pdc_open_items', '>', 0))
            ->latest('updated_at');

        if ($export = $this->maybeExport('compliance', 'Compliance Tracker', ['Land ref no.', 'Mortgage', 'Mortgage date', 'Lender', 'CERSAI no.', 'Originals with', 'Custodian', 'Lender NOC', 'Open PDC', 'Target closure', 'Escalation', 'Remarks'],
            $q->get()->map(fn ($c) => [$c->plot->land_ref_no, $c->mortgage_created, $c->mortgage_date?->format('Y-m-d'), $c->lender_name, $c->cersai_reg_no, $c->original_docs_custody, $c->custodian_name,
                $c->lender_noc_status, $c->pdc_open_items, $c->target_closure_date?->format('Y-m-d'), $c->escalation_level, $c->remarks]))) {
            return $export;
        }

        return view('compliance.index', ['rows' => $q->paginate(config('solar.per_page'))->withQueryString(), 'total' => ComplianceRecord::visibleTo()->count()]);
    }

    public function create(Request $request)
    {
        $rec = new ComplianceRecord(['plot_id' => $request->plot_id, 'mortgage_created' => 'No', 'original_docs_custody' => 'With company',
            'lender_noc_status' => 'Not required', 'pdc_open_items' => 0, 'escalation_level' => 'L0 · None']);

        return $this->drawer('Add compliance record', 'COMPLIANCE_RECORDS', 'compliance.form', $this->formData($rec), module: 'compliance');
    }

    public function store(ComplianceRequest $request)
    {
        $data = $request->validated();
        if (($data['mortgage_created'] ?? 'No') !== 'Yes') {
            $data['mortgage_date'] = null;
        }
        $rec = ComplianceRecord::create($data);
        if ($rec->mortgage_created === 'Yes') {
            $rec->plot->update(['land_status' => 'Mortgaged']);
        }

        return $this->ok('Compliance record saved for '.$rec->plot->land_ref_no);
    }

    public function edit(ComplianceRecord $compliance)
    {
        abort_unless(auth()->user()->canAccessSpv($compliance->plot->spv_id), 403);
        $ro = ! $this->canEdit('compliance');

        return $this->drawer(($ro ? 'View' : 'Edit').' compliance record', 'COMPLIANCE_RECORDS · '.$compliance->plot->land_ref_no, 'compliance.form', $this->formData($compliance), $ro, 'compliance');
    }

    public function update(ComplianceRequest $request, ComplianceRecord $compliance)
    {
        abort_unless(auth()->user()->canAccessSpv($compliance->plot->spv_id), 403);
        $data = $request->validated();
        if (($data['mortgage_created'] ?? 'No') !== 'Yes') {
            $data['mortgage_date'] = null;
        }
        $compliance->update($data);

        return $this->ok('Compliance record saved for '.$compliance->plot->land_ref_no);
    }

    public function destroy(ComplianceRecord $compliance)
    {
        abort_unless(auth()->user()->canAccessSpv($compliance->plot->spv_id), 403);
        if ($compliance->documents()->exists()) {
            return $this->refuse('This compliance record has evidence documents linked. Delete them first.');
        }
        $compliance->delete();

        return $this->ok('Compliance record deleted');
    }

    private function formData(ComplianceRecord $rec): array
    {
        return [
            'rec' => $rec,
            'plotOpts' => $rec->exists ? [] : Plot::visibleTo()->whereDoesntHave('compliance')->orderBy('land_ref_no')->pluck('land_ref_no', 'id')->all(),
        ];
    }
}
