<?php

namespace App\Http\Controllers;

/** Column layout of each parcel tab for Excel / PDF export (same columns as on screen). */
class TabExport
{
    public static function rows(string $tab, $rows): array
    {
        return match ($tab) {
            'survey' => [['ID', 'Survey / plot no.', 'Village', 'City / taluka', 'District', 'State', 'Area (sq.m)', 'Area (ac)'],
                $rows->map(fn ($r) => [$r->code, $r->survey_plot_no, $r->village, $r->city->city_name, $r->district->district_name, $r->state->state_name, (float) $r->area_sqm, (float) $r->area_acres])],
            'farmer' => [['ID', 'Share (%)', 'Farmer name', 'PAN', 'Aadhaar no.', 'Voter ID', 'Bank A/c no.', 'Bank & branch'],
                $rows->map(fn ($r) => [$r->code, (float) $r->ownership_pct, $r->owner_name, $r->owner_pan, $r->aadhaar, $r->voter_id, $r->maskedBank(), $r->bank_branch])],
            'lease' => [['Lease', 'Lessor', 'PAN', 'Area (ac)', 'Capacity (MW)', 'Start', 'End', 'Rent / instalment', 'Frequency', 'Escalation', 'Status'],
                $rows->map(fn ($r) => [$r->code, $r->lessor_name, $r->lessor_pan, (float) $r->leased_area_acres, (float) $r->leased_capacity_mw, $r->lease_start_date->format('Y-m-d'),
                    $r->lease_end_date->format('Y-m-d'), (float) $r->rent_amount, $r->rent_frequency, fmt_num($r->escalation_pct).'% every '.$r->escalation_every_yrs.' yrs', $r->lease_status])],
            'schedule' => [['ID', 'Lease', 'Month', 'Due date', 'Rent', 'TDS', 'Net payable', 'Paid', 'Remaining', 'Status', 'Alert'],
                $rows->map(fn ($s) => [$s->code, $s->lease->code, $s->due_month, $s->due_date->format('Y-m-d'), (float) $s->rent_amount, (float) $s->tds_amount, (float) $s->net_payable,
                    $s->totalPaid(), $s->remaining(), $s->status(), $s->alert_sent_flag])],
            'payment' => [['Payment', 'Instalment', 'Farmer', 'Paid on', 'Gross paid', 'TDS', 'TDS challan', 'Net paid', 'UTR', 'SAP voucher'],
                $rows->map(fn ($p) => [$p->code, $p->schedule->code, $p->farmer->owner_name, $p->payment_date->format('Y-m-d'), (float) $p->gross_amount, (float) $p->tds_deducted,
                    $p->tds_challan_no, (float) $p->net_paid, $p->utr_reference, $p->sap_voucher_no])],
            'sublease' => [['Sublease', 'Lease', 'Sublessee', 'PAN', 'Area (ac)', 'Capacity (MW)', 'Start', 'End', 'Rent / yr', 'Agreement no.', 'Lender NOC', 'Status'],
                $rows->map(fn ($s) => [$s->code, $s->lease->code, $s->sublessee_name, $s->sublessee_pan, (float) $s->sublease_area_acres, (float) $s->capacity_mw,
                    $s->sublease_start->format('Y-m-d'), $s->sublease_end->format('Y-m-d'), (float) $s->sublease_rent, $s->agreement_no, $s->lender_noc_obtained, $s->status])],
            'compliance' => [['ID', 'Mortgage', 'Mortgage date', 'Lender', 'CERSAI no.', 'Originals', 'Custodian', 'NOC', 'Open PDC', 'Target closure', 'Escalation', 'Remarks'],
                $rows->map(fn ($c) => [$c->code, $c->mortgage_created, $c->mortgage_date?->format('Y-m-d'), $c->lender_name, $c->cersai_reg_no, $c->original_docs_custody, $c->custodian_name,
                    $c->lender_noc_status, $c->pdc_open_items, $c->target_closure_date?->format('Y-m-d'), $c->escalation_level, $c->remarks])],
            'doc' => [['ID', 'Document', 'Type', 'Linked to', 'Record', 'File', 'Size (MB)', 'Version', 'Original', 'Uploaded by', 'Uploaded at'],
                $rows->map(fn ($d) => [$d->code, $d->doc_title, $d->doc_type, $d->linked_entity_type, $d->linkedCode(), $d->file_name, round($d->file_size_kb / 1024, 1), $d->version_no,
                    $d->is_original_scan, $d->uploader?->username, $d->created_at->format('Y-m-d H:i')])],
        };
    }
}
