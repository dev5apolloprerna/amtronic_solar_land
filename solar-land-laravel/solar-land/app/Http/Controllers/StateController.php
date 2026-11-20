<?php

namespace App\Http\Controllers;

use App\Http\Requests\StateRequest;
use App\Models\Park;
use App\Models\State;
use Illuminate\Http\Request;

/** STATE_MASTER */
class StateController extends Controller
{
    public function index(Request $request)
    {
        $q = State::withCount(['districts', 'lands'])
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('state_name', 'like', "%$s%")->orWhere('state_code', 'like', "%$s%")))
            ->orderBy('id');

        if ($e = $this->maybeExport('state', 'State Master', ['ID', 'State', 'Code', 'Districts', 'Land parcels'],
            $q->get()->map(fn ($r) => ['ST'.str_pad((string) $r->id, 2, '0', STR_PAD_LEFT), $r->state_name, $r->state_code, $r->districts_count, $r->lands_count]))) {
            return $e;
        }

        return view('masters.states.index', ['rows' => $q->paginate(config('solar.per_page'))->withQueryString(), 'total' => State::count()]);
    }

    public function create()
    {
        return $this->drawer('Add state', 'STATE_MASTER', 'masters.states.form', ['state' => new State()], module: 'state');
    }

    public function store(StateRequest $request)
    {
        $s = State::create($request->validated());

        return $this->ok('State saved');
    }

    public function edit(State $state)
    {
        $ro = ! $this->canEdit('state');

        return $this->drawer(($ro ? 'View' : 'Edit').' state', 'STATE_MASTER · '.$state->state_name, 'masters.states.form', compact('state'), $ro, 'state');
    }

    public function update(StateRequest $request, State $state)
    {
        $state->update($request->validated());

        return $this->ok('State saved');
    }

    public function destroy(State $state)
    {
        if ($state->districts()->exists()) {
            return $this->refuse("{$state->state_name} has districts. Delete them first.");
        }
        if (Park::where('state_id', $state->id)->exists() || $state->lands()->exists()) {
            return $this->refuse("{$state->state_name} is used by parks or survey numbers.");
        }
        $state->delete();

        return $this->ok("{$state->state_name} deleted");
    }
}
