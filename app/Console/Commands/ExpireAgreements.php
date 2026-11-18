<?php

namespace App\Console\Commands;

use App\Models\Lease;
use App\Models\SubLease;
use Illuminate\Console\Command;

/** Marks leases and sub-leases Expired the day after their end date. Scheduled daily in routes/console.php. */
class ExpireAgreements extends Command
{
    protected $signature = 'solar:expire-agreements';

    protected $description = 'Set leases and sub-leases past their end date to Expired';

    public function handle(): int
    {
        $today = now()->toDateString();
        $subs = 0;
        SubLease::where('status', 'Active')->whereDate('end_date', '<', $today)->each(function (SubLease $s) use (&$subs) {
            $s->update(['status' => 'Expired']);
            $subs++;
        });
        $leases = 0;
        Lease::where('status', 'Active')->whereDate('lease_end_date', '<', $today)->each(function (Lease $l) use (&$leases) {
            $l->update(['status' => 'Expired']);
            $leases++;
        });
        $this->info("$leases leases and $subs sub-leases marked Expired.");

        return self::SUCCESS;
    }
}
