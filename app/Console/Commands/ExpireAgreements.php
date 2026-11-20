<?php

namespace App\Console\Commands;

use App\Models\Lease;
use App\Models\Sublease;
use Illuminate\Console\Command;

/** Leases past their end date become Expired; subleases past their end date become Terminated. Runs daily (routes/console.php). */
class ExpireAgreements extends Command
{
    protected $signature = 'solar:expire-agreements';

    protected $description = 'Expire leases and terminate subleases that are past their end date';

    public function handle(): int
    {
        $today = now()->toDateString();
        $subs = 0;
        Sublease::where('status', 'Active')->whereDate('sublease_end', '<', $today)->each(function (Sublease $s) use (&$subs) {
            $s->update(['status' => 'Terminated']);
            $subs++;
        });
        $leases = 0;
        Lease::where('lease_status', 'Active')->whereDate('lease_end_date', '<', $today)->each(function (Lease $l) use (&$leases) {
            $l->update(['lease_status' => 'Expired']);
            $leases++;
        });
        $this->info("$leases leases marked Expired and $subs subleases marked Terminated.");

        return self::SUCCESS;
    }
}
