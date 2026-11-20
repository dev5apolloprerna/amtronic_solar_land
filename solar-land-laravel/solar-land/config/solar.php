<?php

/*
| Leased Land System configuration: permission matrix modules, dropdown options, chip colours.
| Module keys and table names follow the HTML template.
*/

return [

    'modules' => [
        'state'      => ['label' => 'State Master',                         'table' => 'STATE_MASTER',        'na' => []],
        'district'   => ['label' => 'District Master',                      'table' => 'DISTRICT_MASTER',     'na' => []],
        'city'       => ['label' => 'City / Taluka Master',                 'table' => 'CITY_MASTER',         'na' => []],
        'park'       => ['label' => 'Park Master',                          'table' => 'PARK_MASTER',         'na' => []],
        'spv'        => ['label' => 'SPV Master',                           'table' => 'SPV_MASTER',          'na' => []],
        'land'       => ['label' => 'Land Master (opens the parcel)',       'table' => 'LAND_MASTER',         'na' => []],
        'survey'     => ['label' => '↳ Survey / Plot No. (inside a parcel)', 'table' => 'LAND_SURVEY',         'na' => []],
        'farmer'     => ['label' => '↳ Farmers / Owners (inside a parcel)',  'table' => 'LAND_OWNER',          'na' => []],
        'lease'      => ['label' => '↳ Lease (inside a parcel)',             'table' => 'LEASE_MASTER',        'na' => []],
        'schedule'   => ['label' => '↳ Rent Schedule (inside a parcel)',     'table' => 'RENT_SCHEDULE',       'na' => ['add', 'delete']],
        'payment'    => ['label' => '↳ Payment Register (inside a parcel)',  'table' => 'PAYMENT_REGISTER',    'na' => []],
        'sublease'   => ['label' => '↳ Sublease (inside a parcel)',          'table' => 'SUBLEASE_MASTER',     'na' => []],
        'compliance' => ['label' => '↳ Compliance Tracker (inside a parcel)', 'table' => 'COMPLIANCE_TRACKER', 'na' => []],
        'doc'        => ['label' => '↳ Documents (inside a parcel)',         'table' => 'DOCUMENT_REPOSITORY', 'na' => ['edit']],
        'report'     => ['label' => 'Reports',                              'table' => 'REPORTS',             'na' => ['add', 'edit', 'delete']],
        'audit'      => ['label' => 'Audit Trail',                          'table' => 'AUDIT_TRAIL',         'na' => ['add', 'edit', 'delete']],
    ],

    'actions' => ['view', 'add', 'edit', 'delete', 'export'],

    // Tabs inside an open land parcel, in template order => [label, permission module]
    'land_tabs' => [
        'land'       => ['Land Master', 'land'],
        'survey'     => ['Survey / Plot No.', 'survey'],
        'farmer'     => ['Farmers / Owners', 'farmer'],
        'grid'       => ['Grid Connectivity', 'land'],
        'lease'      => ['Lease', 'lease'],
        'schedule'   => ['Rent Schedule', 'schedule'],
        'payment'    => ['Payment Register', 'payment'],
        'sublease'   => ['Sublease', 'sublease'],
        'compliance' => ['Compliance Tracker', 'compliance'],
        'doc'        => ['Documents', 'doc'],
    ],

    'options' => [
        'yes_no'             => ['Yes', 'No'],
        'city_type'          => ['Taluka', 'Tehsil', 'City'],
        'technology'         => ['Solar PV', 'Wind', 'Hybrid'],
        'spv_status'         => ['Development', 'Under construction', 'Commissioned'],
        'title_dd_status'    => ['Pending', 'In progress', 'Clear', 'Issue found'],
        'encumbrance_status' => ['Under verification', 'Nil', 'Encumbered'],
        'na_status'          => ['Not applied', 'Applied', 'Approved', 'Rejected'],
        'land_status'        => ['Identified', 'ATL signed', 'Leased', 'Subleased', 'Mortgaged', 'Dropped'],
        'lease_status'       => ['Draft', 'Active', 'Expired', 'Terminated'],
        'sublease_status'    => ['Draft', 'Active', 'Terminated'],
        'rent_frequency'     => ['Annual', 'Half-yearly', 'Quarterly', 'Monthly'],
        'docs_custody'       => ['With company', 'With lender', 'With custodian'],
        'lender_noc_status'  => ['Not required', 'Pending', 'Obtained'],
        'escalation_level'   => ['L0 · None', 'L1 · Land head', 'L2 · CFO', 'L3 · MD'],
        'doc_type'           => ['7/12 extract', 'Title report', 'Agreement to lease (ATL)', 'Registered lease deed', 'Sublease deed', 'Mutation entry',
                                 'NA order', 'CERSAI certificate', 'Lender NOC', 'Payment proof', 'TDS certificate', 'Other'],
        'schedule_status'    => ['Overdue', 'Due soon', 'Upcoming', 'Partial Payment', 'Paid', 'On hold'],
    ],

    // chip colours, as TONE in the template
    'tones' => [
        'Active' => 'ok', 'Paid' => 'ok', 'Clear' => 'ok', 'Obtained' => 'ok', 'Yes' => 'ok', 'Commissioned' => 'ok', 'Leased' => 'ok', 'Approved' => 'ok', 'Nil' => 'ok',
        'Subleased' => 'info', 'ATL signed' => 'info', 'Mortgaged' => 'info', 'Matches' => 'ok',
        'Pending' => 'warn', 'In progress' => 'warn', 'Due soon' => 'warn', 'Under verification' => 'warn', 'Applied' => 'warn', 'Under construction' => 'warn',
        'On hold' => 'warn', 'Partial Payment' => 'warn', 'L1 · Land head' => 'warn',
        'Overdue' => 'bad', 'Issue found' => 'bad', 'Encumbered' => 'bad', 'Rejected' => 'bad', 'L2 · CFO' => 'bad', 'L3 · MD' => 'bad', 'Terminated' => 'bad', 'Expired' => 'bad',
    ],

    'upload' => [
        'disk'       => 'local',
        'max_kb'     => 102400,
        'extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'tif', 'tiff', 'doc', 'docx', 'xls', 'xlsx', 'zip'],
    ],

    'sqm_per_acre' => 4046.8564,
    'max_login_attempts' => 5,
    'per_page' => 25,
];
