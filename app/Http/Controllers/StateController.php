<?php

namespace App\Http\Controllers;

use App\Http\Requests\StateRequest;
use App\Models\Park;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index(Request $request)
    {
        $q = State::withCount(['cities', 'plots'])
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('code', 'like', "%$s%")))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->orderBy('name');

        if ($export = $this->maybeExport('state', 'State Master', ['State', 'Code', 'Cities', 'Survey numbers', 'Status'],
            $q->get()->map(fn ($s) => [$s->name, $s->code, $s->cities_count, $s->plots_count, $s->status]))) {
            return $export;
        }

        return view('masters.states.index', ['rows' => $q->paginate(config('solar.per_page'))->withQueryString(), 'total' => State::count()]);
    }

    public function create()
    {
        return $this->drawer('Add state', 'STATES', 'masters.states.form', ['state' => new State(['status' => 'Active'])], module: 'state');
    }

    public function store(StateRequest $request)
    {
        $state = State::create($request->validated());

        return $this->ok("State {$state->name} saved");
    }

    public function edit(State $state)
    {
        $ro = ! $this->canEdit('state');

        return $this->drawer(($ro ? 'View' : 'Edit').' state', 'STATES · '.$state->name, 'masters.states.form', compact('state'), $ro, 'state');
    }

    public function update(StateRequest $request, State $state)
    {
        $state->update($request->validated());

        return $this->ok("State {$state->name} saved");
    }

    public function destroy(State $state)
    {
        if ($state->cities()->exists()) {
            return $this->refuse("{$state->name} has cities. Delete them first.");
        }
        if ($state->plots()->withTrashed()->exists() || Park::where('state_id', $state->id)->exists()) {
            return $this->refuse("{$state->name} is used by parks or survey numbers.");
        }
        $state->delete();

        return $this->ok("{$state->name} deleted");
    }
}
