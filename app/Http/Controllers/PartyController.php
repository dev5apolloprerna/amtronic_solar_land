<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartyRequest;
use App\Models\Party;
use App\Models\PlotOwnershipShare;
use App\Models\State;
use App\Support\LocationOptions;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $q = Party::with(['state', 'city', 'taluka'])
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('party_name', 'like', "%$s%")->orWhere('party_code', 'like', "%$s%")
                ->orWhere('pan', 'like', "%$s%")->orWhere('mobile', 'like', "%$s%")->orWhere('contact_person', 'like', "%$s%")))
            ->when($request->party_type, fn ($q, $v) => $q->where('party_type', $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->orderBy('party_name');

        if ($export = $this->maybeExport('party', 'Party Master', ['Code', 'Party', 'Type', 'Contact', 'Mobile', 'Email', 'Address', 'Taluka', 'City', 'State', 'PAN', 'GST', 'Status'],
            $q->get()->map(fn ($p) => [$p->party_code, $p->party_name, $p->party_type, $p->contact_person, $p->mobile, $p->email, $p->address,
                $p->taluka?->name, $p->city?->name, $p->state?->name, $p->pan, $p->gst_no, $p->status]))) {
            return $export;
        }

        return view('masters.parties.index', ['rows' => $q->paginate(config('solar.per_page'))->withQueryString(), 'total' => Party::count()]);
    }

    public function create(Request $request)
    {
        $party = new Party(['status' => 'Active', 'party_type' => $request->query('type', 'Landowner')]);

        return $this->drawer('Add party', 'PARTIES', 'masters.parties.form', ['party' => $party] + LocationOptions::for($party), module: 'party');
    }

    public function store(PartyRequest $request)
    {
        $party = new Party($request->payload());
        $party->party_code = Party::nextCode();
        $party->save();

        return $this->ok("{$party->party_name} saved as {$party->party_code}");
    }

    public function edit(Party $party)
    {
        $ro = ! $this->canEdit('party');

        return $this->drawer(($ro ? 'View' : 'Edit').' party', 'PARTIES · '.$party->party_code, 'masters.parties.form', ['party' => $party] + LocationOptions::for($party), $ro, 'party');
    }

    public function update(PartyRequest $request, Party $party)
    {
        $party->update($request->payload());

        return $this->ok("{$party->party_name} saved");
    }

    public function destroy(Party $party)
    {
        $uses = [];
        if (PlotOwnershipShare::where('party_id', $party->id)->exists()) {
            $uses[] = 'landowner shares';
        }
        if ($party->subLeases()->withTrashed()->exists()) {
            $uses[] = 'sub-leases';
        }
        if ($party->spvs()->exists()) {
            $uses[] = 'SPVs';
        }
        if ($party->rentAllocations()->exists()) {
            $uses[] = 'rent allocations';
        }
        if ($uses) {
            return $this->refuse("{$party->party_name} is referenced by ".implode(', ', $uses).'. Set the party to Inactive instead.');
        }
        $party->delete();

        return $this->ok("{$party->party_name} deleted");
    }
}
