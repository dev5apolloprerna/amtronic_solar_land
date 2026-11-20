<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Compliance;
use App\Models\Document;
use App\Models\Land;
use App\Models\LandOwner;
use App\Models\LandSurvey;
use App\Models\Lease;
use App\Models\Park;
use App\Models\Payment;
use App\Models\RentSchedule;
use App\Models\Role;
use App\Models\Spv;
use App\Models\State;
use App\Models\Sublease;
use App\Models\User;
use App\Services\AuditService;
use App\Services\LandRefService;
use App\Services\RentScheduleService;
use App\Services\ShareService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Sample data of the HTML template: 2 parks, 3 SPVs, 4 land parcels, 8 survey numbers, 8 farmers,
 * 3 leases with generated rent schedules, payments for past instalments (LSE001's April 2026 instalment left overdue),
 * 2 subleases, 2 compliance records, 4 documents, 3 more users and a few audit rows.
 * MW: BHV park 250 MW, SLP park 75 MW; LSE002 leases 200 MW and SUB001 subleases 100 MW of it → 100 MW remaining (the spec example).
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        AuditService::muted(fn () => DB::transaction(fn () => $this->seed()));
    }

    private function seed(): void
    {
        $refs = app(LandRefService::class);
        $sched = app(RentScheduleService::class);
        $shares = app(ShareService::class);
        $sqm = config('solar.sqm_per_acre');

        $state = fn ($c) => State::where('state_code', $c)->first();
        $city = fn ($n) => City::where('city_name', $n)->first();
        $loc = fn ($c) => ['state_id' => $c->state_id, 'district_id' => $c->district_id, 'city_id' => $c->id];
        $GJ = $loc($city('Talaja'));
        $MH = $loc($city('Mohol'));

        /* parks */
        $park = function (string $code, array $a) {
            $p = new Park($a);
            $p->park_code = $code;
            $p->save();

            return $p;
        };
        $bhv = $park('BHV', ['park_name' => 'Bhavnagar Solar Park', 'total_area_acres' => 420, 'installed_capacity_mw' => 250] + $GJ);
        $slp = $park('SLP', ['park_name' => 'Solapur Solar Park', 'total_area_acres' => 260, 'installed_capacity_mw' => 75] + $MH);

        /* SPVs */
        $spv = function (string $code, Park $p, array $a) {
            $s = new Spv($a + ['park_id' => $p->id, 'state_id' => $p->state_id]);
            $s->spv_code = $code;
            $s->save();

            return $s;
        };
        $pgp10 = $spv('PGP10', $bhv, ['spv_name' => 'Sample SPV Ten Pvt Ltd', 'technology' => 'Solar PV', 'capacity_mwp' => 150, 'status' => 'Under construction']);
        $pgp12 = $spv('PGP12', $slp, ['spv_name' => 'Sample SPV Twelve Pvt Ltd', 'technology' => 'Solar PV', 'capacity_mwp' => 75, 'status' => 'Development']);
        $pgp11 = $spv('PGP11', $bhv, ['spv_name' => 'Sample SPV Eleven Pvt Ltd', 'technology' => 'Hybrid', 'capacity_mwp' => 100, 'status' => 'Development']);

        /* users (password Demo@123) with SPV access */
        $su = User::where('username', 'superadmin')->first();
        $su->spvs()->sync([$pgp10->id, $pgp12->id, $pgp11->id]);
        $su->forceFill(['last_login_at' => '2026-10-16 09:12:00', 'last_login_ip' => '10.20.4.11'])->save();
        foreach ([
            ['priya.land', 'Priya Desai', 'priya.desai@example.com', 'Land Manager', [$pgp10, $pgp12, $pgp11], '2026-10-15 17:40:00', '10.20.4.27'],
            ['kunal.fin', 'Kunal Mehta', 'kunal.mehta@example.com', 'Finance', [$pgp10], '2026-10-16 10:05:00', '10.20.6.42'],
            ['anita.audit', 'Anita Rao', 'anita.rao@example.com', 'Auditor', [$pgp10, $pgp12, $pgp11], '2026-10-09 11:20:00', '10.20.8.5'],
        ] as [$un, $name, $email, $role, $spvs, $last, $ip]) {
            $u = User::updateOrCreate(['username' => $un], ['name' => $name, 'email' => $email, 'password' => 'Demo@123', 'role_id' => Role::where('name', $role)->value('id'), 'is_active' => true]);
            $u->forceFill(['last_login_at' => $last, 'last_login_ip' => $ip])->save();
            $u->spvs()->sync(collect($spvs)->pluck('id'));
        }

        /* land parcels */
        $land = function (Spv $s, ?Park $p, array $g, string $parcel, int $year, array $a) use ($refs, $state, $sqm) {
            $l = new Land($a + $g + ['park_id' => $p?->id, 'spv_id' => $s->id, 'tfr_confirmation' => 'Yes', 'title_report' => 'Yes', 'lessor_capacity_verified' => 'Yes',
                'na_application' => 'Yes', 'increment_after_yrs' => 3, 'increment_pct' => 5]);
            $l->parcel_code = $parcel;
            $l->ref_year = $year;
            $l->area_acres_712 = round($a['area_sqm_712'] / $sqm, 2);
            $l->land_ref_no = $refs->build($s, State::find($g['state_id']), $p, $parcel, $year);
            $l->save();

            return $l;
        };
        $a1 = $land($pgp10, $bhv, $GJ, 'A1', 2024, ['area_sqm_712' => 50586, 'area_under_lease_acres' => 12.5, 'rate_per_acre' => 45000, 'connectivity_mw' => 5, 'substation' => 'Talaja 220 kV',
            'aggregator' => 'Sample Aggregator A', 'title_dd_status' => 'Clear', 'encumbrance_status' => 'Nil', 'na_status' => 'Approved', 'land_status' => 'Leased']);
        $a2 = $land($pgp10, $bhv, $GJ, 'A2', 2024, ['area_sqm_712' => 33184, 'area_under_lease_acres' => 8.2, 'rate_per_acre' => 45000, 'connectivity_mw' => 3, 'substation' => 'Talaja 220 kV',
            'aggregator' => 'Sample Aggregator A', 'title_report' => 'No', 'tfr_confirmation' => 'No', 'lessor_capacity_verified' => 'No', 'title_dd_status' => 'In progress',
            'encumbrance_status' => 'Under verification', 'na_application' => 'No', 'na_status' => 'Not applied', 'land_status' => 'ATL signed',
            'lien_details' => 'Crop loan with district co-op bank, ₹2.4 lakh, closure letter awaited']);
        $b1 = $land($pgp10, $bhv, $GJ, 'B1', 2024, ['area_sqm_712' => 410000, 'area_under_lease_acres' => 100, 'rate_per_acre' => 42000, 'connectivity_mw' => 200, 'substation' => 'Sosiya 400 kV',
            'aggregator' => 'Sample Aggregator B', 'title_dd_status' => 'Clear', 'encumbrance_status' => 'Encumbered', 'na_status' => 'Approved', 'land_status' => 'Mortgaged']);
        $m1 = $land($pgp12, $slp, $MH, 'A1', 2025, ['area_sqm_712' => 80937, 'area_under_lease_acres' => 20, 'rate_per_acre' => 38000, 'increment_pct' => 4, 'connectivity_mw' => 8,
            'substation' => 'Mohol 132 kV', 'aggregator' => 'Sample Aggregator C', 'title_dd_status' => 'Clear', 'encumbrance_status' => 'Nil', 'na_status' => 'Applied', 'land_status' => 'Subleased']);

        /* survey / plot numbers */
        $sv = fn (Land $l, array $g, string $village, string $no, float $area) => LandSurvey::create($g + ['land_id' => $l->id, 'village' => $village, 'survey_plot_no' => $no,
            'area_sqm' => $area, 'area_acres' => round($area / $sqm, 2)]);
        $s1 = $sv($a1, $GJ, 'Trapaj', '112/2', 50586);
        $sv($a2, $GJ, 'Trapaj', '118', 33184);
        foreach ([['205/1', 101171], ['205/2', 101171], ['205/3', 103829], ['206', 103829]] as [$no, $ar]) {
            $sv($b1, $GJ, 'Sosiya', $no, $ar);
        }
        $sv($m1, $MH, 'Penur', '43/1', 40468);
        $sv($m1, $MH, 'Penur', '43/2', 40469);

        /* farmers / owners (60/40, 100, 51/25/24, 50/50) */
        $own = fn (Land $l, $name, $pct, $pan, $aad, $voter, $bank, $branch) => LandOwner::create(['land_id' => $l->id, 'owner_name' => $name, 'ownership_pct' => $pct, 'owner_pan' => $pan,
            'aadhaar' => $aad, 'voter_id' => $voter ?: null, 'bank_ac' => $bank ?: null, 'bank_branch' => $branch ?: null]);
        $own($a1, 'Rameshbhai K. Patel', 60, 'AKLPP4821M', 'XXXX-XXXX-4521', 'GJ/14/112/0388', '200410013301', 'Bank of Baroda, Talaja');
        $own($a1, 'Savitaben R. Patel', 40, 'BXRPP1190D', 'XXXX-XXXX-7310', 'GJ/14/112/0389', '200410013302', 'Bank of Baroda, Talaja');
        $own($a2, 'Bhikhabhai N. Rathod', 100, 'BQRPR7710K', 'XXXX-XXXX-9073', '', '', '');
        $own($b1, 'Mahendrasinh J. Gohil', 51, 'CJGPG2290H', 'XXXX-XXXX-1186', 'GJ/14/205/1120', '30881207782', 'SBI, Alang');
        $own($b1, 'Pradyumansinh J. Gohil', 25, 'CJGPG4417A', 'XXXX-XXXX-2290', 'GJ/14/205/1121', '30881207790', 'SBI, Alang');
        $own($b1, 'Rajendrasinh J. Gohil', 24, 'CJGPG5528B', 'XXXX-XXXX-3361', 'GJ/14/205/1122', '30881207811', 'SBI, Alang');
        $own($m1, 'Dattatray S. Jadhav', 50, 'AFJPJ5532Q', 'XXXX-XXXX-6620', 'MH/42/043/0071', '60120080419', 'Bank of Maharashtra, Mohol');
        $own($m1, 'Sunita D. Jadhav', 50, 'BHJPJ8841R', 'XXXX-XXXX-5507', 'MH/42/043/0072', '60120080420', 'Bank of Maharashtra, Mohol');

        /* leases (rent schedule generated by the same service the screens use) */
        $lease = function (Land $l, array $g, array $a) use ($sched) {
            $L = new Lease($a + ['land_id' => $l->id, 'lessor_state_id' => $g['state_id'], 'lessor_district_id' => $g['district_id'], 'lessor_city_id' => $g['city_id'],
                'lease_duration_mths' => 348, 'rent_frequency' => 'Annual', 'escalation_pct' => 5, 'escalation_every_yrs' => 3, 'tds_applicable' => 'Yes', 'tds_rate' => 10,
                'gst_applicable' => 'No', 'lease_status' => 'Active', 'security_deposit' => 0]);
            $L->lease_end_date = RentScheduleService::endDate($L->lease_start_date->toDateString(), (int) $L->lease_duration_mths);
            $L->rent_amount = RentScheduleService::instalment((float) $L->leased_area_acres, (float) $L->rent_rate_per_acre, $L->rent_frequency);
            $L->save();
            $sched->generate($L);

            return $L;
        };
        $l1 = $lease($a1, $GJ, ['lessor_name' => 'Rameshbhai K. Patel', 'lessor_pan' => 'AKLPP4821M', 'lessor_address' => 'At & Post Trapaj', 'lessor_pincode' => '364140',
            'leased_area_acres' => 12.5, 'leased_capacity_mw' => 5, 'rent_rate_per_acre' => 45000, 'atl_date' => '2024-01-18', 'lease_date' => '2024-03-22', 'lease_start_date' => '2024-04-01',
            'uni_number' => 'UNI-GJ-24-008812', 'sro_doc_no' => 'TLJ/1442/2024', 'agreement_no' => 'LA-PGP10-001', 'mutation_entry_no' => 'ME-3381', 'security_deposit' => 100000]);
        $l2 = $lease($b1, $GJ, ['lessor_name' => 'Mahendrasinh J. Gohil', 'lessor_pan' => 'CJGPG2290H', 'lessor_address' => 'Darbar Gadh, Sosiya', 'lessor_pincode' => '364150',
            'leased_area_acres' => 100, 'leased_capacity_mw' => 200, 'rent_rate_per_acre' => 42000, 'atl_date' => '2024-07-02', 'lease_date' => '2024-11-08', 'lease_start_date' => '2024-11-20',
            'uni_number' => 'UNI-GJ-24-019920', 'sro_doc_no' => 'TLJ/3307/2024', 'agreement_no' => 'LA-PGP10-002', 'mutation_entry_no' => 'ME-3519', 'security_deposit' => 500000]);
        $l3 = $lease($m1, $MH, ['lessor_name' => 'Dattatray S. Jadhav', 'lessor_pan' => 'AFJPJ5532Q', 'lessor_address' => 'Jadhav Wasti, Penur', 'lessor_pincode' => '413213',
            'leased_area_acres' => 20, 'leased_capacity_mw' => 8, 'rent_rate_per_acre' => 38000, 'atl_date' => '2024-10-10', 'lease_date' => '2025-01-06', 'lease_start_date' => '2025-01-10',
            'uni_number' => 'UNI-MH-25-000417', 'sro_doc_no' => 'MHL/88/2025', 'agreement_no' => 'LA-PGP12-001', 'mutation_entry_no' => 'ME-1207', 'escalation_pct' => 4]);

        /* payments for every past instalment, farmer by farmer (LSE001 · 1 Apr 2026 left overdue) */
        $utr = 40112345;
        $chl = 28100;
        $sap = 4410;
        RentSchedule::with('land.farmers')->whereDate('due_date', '<', now())->orderBy('due_date')->get()
            ->reject(fn ($s) => $s->lease_id === $l1->id && $s->due_date->toDateString() === '2026-04-01')
            ->each(function (RentSchedule $s) use ($shares, &$utr, &$chl, &$sap) {
                $pd = $s->due_date->copy()->addDays(3);
                foreach ($shares->forSchedule($s) as $x) {
                    Payment::create(['schedule_id' => $s->id, 'land_id' => $s->land_id, 'farmer_id' => $x['farmer']->id, 'payment_date' => $pd, 'gross_amount' => $x['share'],
                        'tds_deducted' => $x['tds'], 'tds_challan_no' => 'CHL'.($chl++), 'net_paid' => $x['share'] - $x['tds'], 'utr_reference' => 'UTIBR'.$pd->format('Ymd').($utr++),
                        'sap_voucher_no' => '51000'.($sap++)]);
                }
                $s->forceFill(['alert_sent_flag' => 'Yes'])->save();
                $s->refreshStatus();
            });
        RentSchedule::where('lease_id', $l1->id)->whereDate('due_date', '2026-04-01')->update(['alert_sent_flag' => 'Yes']);

        /* subleases (acres + MW) */
        $sub1 = Sublease::create(['lease_id' => $l2->id, 'land_id' => $b1->id, 'sublessee_name' => 'Sample EPC Co. Pvt Ltd', 'sublessee_pan' => 'AAKCS7712P', 'sublease_area_acres' => 60,
            'capacity_mw' => 100, 'sublease_start' => '2025-01-01', 'sublease_end' => '2053-11-19', 'sublease_rent' => 2700000, 'agreement_no' => 'SLA-PGP10-001', 'lender_noc_obtained' => 'Yes', 'status' => 'Active']);
        Sublease::create(['lease_id' => $l3->id, 'land_id' => $m1->id, 'sublessee_name' => 'Sample Grid Services LLP', 'sublessee_pan' => 'AAPFS3391L', 'sublease_area_acres' => 20,
            'capacity_mw' => 8, 'sublease_start' => '2025-03-01', 'sublease_end' => '2054-01-09', 'sublease_rent' => 800000, 'agreement_no' => 'SLA-PGP12-001', 'lender_noc_obtained' => 'No', 'status' => 'Active']);

        /* compliance */
        $c1 = Compliance::create(['land_id' => $b1->id, 'mortgage_created' => 'Yes', 'mortgage_date' => '2025-03-12', 'lender_name' => 'Sample Lender Bank', 'cersai_reg_no' => '400012345678',
            'original_docs_custody' => 'With lender', 'custodian_name' => 'Sample Lender Bank, Ahmedabad', 'docs_deposit_date' => '2025-03-12', 'lender_noc_status' => 'Obtained',
            'pdc_open_items' => 2, 'target_closure_date' => '2026-12-31', 'escalation_level' => 'L1 · Land head', 'remarks' => 'Mutation of mortgage charge pending in 7/12.']);
        Compliance::create(['land_id' => $a1->id, 'mortgage_created' => 'No', 'original_docs_custody' => 'With company', 'custodian_name' => 'Corporate legal vault',
            'docs_deposit_date' => '2024-04-05', 'lender_noc_status' => 'Not required', 'pdc_open_items' => 3, 'target_closure_date' => '2026-09-30', 'escalation_level' => 'L2 · CFO',
            'remarks' => 'NA order copy and two heirship affidavits outstanding.']);

        /* documents (small placeholder PDFs so download works) */
        $priya = User::where('username', 'priya.land')->value('id');
        $doc = function (Land $l, string $type, ?int $id, string $docType, string $title, string $file, int $kb, string $at, int $ver = 1) use ($priya) {
            $path = 'documents/'.$l->spv->spv_code.'/'.$l->land_ref_no.'/'.$file;
            Storage::disk(config('solar.upload.disk'))->put($path, self::pdf($title));
            $d = Document::create(['land_id' => $l->id, 'linked_entity_type' => $type, 'linked_entity_id' => $id ?? $l->id, 'doc_type' => $docType, 'doc_title' => $title,
                'file_name' => $file, 'file_path' => $path, 'file_size_kb' => $kb, 'mime_type' => 'application/pdf', 'version_no' => $ver, 'is_original_scan' => 'Yes', 'uploaded_by' => $priya]);
            $d->forceFill(['created_at' => $at, 'updated_at' => $at])->save();
        };
        $doc($a1, 'Survey', $s1->id, '7/12 extract', '7/12 extract survey 112/2', '712_112-2_Trapaj.pdf', 1840, '2024-02-02 11:30:00');
        $doc($a1, 'Lease', $l1->id, 'Registered lease deed', 'Lease deed LA-PGP10-001', 'LeaseDeed_LA-PGP10-001.pdf', 24210, '2024-03-25 15:02:00');
        $doc($b1, 'Compliance', $c1->id, 'CERSAI certificate', 'CERSAI registration 400012345678', 'CERSAI_400012345678.pdf', 412, '2025-03-15 10:44:00');
        $doc($b1, 'Sublease', $sub1->id, 'Sublease deed', 'Sublease deed SLA-PGP10-001', 'Sublease_SLA-PGP10-001_v2.pdf', 31870, '2025-01-09 16:20:00', 2);

        /* a few audit rows like the template's */
        $u = User::pluck('id', 'username');
        $rows = [
            ['priya.land', 'city', 'CITY_MASTER', (string) City::where('city_name', 'Rapar')->value('id'), 'Rapar', 'CREATE', null, null, 'New record', '2026-10-13 11:02:00', '10.20.4.27'],
            ['priya.land', 'land', 'LAND_MASTER', (string) $a2->id, $a2->land_ref_no, 'UPDATE', 'title_dd_status', 'Pending', 'In progress', '2026-10-14 12:08:00', '10.20.4.27'],
            ['kunal.fin', 'schedule', 'RENT_SCHEDULE', '7', 'RS0007', 'UPDATE', 'alert_sent_flag', 'No', 'Yes', '2026-10-15 09:31:00', '10.20.6.42'],
            ['priya.land', 'compliance', 'COMPLIANCE_TRACKER', '2', 'CMP002', 'UPDATE', 'escalation_level', 'L1 · Land head', 'L2 · CFO', '2026-10-15 16:02:00', '10.20.4.27'],
            ['superadmin', 'user', 'USER_MASTER', (string) $u['kunal.fin'], 'kunal.fin', 'UPDATE', 'spv_access', 'PGP10, PGP12', 'PGP10', '2026-10-16 09:20:00', '10.20.4.11'],
            ['anita.audit', 'land', 'LAND_MASTER', null, 'Land Master', 'EXPORT', null, null, '4 rows to xlsx', '2026-10-16 09:45:00', '10.20.8.5'],
            ['kunal.fin', 'session', 'SESSION', (string) $u['kunal.fin'], 'kunal.fin', 'LOGIN_FAILED', 'password', null, 'Wrong password (1 of 5)', '2026-10-16 10:04:00', '10.20.6.42'],
            ['kunal.fin', 'session', 'SESSION', (string) $u['kunal.fin'], 'kunal.fin', 'LOGIN', null, null, 'Signed in', '2026-10-16 10:05:00', '10.20.6.42'],
        ];
        foreach ($rows as [$un, $mod, $tbl, $rid, $label, $act, $field, $old, $new, $at, $ip]) {
            DB::table('audit_logs')->insert(['user_id' => $u[$un], 'username' => $un, 'ip_address' => $ip, 'module' => $mod, 'table_name' => $tbl, 'record_id' => $rid,
                'record_label' => $label, 'action' => $act, 'field_name' => $field, 'old_value' => $old, 'new_value' => $new, 'created_at' => Carbon::parse($at)]);
        }
    }

    /** A one-page PDF with the document title, used as a stand-in for the scanned original. */
    private static function pdf(string $title): string
    {
        $t = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], 'Sample document: '.$title);
        $stream = "BT /F1 16 Tf 60 760 Td ($t) Tj ET";
        $objs = ['<< /Type /Catalog /Pages 2 0 R >>', '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length '.strlen($stream)." >>\nstream\n$stream\nendstream", '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>'];
        $out = "%PDF-1.4\n";
        $off = [];
        foreach ($objs as $i => $o) {
            $off[] = strlen($out);
            $out .= ($i + 1)." 0 obj\n$o\nendobj\n";
        }
        $x = strlen($out);
        $out .= "xref\n0 ".(count($objs) + 1)."\n0000000000 65535 f \n";
        foreach ($off as $o) {
            $out .= sprintf("%010d 00000 n \n", $o);
        }

        return $out.'trailer << /Size '.(count($objs) + 1)." /Root 1 0 R >>\nstartxref\n$x\n%%EOF\n";
    }
}
