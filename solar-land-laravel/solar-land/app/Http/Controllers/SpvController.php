<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpvRequest;
use App\Models\Park;
use App\Models\Spv;
use App\Models\State;
use Illuminate\Http\Request;

/** SPV_MASTER */
class SpvController extends Controller
{
    public function index(Request $request)
    {
        $q = Spv::visibleTo()->with(['park.city', 'park.state'])->withCount('lands')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('spv_code', 'like', "%$s%")->orWhere('spv_name', 'like', "%$s%")))
            ->orderBy('id');

        if ($e = $this->maybeExport('spv', 'SPV Master', ['Code', 'SPV name', 'Park', 'City', 'State', 'Technology', 'MWp', 'Parcels', 'Status'],
            $q->get()->map(fn ($s) => [$s->spv_code, $s->spv_name, $s->park->park_code.' · '.$s->park->park_name, $s->park->city->city_name, $s->park->state->state_name,
                $s->technology, (float) $s->capacity_mwp, $s->lands_count, $s->status]))) {
            return $e;
        }

        return view('masters.spvs.index', ['rows' => $q->paginate(config('solar.per_page'))->withQueryString(), 'total' => Spv::visibleTo()->count()]);
    }

    public function create()
    {
        return $this->drawer('Add spv', 'SPV_MASTER', 'masters.spvs.form', $this->data(new Spv(['technology' => 'Solar PV', 'status' => 'Development'])), module: 'spv');
    }

    public function store(SpvRequest $request)
    {
        $d = $request->validated();
        $s = new Spv($d);
        $s->spv_code = $d['spv_code'];
        $s->save();
        if (! auth()->user()->isSuper()) {
            auth()->user()->spvs()->syncWithoutDetaching([$s->id]);
        }

        return $this->ok('SPV saved');
    }

    public function edit(Spv $spv)
    {
        abort_unless(auth()->user()->canAccessSpv($spv->id), 403);
        $ro = ! $this->canEdit('spv');

        return $this->drawer(($ro ? 'View' : 'Edit').' spv', 'SPV_MASTER · '.$spv->spv_code, 'masters.spvs.form', $this->data($spv), $ro, 'spv');
    }

    public function update(SpvRequest $request, Spv $spv)
    {
        abort_unless(auth()->user()->canAccessSpv($spv->id), 403);
        $spv->update($request->validated());

        return $this->ok('SPV saved');
    }

    public function destroy(Spv $spv)
    {
        abort_unless(auth()->user()->canAccessSpv($spv->id), 403);
        if ($spv->lands()->exists()) {
            return $this->refuse("{$spv->spv_code} has land parcels. Delete or move them first.");
        }
        $spv->delete();

        return $this->ok("{$spv->spv_code} deleted");
    }

    private function data(Spv $spv): array
    {
        return [
            'spv' => $spv,
            'states' => State::stateOptions(),
            'parks' => $spv->state_id ? Park::where('state_id', $spv->state_id)->orderBy('park_code')->get()->mapWithKeys(fn ($p) => [$p->id => "$p->park_code · $p->park_name"])->all() : [],
        ];
    }
}
