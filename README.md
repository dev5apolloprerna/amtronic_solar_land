# Solar Land Lease & Sub-Lease Management System (Laravel 12)

Complete, ready-to-run Laravel 12.72 project. It provides parks, SPVs, parties, survey numbers with
automatic Land Reference Numbers, landowners with versioned % shares, leases with rent schedules,
landowner-wise payments, MW sub-leasing with overlap-aware capacity checks, compliance, documents,
a dashboard, 11 reports with Excel/PDF export, users/roles with per-SPV access and a full audit trail.
The UI is your HTML admin template converted to Blade (same layout, drawer forms, modals, chips, tables).

## 1. Install (everything is included: vendor, .env, composer.lock, database)

Requirements: PHP 8.2+ (8.3 tested) with extensions pdo_mysql, mbstring, gd, zip, intl, xml; MySQL 8 or MariaDB 10.6+ (MariaDB 10.11 tested).
XAMPP / WAMP / Laragon work.

1. Unzip the folder, e.g. to `C:\xampp\htdocs\solar-land` or `/var/www/solar-land`.
2. Create an empty database `solar_land` (utf8mb4 / utf8mb4_unicode_ci).
3. Open `.env` and check `DB_USERNAME` / `DB_PASSWORD` (default: root with an empty password, as in XAMPP).
4. Load the data, **either**:
   * import `database/sql/solar_land.sql` in phpMyAdmin (or `mysql -u root solar_land < database/sql/solar_land.sql`), **or**
   * run `php artisan migrate --seed` to build the same tables and demo data from the migrations.
5. Start it: `php artisan serve` and open http://localhost:8000 (or point Apache/Nginx at the `public` folder).

Notes
* `vendor/` is already installed, so `composer install` is not needed. `composer.json` and `composer.lock` are standard;
  `composer install` or `composer update` will refresh vendor from Packagist if you ever want to.
* **Keep the `APP_KEY` in `.env` if you import the SQL dump**: landowner bank account numbers in it are encrypted with that key.
  If you generate a new key, re-create the data with `php artisan migrate:fresh --seed` instead.
* `database/sql/solar_land_schema_only.sql` has the tables without data, for a clean start (then run `php artisan db:seed --class=Database\\Seeders\\BaseSeeder` for roles, locations, settings and the Super Admin).
* For production set `APP_ENV=production`, `APP_DEBUG=false`, a strong `ADMIN_PASSWORD`, real `MAIL_*` settings (forgot password),
  and a cron job `* * * * * php artisan schedule:run` so leases and sub-leases past their end date become **Expired** daily.
* Bootstrap CSS is not loaded on purpose: the template has its own complete styles. jQuery is bundled in `public/js/vendor`.

### Sign-in accounts

| Username | Password | Role | SPVs |
|---|---|---|---|
| superadmin | `Admin@12345` (or `ADMIN_PASSWORD`) | Super Admin | all |
| priya.land | `Demo@12345` | Land Manager | PGP10, PGP11, PGP12 |
| kunal.fin | `Demo@12345` | Finance | PGP10 |
| anita.audit | `Demo@12345` | Auditor (read + export) | all three |

Change these passwords after the first sign-in. Five wrong passwords lock an account; the Super Admin unlocks it in Users & Roles.

### Demo data (the spec's examples)

* Survey **125/2** (Trapaj) → `BHV-GJ-BHV-TLJ-0001`, owners A 50 %, B 30 %, C 20 %.
* Lease **LSE00001** on it = **200 MW**; sub-leased to Party A 20 MW, Party B 30 MW, Party C 50 MW → **100 MW remaining**.
* Park BHV has 250 MW installed, SLP 75 MW; past rent instalments are paid, one is part-paid and overdue.

## 2. Where things are

| Area | Files |
|---|---|
| Database | `database/migrations/*` (24 tables, foreign keys, indexes, soft deletes) |
| Models | `app/Models/*`, traits in `app/Models/Concerns` (audit trail, user stamps, SPV scoping) |
| Business rules | `app/Services/LandRefService.php`, `OwnershipService.php`, `RentScheduleService.php`, `RentAllocationService.php`, `CapacityService.php` |
| Validation | `app/Http/Requests/*` + checks inside the services |
| Controllers | `app/Http/Controllers/*` (one per module), routes in `routes/web.php` |
| Reports & dashboard | `app/Services/ReportService.php`, `DashboardService.php`, `ExportService.php` |
| Blade | `resources/views/layouts`, `partials`, `components` (field, filter bar, pager, row actions…), one folder per module |
| CSS | `public/css/app.css` (template core), `auth.css`, `modules/{dashboard,plots,subleases,users,reports}.css` |
| JS | `public/js/app.js` (drawer, modal, toasts, cascading dropdowns), `modules/{plots,ownership,leases,payments,subleases,documents,users}.js`, jQuery bundled in `public/js/vendor` |
| Options & permissions | `config/solar.php` (dropdown lists, module list for the permission matrix, Land Ref defaults) |

## 3. Key rules (as built)

* **Land Reference Number**: `{PARK}-{STATE}-{CITY}-{TALUKA}-{SEQ}` (format, digits and sequence scope editable in Settings).
  Reserved inside a DB transaction with a row lock, unique index as final guard, never editable afterwards.
  Park / state / city / taluka codes lock once a survey number uses them.
* **Ownership**: draft ≤ 100 %, finalise only at exactly 100 %; a new version supersedes the old one from its effective date.
  Each instalment is split by the version in force on its due date; the last owner gets the rounding remainder.
  Paid instalments keep their split; unpaid ones are re-split after a change.
* **Rent**: annual rent = rate × acres (or fixed) → instalments by frequency with escalation every N years and TDS.
  Changing lease terms regenerates unpaid instalments only. Payments are per landowner and may be partial.
* **Capacity (MW)**: active leases in a park ≤ installed MW, and active sub-leases ≤ lease MW, **on every overlapping day**
  (peak check). Draft sub-leases don't consume MW until activated. MW can't be reduced below what is committed.
  Active sub-leases on a mortgaged survey number need the lender NOC.

## 4. Defaults used for the open questions

1. Leases are capped by the park's installed MW only (SPV MWp shown in reports).
2. Land Ref sequence restarts per park + state + city + taluka.
3. Rent types: Per acre and Fixed annual.
4. Draft sub-leases do not reserve capacity.
5. One party type per party.
6. Village is free text.
7. Only the last 4 Aadhaar digits are stored; bank account numbers are encrypted.
8. PHP 8.2+/MySQL 8, Laravel 12.
9. No import of template-format references.

## 5. Notes

* Fonts load from Google Fonts as in the template; without internet the system font is used.
* Tested on MariaDB 10.11 and SQLite with PHP 8.3: all pages, ~145 business-rule and CRUD checks, Excel/PDF exports.
* Uploads go to `storage/app/private/documents/<SPV>/<Land Ref>/` (100 MB max; PDF, images, Word, Excel, ZIP).
# amtronic_solar_land
