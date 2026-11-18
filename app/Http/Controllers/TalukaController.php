<?php

namespace App\Http\Controllers;

use App\Http\Requests\TalukaRequest;
use App\Models\City;
use App\Models\Park;
use App\Models\State;
use App\Models\Taluka;
use Illuminate\Http\Request;

class TalukaController extends Controller
{
    public function index(Request $request)
    {
        $q = Taluka::with('city.state')->withCount('plots')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('code', 'like', "%$s%")))
            ->when($request->state_id, fn ($q, $v) => $q->whereHas('city', fn ($c) => $c->where('state_id', $v)))
            ->when($request->city_id, fn ($q, $v) => $q->where('city_id', $v))
            ->orderBy('name');

        if ($export = $this->maybeExport('taluka', 'Taluka Master', ['Taluka', 'Code', 'City', 'State', 'Survey numbers', 'Status'],
            $q->get()->map(fn ($t) => [$t->name, $t->code, $t->city->name, $t->city->state->name, $t->plots_count, $t->status]))) {
            return $export;
        }

        return view('masters.talukas.index', [
            'rows' => $q->paginate(config('solar.per_page'))->withQueryString(),
            'total' => Taluka::count(),
            'states' => State::orderBy('name')->pluck('name', 'id'),
            'cities' => City::when($request->state_id, fn ($q, $v) => $q->where('state_id', $v))->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create()
    {
        return $this->drawer('Add taluka', 'TALUKAS', 'masters.talukas.form', $this->formData(new Taluka(['status' => 'Active'])), module: 'taluka');
    }

    public function store(TalukaRequest $request)
    {
        $taluka = Taluka::create($request->validated());

        return $this->ok("Taluka {$taluka->name} saved");
    }

    public function edit(Taluka $taluka)
    {
        $ro = ! $this->canEdit('taluka');

        return $this->drawer(($ro ? 'View' : 'Edit').' taluka', 'TALUKAS · '.$taluka->name, 'masters.talukas.form', $this->formData($taluka), $ro, 'taluka');
    }

    public function update(TalukaRequest $request, Taluka $taluka)
    {
        $taluka->update($request->validated());

        return $this->ok("Taluka {$taluka->name} saved");
    }

    public function destroy(Taluka $taluka)
    {
        if ($taluka->plots()->withTrashed()->exists() || Park::where('taluka_id', $taluka->id)->exists()) {
            return $this->refuse("{$taluka->name} is used by parks or survey numbers.");
        }
        $taluka->delete();

        return $this->ok("{$taluka->name} deleted");
    }

    private function formData(Taluka $taluka): array
    {
        $stateId = $taluka->city?->state_id;

        return [
            'taluka' => $taluka,
            'stateId' => $stateId,
            'states' => State::where('status', 'Active')->orderBy('name')->get()->mapWithKeys(fn ($s) => [$s->id => "$s->name ($s->code)"]),
            'cities' => $stateId ? City::where('state_id', $stateId)->orderBy('name')->get()->mapWithKeys(fn ($c) => [$c->id => "$c->name ($c->code)"]) : collect(),
        ];
    }
}
