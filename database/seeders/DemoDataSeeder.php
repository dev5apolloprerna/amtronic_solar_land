<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\ComplianceRecord;
use App\Models\Lease;
use App\Models\Park;
use App\Models\Party;
use App\Models\PlotGridConnectivity;
use App\Models\RentPayment;
use App\Models\RentSchedule;
use App\Models\Role;
use App\Models\Spv;
use App\Models\State;
use App\Models\SubLease;
use App\Models\Taluka;
use App\Models\User;
use App\Services\AuditService;
use App\Services\LandRefService;
use App\Services\OwnershipService;
use App\Services\RentScheduleService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Sample data that walks through the spec's examples:
 *  - Survey 125/2 with owners A 50%, B 30%, C 20%
 *  - A 200 MW lease sub-leased 20 + 30 + 50 MW to three parties → 100 MW remaining
 * All business rules run through the same services the screens use.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        AuditService::muted(fn () => $this->seed());
    }

    private function seed(): void
    {
        $refs = app(LandRefService::class);
        $own = app(OwnershipService::class);
        $sched = app(RentScheduleService::class);

        $loc = fn (string $st, string $ct, string $tk) => [
            'state_id' => $s = State::where('code', $st)->value('id'),
            'city_id' => $c = City::where('state_id', $s)->where('code', $ct)->value('id'),
            'taluka_id' => Taluka::where('city_id', $c)->where('code', $tk)->value('id'),
        ];
        $gj = $loc('GJ', 'BHV', 'TLJ');
        $mh = $loc('MH', 'SLP', 'MHL');

        /* parties */
        $party = function (array $a) {
            $p = new Party($a + ['status' => 'Active', 'address' => $a['address'] ?? 'At & Post', 'mobile' => $a['mobile'] ?? '98250'.random_int(10000, 99999)]);
            $p->party_code = Party::nextCode();
            $p->save();

            return $p;
        };
        $dev = $party(['party_name' => 'Sample Solar Developer Pvt Ltd', 'party_type' => 'Company', 'pan' => 'AAKCS1234P', 'gst_no' => '24AAKCS1234P1Z5', 'contact_person' => 'Project Head'] + $gj);
        $ownerA = $party(['party_name' => 'Rameshbhai K. Patel', 'party_type' => 'Landowner', 'pan' => 'AKLPP4821M', 'aadhaar_last4' => '4521', 'bank_account_no' => '33010012345678', 'bank_ifsc' => 'BARB0TALAJA', 'bank_name_branch' => 'Bank of Baroda, Talaja', 'address' => 'At & Post Trapaj'] + $gj);
        $ownerB = $party(['party_name' => 'Savitaben R. Patel', 'party_type' => 'Landowner', 'pan' => 'BXRPP1190D', 'aadhaar_last4' => '7310', 'bank_account_no' => '33010012345679', 'bank_ifsc' => 'BARB0TALAJA', 'bank_name_branch' => 'Bank of Baroda, Talaja', 'address' => 'At & Post Trapaj'] + $gj);
        $ownerC = $party(['party_name' => 'Bhikhabhai N. Rathod', 'party_type' => 'Landowner', 'pan' => 'BQRPR7710K', 'aadhaar_last4' => '9073', 'address' => 'At & Post Trapaj'] + $gj);
        $ownerD = $party(['party_name' => 'Mahendrasinh J. Gohil', 'party_type' => 'Landowner', 'pan' => 'CJGPG2290H', 'bank_account_no' => '20077782001', 'bank_ifsc' => 'SBIN0003355', 'bank_name_branch' => 'SBI, Alang', 'address' => 'Darbar Gadh, Sosiya'] + $gj);
        $ownerE = $party(['party_name' => 'Pradyumansinh J. Gohil', 'party_type' => 'Landowner', 'pan' => 'CJGPG4417A', 'address' => 'Darbar Gadh, Sosiya'] + $gj);
        $ownerF = $party(['party_name' => 'Dattatray S. Jadhav', 'party_type' => 'Landowner', 'pan' => 'AFJPJ5532Q', 'bank_account_no' => '60041900419', 'bank_ifsc' => 'MAHB0000123', 'bank_name_branch' => 'Bank of Maharashtra, Mohol', 'address' => 'Jadhav Wasti, Penur'] + $mh);
        $ownerG = $party(['party_name' => 'Sunita D. Jadhav', 'party_type' => 'Landowner', 'pan' => 'BHJPJ8841R', 'address' => 'Jadhav Wasti, Penur'] + $mh);
        $subA = $party(['party_name' => 'Party A Power Traders LLP', 'party_type' => 'Sub-lessee', 'pan' => 'AAPFA1111A', 'contact_person' => 'Commercial Manager'] + $gj);
        $subB = $party(['party_name' => 'Party B Industries Ltd', 'party_type' => 'Sub-lessee', 'pan' => 'AABCB2222B', 'contact_person' => 'Energy Head'] + $gj);
        $subC = $party(['party_name' => 'Party C Green Energy Pvt Ltd', 'party_type' => 'Sub-lessee', 'pan' => 'AAKCC3333C', 'contact_person' => 'Director'] + $gj);
        $subD = $party(['party_name' => 'Sample Grid Services LLP', 'party_type' => 'Sub-lessee', 'pan' => 'AAPFS3391L'] + $mh);

        /* parks & SPVs */
        $bhv = Park::create(['park_code' => 'BHV', 'park_name' => 'Bhavnagar Solar Park', 'address' => 'Talaja–Sosiya road', 'total_land_area' => 1200, 'land_unit' => 'Acre', 'total_land_acres' => 1200,
            'installed_capacity_mw' => 250, 'description' => 'Sample park for the 200 MW sub-lease example.', 'status' => 'Active'] + $gj);
        $slp = Park::create(['park_code' => 'SLP', 'park_name' => 'Solapur Solar Park', 'total_land_area' => 260, 'land_unit' => 'Acre', 'total_land_acres' => 260,
            'installed_capacity_mw' => 75, 'status' => 'Active'] + $mh);
        $spv10 = Spv::create(['spv_code' => 'PGP10', 'spv_name' => 'Sample SPV Ten Pvt Ltd', 'park_id' => $bhv->id, 'party_id' => $dev->id, 'cin' => 'U40106GJ2023PTC123456', 'technology' => 'Solar PV', 'capacity_mwp' => 230, 'project_status' => 'Under construction', 'status' => 'Active']);
        $spv11 = Spv::create(['spv_code' => 'PGP11', 'spv_name' => 'Sample SPV Eleven Pvt Ltd', 'park_id' => $bhv->id, 'party_id' => $dev->id, 'technology' => 'Hybrid', 'capacity_mwp' => 20, 'project_status' => 'Development', 'status' => 'Active']);
        $spv12 = Spv::create(['spv_code' => 'PGP12', 'spv_name' => 'Sample SPV Twelve Pvt Ltd', 'park_id' => $slp->id, 'party_id' => $dev->id, 'technology' => 'Solar PV', 'capacity_mwp' => 75, 'project_status' => 'Development', 'status' => 'Active']);

        /* demo users (password Demo@12345) */
        $role = fn ($n) => Role::where('name', $n)->value('id');
        foreach ([['priya.land', 'Priya Desai', 'Land Manager', [$spv10, $spv11, $spv12]], ['kunal.fin', 'Kunal Mehta', 'Finance', [$spv10]], ['anita.audit', 'Anita Rao', 'Auditor', [$spv10, $spv11, $spv12]]] as [$un, $name, $r, $spvs]) {
            $u = User::updateOrCreate(['username' => $un], ['name' => $name, 'email' => "$un@example.com", 'password' => 'Demo@12345', 'role_id' => $role($r), 'is_active' => true]);
            $u->spvs()->sync(collect($spvs)->pluck('id'));
        }

        /* survey numbers (Land Reference Numbers generated by the service) */
        $plotBase = ['land_unit' => 'Acre', 'title_report' => 'Yes', 'title_dd_status' => 'Clear', 'lessor_capacity_verified' => 'Yes', 'encumbrance_status' => 'Nil',
            'na_application' => 'Yes', 'na_status' => 'Approved', 'increment_after_yrs' => 3, 'increment_pct' => 5, 'land_status' => 'Identified', 'sub_survey_no' => ''];
        $p1 = $refs->createPlot(array_merge($plotBase, $gj, ['park_id' => $bhv->id, 'spv_id' => $spv10->id, 'village' => 'Trapaj', 'survey_no' => '125', 'sub_survey_no' => '2', 'total_area' => 101.31, 'area_under_lease_acres' => 100, 'rate_per_acre' => 45000]));
        $p2 = $refs->createPlot(array_merge($plotBase, $gj, ['park_id' => $bhv->id, 'spv_id' => $spv10->id, 'village' => 'Sosiya', 'survey_no' => '205', 'sub_survey_no' => '1', 'total_area' => 50600, 'land_unit' => 'Sq.m', 'area_under_lease_acres' => 12.5, 'rate_per_acre' => 42000]));
        $p3 = $refs->createPlot(array_merge($plotBase, $gj, ['park_id' => $bhv->id, 'spv_id' => $spv11->id, 'village' => 'Trapaj', 'survey_no' => '118', 'total_area' => 8.2, 'area_under_lease_acres' => 8.2, 'rate_per_acre' => 45000,
            'title_dd_status' => 'In progress', 'encumbrance_status' => 'Under verification', 'lien_details' => 'Crop loan with district co-op bank, closure letter awaited', 'land_status' => 'ATL signed']));
        $p4 = $refs->createPlot(array_merge($plotBase, $mh, ['park_id' => $slp->id, 'spv_id' => $spv12->id, 'village' => 'Penur', 'survey_no' => '43', 'sub_survey_no' => '1', 'total_area' => 8.1, 'land_unit' => 'Hectare', 'area_under_lease_acres' => 20, 'rate_per_acre' => 38000, 'increment_pct' => 4]));

        PlotGridConnectivity::create(['plot_id' => $p1->id, 'connectivity_mw' => 200, 'substation' => 'Talaja 400 kV', 'aggregator' => 'Sample Aggregator A', 'tfr_confirmation' => 'Yes']);
        PlotGridConnectivity::create(['plot_id' => $p4->id, 'connectivity_mw' => 20, 'substation' => 'Mohol 132 kV', 'aggregator' => 'Sample Aggregator C', 'tfr_confirmation' => 'Yes']);

        /* ownership: survey 125/2 → A 50%, B 30%, C 20% (the spec example) */
        $setOwners = function ($plot, array $shares, string $from) use ($own) {
            $v = $own->draftFor($plot);
            foreach ($shares as [$party, $pct]) {
                $own->saveShare($v, $party->id, $pct);
            }
            $own->finalise($v, $from, null);
        };
        $setOwners($p1, [[$ownerA, 50], [$ownerB, 30], [$ownerC, 20]], '2024-04-01');
        $setOwners($p2, [[$ownerD, 51], [$ownerE, 49]], '2024-11-20');
        $setOwners($p3, [[$ownerC, 100]], '2025-01-01');
        $setOwners($p4, [[$ownerF, 50], [$ownerG, 50]], '2025-01-10');
        // p3 has an open draft to show the workflow (inheritance in progress)
        $draft = $own->draftFor($p3);
        $own->saveShare($draft, $ownerC->id, 60, $draft->shares()->first());

        /* leases */
        $lease = function ($plot, array $a) use ($sched) {
            $start = Carbon::parse($a['lease_start_date']);
            $months = $a['lease_duration_months'] ?? 348;
            $l = new Lease($a + ['lease_duration_months' => $months, 'land_unit' => 'Acre', 'rent_type' => 'Per acre', 'payment_frequency' => 'Annual', 'escalation_pct' => 5,
                'escalation_every_yrs' => 3, 'tds_applicable' => 'Yes', 'tds_rate' => 10, 'gst_applicable' => 'No', 'status' => 'Active', 'security_deposit' => 0]);
            $l->lease_no = Lease::nextNumber();
            $l->plot_id = $plot->id;
            $l->park_id = $plot->park_id;
            $l->spv_id = $plot->spv_id;
            $l->lease_end_date = $start->copy()->addMonthsNoOverflow($months)->subDay();
            $l->lease_area_acres = $l->total_lease_area;
            $l->annual_rent = RentScheduleService::annualRent($l->rent_type, (float) $l->rent_rate, (float) $l->lease_area_acres);
            $l->save();
            $plot->update(['land_status' => 'Leased']);
            $sched->generate($l);

            return $l;
        };
        $l1 = $lease($p1, ['lease_date' => '2024-03-22', 'atl_date' => '2024-01-18', 'registration_date' => '2024-03-22', 'lease_start_date' => '2024-04-01', 'total_lease_area' => 100,
            'leased_capacity_mw' => 200, 'rent_rate' => 45000, 'uni_number' => 'UNI-GJ-24-008812', 'sro_doc_no' => 'TLJ/1442/2024', 'agreement_no' => 'LA-PGP10-001', 'mutation_entry_no' => 'ME-3381', 'security_deposit' => 500000]);
        $l2 = $lease($p2, ['lease_start_date' => '2024-11-20', 'total_lease_area' => 12.5, 'leased_capacity_mw' => 30, 'rent_rate' => 42000, 'agreement_no' => 'LA-PGP10-002', 'payment_frequency' => 'Half-yearly']);
        $l4 = $lease($p4, ['lease_start_date' => '2025-01-10', 'total_lease_area' => 20, 'leased_capacity_mw' => 20, 'rent_rate' => 38000, 'escalation_pct' => 4, 'agreement_no' => 'LA-PGP12-001']);

        /* pay every instalment due before today, except the most recent one on L1 (stays overdue), with one part payment */
        $lastL1 = RentSchedule::where('lease_id', $l1->id)->whereDate('due_date', '<', now())->max('due_date');
        $utr = 40112345;
        RentSchedule::with('allocations')->whereDate('due_date', '<', now())->orderBy('due_date')->get()->each(function (RentSchedule $s) use ($l1, $lastL1, &$utr) {
            $isLast = $s->lease_id === $l1->id && $s->due_date->toDateString() === Carbon::parse($lastL1)->toDateString();
            foreach ($s->allocations as $i => $a) {
                if ($isLast && $i > 0) {
                    break; // only the first owner has been paid, and only half
                }
                $gross = $isLast ? round((float) $a->gross_amount / 2, 2) : (float) $a->gross_amount;
                $tds = $isLast ? round((float) $a->tds_amount / 2, 2) : (float) $a->tds_amount;
                $p = new RentPayment(['rent_allocation_id' => $a->id, 'payment_date' => $s->due_date->copy()->addDays(3), 'gross_amount' => $gross, 'tds_deducted' => $tds,
                    'tds_challan_no' => 'CHL'.(28100 + $utr % 1000), 'net_paid' => round($gross - $tds, 2), 'payment_mode' => 'NEFT', 'utr_reference' => 'UTIBR'.($utr++), 'sap_voucher_no' => '51000'.$utr % 10000]);
                $p->payment_no = RentPayment::nextNumber();
                $p->save();
                $a->refreshPaid();
            }
            $s->refreshStatus();
        });

        /* sub-leases on the 200 MW lease: 20 + 30 + 50 = 100 MW → 100 MW remaining */
        $sub = function (Lease $l, Party $p, float $mw, string $start, ?string $end, array $extra = []) {
            $s = new SubLease(['lease_id' => $l->id, 'party_id' => $p->id, 'capacity_mw' => $mw, 'start_date' => $start, 'end_date' => $end ?? $l->lease_end_date,
                'rent_frequency' => 'Annual', 'lender_noc_obtained' => 'Yes', 'status' => 'Active'] + $extra);
            $s->park_id = $l->park_id;
            $s->spv_id = $l->spv_id;
            $s->sub_lease_no = SubLease::nextNumber();
            app(\App\Services\CapacityService::class)->assertSubLeaseFits($l, $mw, Carbon::parse($s->start_date), Carbon::parse($s->end_date));
            $s->save();

            return $s;
        };
        $sub($l1, $subA, 20, '2025-01-01', null, ['rent_amount' => 2400000, 'agreement_no' => 'SLA-PGP10-001', 'payment_terms' => 'Annual in advance, 15 days credit']);
        $sub($l1, $subB, 30, '2025-04-01', null, ['rent_amount' => 3600000, 'agreement_no' => 'SLA-PGP10-002']);
        $sub($l1, $subC, 50, '2025-06-01', null, ['rent_amount' => 6000000, 'agreement_no' => 'SLA-PGP10-003']);
        $sub($l4, $subD, 20, '2025-03-01', null, ['rent_amount' => 800000, 'agreement_no' => 'SLA-PGP12-001']);
        $p1->update(['land_status' => 'Sub-leased']);
        $p4->update(['land_status' => 'Sub-leased']);

        /* compliance */
        ComplianceRecord::create(['plot_id' => $p1->id, 'mortgage_created' => 'Yes', 'mortgage_date' => '2025-03-12', 'lender_name' => 'Sample Lender Bank', 'cersai_reg_no' => '400012345678',
            'original_docs_custody' => 'With lender', 'custodian_name' => 'Sample Lender Bank, Ahmedabad', 'docs_deposit_date' => '2025-03-12', 'lender_noc_status' => 'Obtained',
            'pdc_open_items' => 2, 'target_closure_date' => now()->addMonths(2)->toDateString(), 'escalation_level' => 'L1 · Land head', 'remarks' => 'Mutation of mortgage charge pending in 7/12.']);
        ComplianceRecord::create(['plot_id' => $p2->id, 'mortgage_created' => 'No', 'original_docs_custody' => 'With company', 'custodian_name' => 'Corporate legal vault',
            'lender_noc_status' => 'Not required', 'pdc_open_items' => 3, 'target_closure_date' => now()->subMonth()->toDateString(), 'escalation_level' => 'L2 · CFO', 'remarks' => 'NA order copy and two heirship affidavits outstanding.']);
    }
}
