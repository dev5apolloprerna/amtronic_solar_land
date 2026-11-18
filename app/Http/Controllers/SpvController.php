<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpvRequest;
use App\Models\Park;
use App\Models\Party;
use App\Models\Spv;
use Illuminate\Http\Request;

class SpvController extends Controller
{
    public function index(Request $request)
    {
        $q = Spv::visibleTo()->with(['park.city', 'park.state', 'party'])->withCount('plots')
            ->withSum(['leases as leased_mw' => fn ($l) => $l->where('status', 'Active')], 'leased_capacity_mw')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('spv_name', 'like', "%$s%")->orWhere('spv_code', 'like', "%$s%")))
            ->when($request->park_id, fn ($q, $v) => $q->where('park_id', $v))
            ->when($request->project_status, fn ($q, $v) => $q->where('project_status', $v))
            ->orderBy('spv_code');

        if ($export = $this->maybeExport('spv', 'SPV Master', ['Code', 'SPV', 'Park', 'Owning company', 'CIN', 'Technology', 'Capacity MWp', 'Leased MW', 'Survey nos.', 'Project status', 'Status'],
            $q->get()->map(fn ($s) => [$s->spv_code, $s->spv_name, $s->park->park_code, $s->party?->party_name, $s->cin, $s->technology, (float) $s->capacity_mwp,
                (float) $s->leased_mw, $s->plots_count, $s->project_status, $s->status]))) {
            return $export;
        }

        return view('masters.spvs.index', [
            'rows' => $q->paginate(config('solar.per_page'))->withQueryString(),
            'total' => Spv::visibleTo()->count(),
            'parks' => Park::orderBy('park_code')->pluck('park_code', 'id'),
        ]);
    }

    public function create()
    {
        return $this->drawer('Add SPV', 'SPVS', 'masters.spvs.form', $this->formData(new Spv(['status' => 'Active', 'technology' => 'Solar PV', 'project_status' => 'Development'])), module: 'spv');
    }

    public function store(SpvRequest $request)
    {
        $data = $request->validated();
        $spv = new Spv($data);
        $spv->spv_code = $data['spv_code'];
        $spv->save();
        // Non-super creators get access to the SPV they created.
        if (! auth()->user()->isSuper()) {
            auth()->user()->spvs()->syncWithoutDetaching([$spv->id]);
        }

        return $this->ok("SPV {$spv->spv_code} saved");
    }

    public function edit(Spv $spv)
    {
        abort_unless(auth()->user()->canAccessSpv($spv->id), 403);
        $ro = ! $this->canEdit('spv');

        return $this->drawer(($ro ? 'View' : 'Edit').' SPV', 'SPVS · '.$spv->spv_code, 'masters.spvs.form', $this->formData($spv), $ro, 'spv');
    }

    public function update(SpvRequest $request, Spv $spv)
    {
        abort_unless(auth()->user()->canAccessSpv($spv->id), 403);
        if ((int) $request->park_id !== $spv->park_id && $spv->plots()->withTrashed()->exists()) {
            return $this->refuse("{$spv->spv_code} already has survey numbers in park {$spv->park->park_code}. Its park cannot change.");
        }
        $spv->update($request->validated());

        return $this->ok("SPV {$spv->spv_code} saved");
    }

    public function destroy(Spv $spv)
    {
        abort_unless(auth()->user()->canAccessSpv($spv->id), 403);
        if ($spv->plots()->withTrashed()->exists() || $spv->leases()->withTrashed()->exists()) {
            return $this->refuse("{$spv->spv_code} has survey numbers or leases. Delete or move them first.");
        }
        $spv->delete();

        return $this->ok("SPV {$spv->spv_code} deleted");
    }

    private function formData(Spv $spv): array
    {
        return [
            'spv' => $spv,
            'parks' => Park::where('status', 'Active')->orWhere('id', $spv->park_id)->orderBy('park_code')->get()->mapWithKeys(fn ($p) => [$p->id => "$p->park_code · $p->park_name"]),
            'companies' => Party::where('party_type', 'Company')->where(fn ($q) => $q->where('status', 'Active')->orWhere('id', $spv->party_id))
                ->orderBy('party_name')->get()->mapWithKeys(fn ($p) => [$p->id => "$p->party_name ($p->party_code)"]),
        ];
    }
}
