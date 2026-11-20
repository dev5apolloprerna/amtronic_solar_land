<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\District;
use App\Models\Land;
use App\Models\Park;
use App\Models\RentSchedule;
use App\Models\Spv;
use App\Models\State;
use App\Services\LandRefService;
use App\Services\ShareService;
use Illuminate\Http\Request;

/** Small JSON endpoints for cascading selects and live previews in the forms. */
class LookupController extends Controller
{
    private function out(array $opts)
    {
        return response()->json(collect($opts)->map(fn ($l, $v) => ['id' => (string) $v, 'label' => $l])->values());
    }

    public function districts(Request $r)
    {
        return $this->out(District::where('state_id', (int) $r->state_id)->orderBy('id')->pluck('district_name', 'id')->all());
    }

    public function cities(Request $r)
    {
        return $this->out(City::where('district_id', (int) $r->district_id)->orderBy('id')->get()->mapWithKeys(fn ($c) => [$c->id => "$c->city_name · $c->city_type"])->all());
    }

    public function parks(Request $r)
    {
        return $this->out(Park::when($r->state_id, fn ($q, $v) => $q->where('state_id', $v))->orderBy('id')->get()->mapWithKeys(fn ($p) => [$p->id => "$p->park_code · $p->park_name"])->all());
    }

    public function spvs(Request $r)
    {
        return $this->out(Spv::visibleTo()->when($r->park_id, fn ($q, $v) => $q->where('park_id', $v))->orderBy('id')->get()->mapWithKeys(fn ($s) => [$s->id => "$s->spv_code · $s->spv_name"])->all());
    }

    /** Park → its state / district / city, so the land form can fill the location. */
    public function park(Park $park)
    {
        return response()->json(['state_id' => $park->state_id, 'district_id' => $park->district_id, 'city_id' => $park->city_id,
            'districts' => District::where('state_id', $park->state_id)->orderBy('id')->pluck('district_name', 'id'),
            'cities' => City::where('district_id', $park->district_id)->orderBy('id')->get()->mapWithKeys(fn ($c) => [$c->id => "$c->city_name · $c->city_type"])]);
    }

    /** Live preview of the land reference number on the new parcel page. */
    public function landRef(Request $r, LandRefService $refs)
    {
        $ref = $refs->build(Spv::find($r->spv_id), State::find($r->state_id), $r->park_id ? Park::find($r->park_id) : null, $r->parcel_code, $r->ref_year ?: now()->year);

        return response()->json(['ref' => $ref, 'exists' => $ref ? $refs->exists($ref) : false]);
    }

    /** Farmer-wise shares of an instalment for the payment form. */
    public function scheduleFarmers(RentSchedule $schedule, ShareService $shares)
    {
        abort_unless(auth()->user()->canAccessSpv($schedule->land->spv_id), 403);

        return response()->json($shares->forSchedule($schedule)->map(fn ($r) => [
            'id' => (string) $r['farmer']->id, 'label' => $r['farmer']->owner_name.' · '.fmt_num($r['pct']).'%',
            'share' => $r['share'], 'tds' => $r['tds'], 'paid' => $r['paid'], 'remaining' => $r['remaining'],
        ])->values());
    }

    public function entities(Request $r, Land $land)
    {
        abort_unless(auth()->user()->canAccessSpv($land->spv_id), 403);

        return $this->out(self::entityOptions($land, (string) $r->type));
    }

    /** Records of a parcel a document can be linked to. */
    public static function entityOptions(Land $land, string $type): array
    {
        return match ($type) {
            'Land' => [$land->id => $land->land_ref_no],
            'Survey' => $land->surveys()->get()->mapWithKeys(fn ($x) => [$x->id => "$x->code · $x->village · Survey $x->survey_plot_no"])->all(),
            'Farmer' => $land->farmers()->get()->mapWithKeys(fn ($x) => [$x->id => "$x->code · $x->owner_name"])->all(),
            'Lease' => $land->leases()->orderBy('id')->get()->mapWithKeys(fn ($x) => [$x->id => "$x->code · $x->lessor_name"])->all(),
            'Sublease' => $land->subleases()->orderBy('id')->get()->mapWithKeys(fn ($x) => [$x->id => "$x->code · $x->sublessee_name"])->all(),
            'Compliance' => $land->compliance()->get()->mapWithKeys(fn ($x) => [$x->id => $x->code])->all(),
            'Payment' => $land->payments()->with('farmer')->orderBy('id')->get()->mapWithKeys(fn ($x) => [$x->id => "$x->code · ".$x->farmer->owner_name.' · '.inr($x->gross_amount)])->all(),
            default => [],
        };
    }
}
