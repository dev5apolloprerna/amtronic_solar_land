<?php

/*
|--------------------------------------------------------------------------
| Solar Land Lease system configuration
|--------------------------------------------------------------------------
| Modules for the permission matrix, dropdown option lists and defaults.
*/

return [

    // module key => [label, table, actions not applicable]
    'modules' => [
        'state'      => ['label' => 'State Master',            'table' => 'states',                  'na' => []],
        'city'       => ['label' => 'City Master',             'table' => 'cities',                  'na' => []],
        'taluka'     => ['label' => 'Taluka Master',           'table' => 'talukas',                 'na' => []],
        'party'      => ['label' => 'Party Master',            'table' => 'parties',                 'na' => []],
        'park'       => ['label' => 'Park Master',             'table' => 'parks',                   'na' => []],
        'spv'        => ['label' => 'SPV Master',              'table' => 'spvs',                    'na' => []],
        'plot'       => ['label' => 'Plot / Survey No.',       'table' => 'plots',                   'na' => []],
        'ownership'  => ['label' => '↳ Landowners & shares',   'table' => 'plot_ownership_versions', 'na' => []],
        'grid'       => ['label' => '↳ Grid connectivity',     'table' => 'plot_grid_connectivity',  'na' => ['add', 'delete']],
        'lease'      => ['label' => 'Lease Master',            'table' => 'leases',                  'na' => []],
        'schedule'   => ['label' => 'Rent Schedule',           'table' => 'rent_schedules',          'na' => ['add', 'delete']],
        'payment'    => ['label' => 'Payment Register',        'table' => 'rent_payments',           'na' => []],
        'sublease'   => ['label' => 'Sub-Lease Master',        'table' => 'sub_leases',              'na' => []],
        'compliance' => ['label' => 'Compliance Tracker',      'table' => 'compliance_records',      'na' => []],
        'document'   => ['label' => 'Document Repository',     'table' => 'documents',               'na' => ['edit']],
        'report'     => ['label' => 'Reports',                 'table' => '—',                       'na' => ['add', 'edit', 'delete']],
        'audit'      => ['label' => 'Audit Trail',             'table' => 'audit_logs',              'na' => ['add', 'edit', 'delete']],
        'setting'    => ['label' => 'Settings',                'table' => 'settings',                'na' => ['add', 'delete', 'export']],
    ],

    'actions' => ['view', 'add', 'edit', 'delete', 'export'],

    'options' => [
        'status'               => ['Active', 'Inactive'],
        'yes_no'               => ['Yes', 'No'],
        'party_type'           => ['Landowner', 'Company', 'Sub-lessee', 'Other'],
        'land_unit'            => ['Acre', 'Hectare', 'Sq.m'],
        'technology'           => ['Solar PV', 'Wind', 'Hybrid'],
        'project_status'       => ['Development', 'Under construction', 'Commissioned'],
        'title_dd_status'      => ['Pending', 'In progress', 'Clear', 'Issue found'],
        'encumbrance_status'   => ['Under verification', 'Nil', 'Encumbered'],
        'na_status'            => ['Not applied', 'Applied', 'Approved', 'Rejected'],
        'land_status'          => ['Identified', 'ATL signed', 'Leased', 'Sub-leased', 'Mortgaged', 'Dropped'],
        'lease_status'         => ['Draft', 'Active', 'Expired', 'Terminated'],
        'sublease_status'      => ['Draft', 'Active', 'Expired', 'Cancelled'],
        'rent_type'            => ['Per acre', 'Fixed annual'],
        'payment_frequency'    => ['Annual', 'Half-yearly', 'Quarterly', 'Monthly'],
        'payment_mode'         => ['NEFT', 'RTGS', 'Cheque', 'UPI', 'Other'],
        'schedule_status'      => ['Pending', 'Partially paid', 'Paid', 'On hold'],
        'docs_custody'         => ['With company', 'With lender', 'With custodian'],
        'lender_noc_status'    => ['Not required', 'Pending', 'Obtained'],
        'escalation_level'     => ['L0 · None', 'L1 · Land head', 'L2 · CFO', 'L3 · MD'],
        'doc_type'             => ['7/12 extract', 'Title report', 'Agreement to lease (ATL)', 'Registered lease deed',
                                   'Sub-lease deed', 'Mutation entry', 'NA order', 'CERSAI certificate', 'Lender NOC',
                                   'Payment proof', 'TDS certificate', 'Other'],
    ],

    // Chip colour per status word (mirrors TONE in the HTML template)
    'tones' => [
        'Active' => 'ok', 'Paid' => 'ok', 'Clear' => 'ok', 'Obtained' => 'ok', 'Yes' => 'ok', 'Commissioned' => 'ok',
        'Leased' => 'ok', 'Approved' => 'ok', 'Nil' => 'ok', 'Finalised' => 'ok',
        'Sub-leased' => 'info', 'ATL signed' => 'info', 'Mortgaged' => 'info', 'Draft' => 'info',
        'Pending' => 'warn', 'In progress' => 'warn', 'Due soon' => 'warn', 'Under verification' => 'warn',
        'Applied' => 'warn', 'Under construction' => 'warn', 'On hold' => 'warn', 'Partially paid' => 'warn',
        'L1 · Land head' => 'warn',
        'Overdue' => 'bad', 'Issue found' => 'bad', 'Encumbered' => 'bad', 'Rejected' => 'bad', 'Expired' => 'bad',
        'Terminated' => 'bad', 'Cancelled' => 'bad', 'Inactive' => 'bad', 'L2 · CFO' => 'bad', 'L3 · MD' => 'bad',
    ],

    'land_ref' => [
        'format'    => '{PARK}-{STATE}-{CITY}-{TALUKA}-{SEQ}',
        'seq_pad'   => 4,
        'seq_scope' => 'prefix', // 'prefix' = per PARK+STATE+CITY+TALUKA, 'park' = one sequence per park
    ],

    'upload' => [
        'disk'       => 'local',
        'max_kb'     => 102400, // 100 MB
        'extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'tif', 'tiff', 'doc', 'docx', 'xls', 'xlsx', 'zip'],
    ],

    'max_login_attempts' => 5,
    'per_page' => 25,
];
