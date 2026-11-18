<?php

namespace App\Http\Controllers;

use App\Models\Party;
use App\Models\Plot;
use App\Models\PlotOwnershipShare;
use App\Models\PlotOwnershipVersion;
use App\Services\OwnershipService;
use Illuminate\Http\Request;

/** Landowners of a survey number with % shares, versioned (Draft → Finalised → Superseded). */
class OwnershipController extends Controller
{
    public function __construct(private OwnershipService $ownership) {}

    public function startDraft(Plot $plot)
    {
        $this->authorizePlot($plot);
        $draft = $this->ownership->draftFor($plot);

        return $this->ok("Draft v{$draft->version_no} ready · add landowners and their shares", route('plots.show', [$plot, 'tab' => 'owners', 'ver' => $draft->id]));
    }

    public function createShare(PlotOwnershipVersion $version)
    {
        $this->authorizePlot($version->plot);

        return $this->drawer('Add landowner', 'PLOT_OWNERSHIP_SHARES · '.$version->plot->land_ref_no.' v'.$version->version_no, 'ownership.share-form',
            $this->shareData($version, new PlotOwnershipShare()), module: 'ownership');
    }

    public function storeShare(Request $request, PlotOwnershipVersion $version)
    {
        $this->authorizePlot($version->plot);
        $data = $this->validateShare($request);
        $share = $this->ownership->saveShare($version, (int) $data['party_id'], (float) $data['share_percent']);

        return $this->ok($share->party->party_name.' added with '.fmt_num($share->share_percent, 4).'%', route('plots.show', [$version->plot_id, 'tab' => 'owners', 'ver' => $version->id]));
    }

    public function editShare(PlotOwnershipShare $share)
    {
        $version = $share->version;
        $this->authorizePlot($version->plot);

        return $this->drawer('Edit landowner share', 'PLOT_OWNERSHIP_SHARES · '.$version->plot->land_ref_no.' v'.$version->version_no, 'ownership.share-form',
            $this->shareData($version, $share), ! $version->isDraft(), 'ownership');
    }

    public function updateShare(Request $request, PlotOwnershipShare $share)
    {
        $version = $share->version;
        $this->authorizePlot($version->plot);
        $data = $this->validateShare($request);
        $this->ownership->saveShare($version, (int) $data['party_id'], (float) $data['share_percent'], $share);

        return $this->ok('Share saved', route('plots.show', [$version->plot_id, 'tab' => 'owners', 'ver' => $version->id]));
    }

    public function destroyShare(PlotOwnershipShare $share)
    {
        $version = $share->version;
        $this->authorizePlot($version->plot);
        $name = $share->party->party_name;
        $this->ownership->removeShare($share);

        return $this->ok("$name removed from draft v{$version->version_no}", route('plots.show', [$version->plot_id, 'tab' => 'owners', 'ver' => $version->id]));
    }

    public function finaliseForm(PlotOwnershipVersion $version)
    {
        $this->authorizePlot($version->plot);
        $current = $version->plot->currentOwnership;
        $lease = $version->plot->activeLease;

        return $this->drawer('Finalise ownership v'.$version->version_no, 'PLOT_OWNERSHIP_VERSIONS · '.$version->plot->land_ref_no, 'ownership.finalise-form',
            compact('version', 'current', 'lease'), module: 'ownership', saveLabel: 'Finalise');
    }

    public function finalise(Request $request, PlotOwnershipVersion $version)
    {
        $this->authorizePlot($version->plot);
        $data = $request->validate([
            'effective_from' => ['required', 'date'],
            'change_reason' => [$version->version_no > 1 ? 'required' : 'nullable', 'string', 'max:255'],
        ], ['change_reason.required' => 'Give a reason for the ownership change, e.g. inheritance or sale.']);
        $this->ownership->finalise($version, $data['effective_from'], $data['change_reason'] ?? null);

        return $this->ok("Ownership v{$version->version_no} finalised from ".fmt_date($data['effective_from']).' · unpaid rent re-allocated', route('plots.show', [$version->plot_id, 'tab' => 'owners']));
    }

    public function discard(PlotOwnershipVersion $version)
    {
        $this->authorizePlot($version->plot);
        $plotId = $version->plot_id;
        $this->ownership->discard($version);

        return $this->ok('Draft discarded', route('plots.show', [$plotId, 'tab' => 'owners']));
    }

    private function validateShare(Request $request): array
    {
        return $request->validate([
            'party_id' => ['required', 'exists:parties,id'],
            'share_percent' => ['required', 'numeric', 'gt:0', 'max:100'],
        ], ['party_id.required' => 'Choose the landowner.', 'share_percent.gt' => 'Share must be greater than 0%.']);
    }

    private function shareData(PlotOwnershipVersion $version, PlotOwnershipShare $share): array
    {
        $others = (float) $version->shares()->when($share->exists, fn ($q) => $q->whereKeyNot($share->id))->sum('share_percent');
        $taken = $version->shares()->when($share->exists, fn ($q) => $q->whereKeyNot($share->id))->pluck('party_id')->all();

        return [
            'version' => $version,
            'share' => $share,
            'others' => $others,
            'owners' => Party::where('party_type', 'Landowner')->where(fn ($q) => $q->where('status', 'Active')->orWhere('id', $share->party_id))
                ->whereNotIn('id', $taken)->orderBy('party_name')->get()->mapWithKeys(fn ($p) => [$p->id => "$p->party_name · $p->party_code".($p->pan ? " · $p->pan" : '')]),
        ];
    }

    private function authorizePlot(Plot $plot): void
    {
        abort_unless(auth()->user()->canAccessSpv($plot->spv_id), 403);
    }
}
