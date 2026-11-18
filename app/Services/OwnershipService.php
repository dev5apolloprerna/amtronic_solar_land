<?php

namespace App\Services;

use App\Models\Plot;
use App\Models\PlotOwnershipShare;
use App\Models\PlotOwnershipVersion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Landowner shares per survey number, versioned.
 * Draft may total <= 100%. Finalise needs exactly 100% and supersedes the previous version.
 */
class OwnershipService
{
    public const EPS = 0.0001;

    public function __construct(private RentAllocationService $allocations) {}

    /** The open draft, or a new draft copied from the current finalised version. */
    public function draftFor(Plot $plot): PlotOwnershipVersion
    {
        if ($draft = $plot->draftOwnership()->first()) {
            return $draft;
        }

        return DB::transaction(function () use ($plot) {
            $current = $plot->currentOwnership()->with('shares')->first();
            $draft = PlotOwnershipVersion::create([
                'plot_id' => $plot->id,
                'version_no' => (int) PlotOwnershipVersion::where('plot_id', $plot->id)->max('version_no') + 1,
                'status' => 'Draft',
            ]);
            foreach ($current?->shares ?? [] as $share) {
                $draft->shares()->create(['party_id' => $share->party_id, 'share_percent' => $share->share_percent]);
            }
            $draft->recalcTotal();

            return $draft;
        });
    }

    public function saveShare(PlotOwnershipVersion $version, int $partyId, float $percent, ?PlotOwnershipShare $existing = null): PlotOwnershipShare
    {
        $this->assertDraft($version);
        if ($percent <= 0 || $percent > 100) {
            throw ValidationException::withMessages(['share_percent' => 'Share must be greater than 0 and at most 100%.']);
        }

        return DB::transaction(function () use ($version, $partyId, $percent, $existing) {
            $dupe = $version->shares()->where('party_id', $partyId)->when($existing, fn ($q) => $q->whereKeyNot($existing->id))->exists();
            if ($dupe) {
                throw ValidationException::withMessages(['party_id' => 'This landowner is already on this survey number. Edit their share instead.']);
            }
            $others = (float) $version->shares()->when($existing, fn ($q) => $q->whereKeyNot($existing->id))->lockForUpdate()->sum('share_percent');
            if ($others + $percent > 100 + self::EPS) {
                throw ValidationException::withMessages(['share_percent' => sprintf('Total ownership would be %s%%. Other owners already have %s%%, so this share can be at most %s%%.',
                    rtrim(rtrim(number_format($others + $percent, 4), '0'), '.'), rtrim(rtrim(number_format($others, 4), '0'), '.'), rtrim(rtrim(number_format(100 - $others, 4), '0'), '.'))]);
            }
            $share = $existing ?? new PlotOwnershipShare(['ownership_version_id' => $version->id]);
            $share->fill(['party_id' => $partyId, 'share_percent' => $percent])->save();
            $version->recalcTotal();

            return $share;
        });
    }

    public function removeShare(PlotOwnershipShare $share): void
    {
        $version = $share->version;
        $this->assertDraft($version);
        $share->delete();
        $version->recalcTotal();
    }

    public function finalise(PlotOwnershipVersion $version, string $effectiveFrom, ?string $reason): void
    {
        $this->assertDraft($version);
        $total = (float) $version->shares()->sum('share_percent');
        if (abs($total - 100) > self::EPS) {
            throw ValidationException::withMessages(['effective_from' => sprintf('Shares total %s%%. They must add up to exactly 100%% before finalising.', rtrim(rtrim(number_format($total, 4), '0'), '.'))]);
        }
        $from = Carbon::parse($effectiveFrom)->startOfDay();

        DB::transaction(function () use ($version, $from, $reason) {
            $current = PlotOwnershipVersion::where('plot_id', $version->plot_id)->where('status', 'Finalised')->whereNull('effective_to')->lockForUpdate()->first();
            if ($current && $from->lte($current->effective_from)) {
                throw ValidationException::withMessages(['effective_from' => 'Effective date must be after '.$current->effective_from->format('d M Y').', when the current ownership started.']);
            }
            if ($current) {
                $current->update(['status' => 'Superseded', 'effective_to' => $from->copy()->subDay()]);
            }
            $version->update([
                'status' => 'Finalised',
                'effective_from' => $from,
                'change_reason' => $reason,
                'finalised_by' => auth()->id(),
                'finalised_at' => now(),
            ]);
            $version->recalcTotal();
            $this->allocations->reallocateFrom($version->plot, $from);
        });
    }

    public function discard(PlotOwnershipVersion $version): void
    {
        $this->assertDraft($version);
        $version->delete();
    }

    /** Version in force on a date (Finalised or Superseded). */
    public function versionOn(int $plotId, Carbon $date): ?PlotOwnershipVersion
    {
        return PlotOwnershipVersion::where('plot_id', $plotId)
            ->whereIn('status', ['Finalised', 'Superseded'])
            ->whereDate('effective_from', '<=', $date)
            ->where(fn ($q) => $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $date))
            ->orderByDesc('effective_from')
            ->with('shares')
            ->first();
    }

    private function assertDraft(PlotOwnershipVersion $version): void
    {
        if (! $version->isDraft()) {
            throw ValidationException::withMessages(['share_percent' => 'Only a draft ownership version can be changed. Start a new version to change finalised shares.']);
        }
    }
}
