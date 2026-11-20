# Leased Land · Solar Land Lease & Sub-Lease Management System (Laravel 12)

Complete, ready-to-run Laravel 12.72 project built to match your HTML template ("Leased Land · Data management system"):
the same left menu, the same Land Master parcel workspace with its 10 tabs, the same tables, columns, forms, labels and sample data.
On top of the template it keeps the spec features: electricity capacity in **MW** on parks, leases and subleases,
11 reports with Excel / PDF export, users / roles with per-SPV access, and a full audit trail.

## 1. Install (everything is included: vendor, .env, composer.lock, database)

Requirements: PHP 8.2+ (8.3 tested) with extensions pdo_mysql, mbstring, gd, zip, intl, xml; MySQL 8 or MariaDB 10.6+ (MariaDB 10.11 tested).
XAMPP / WAMP / Laragon work.

1. Unzip the folder, e.g. to `C:\xampp\htdocs\solar-land` or `/var/www/solar-land`.
2. Create an empty database `solar_land` (utf8mb4 / utf8mb4_unicode_ci). **If you installed the previous version, drop and re-create it**: the tables changed.
3. Open `.env` and check `DB_USERNAME` / `DB_PASSWORD` (default: root with an empty password, as in XAMPP).
4. Load the data, **either**:
   * import `database/sql/solar_land.sql` in phpMyAdmin (or `mysql -u root solar_land < database/sql/solar_land.sql`), **or**
   * run `php artisan migrate:fresh --seed` to build the same tables and sample data from the migrations.
5. Start it: `php artisan serve` and open http://localhost:8000 (or point Apache/Nginx at the `public` folder).

Notes
* `vendor/` is already installed (`config.platform.php = 8.2.0`), so `composer install` is not needed.
* **Keep the `APP_KEY` in `.env` if you import the SQL dump**: farmers' bank account numbers in it are encrypted with that key.
  If you generate a new key, re-create the data with `php artisan migrate:fresh --seed` instead.
* The 4 sample documents' placeholder PDFs are included under `storage/app/private/documents/`, so their download links work with the SQL dump too.
* `database/sql/solar_land_schema_only.sql` has the tables without data. For a clean start run
  `php artisan migrate:fresh --seed --class=Database\\Seeders\\BaseSeeder` (roles, states / districts / cities and the Super Admin only).
* For production set `APP_ENV=production`, `APP_DEBUG=false`, a strong `ADMIN_PASSWORD`, real `MAIL_*` settings (forgot password),
  and a cron job `* * * * * php artisan schedule:run` (leases past their end date become **Expired**, subleases **Terminated**, daily).

### Sign-in accounts (same as the template)

| Username | Password | Role | SPVs |
|---|---|---|---|
| superadmin (Nikhil Shah) | `Demo@123` (or `ADMIN_PASSWORD`) | Super Admin | all |
| priya.land (Priya Desai) | `Demo@123` | Land Manager | PGP10, PGP12, PGP11 |
| kunal.fin (Kunal Mehta) | `Demo@123` | Finance | PGP10 |
| anita.audit (Anita Rao) | `Demo@123` | Auditor (view + export) | all three |

Change these passwords after the first sign-in. Five wrong passwords lock an account; the Super Admin unlocks it in Users & Roles.

## 2. Menu and screens (as in the template)

| Menu | Screen |
|---|---|
| Overview → Dashboard | "Land portfolio" tiles (parcels, leased area, subleased %, overdue rent, open compliance items) + MW tiles, rent due in 90 days, sublease use by lease, park capacity, recent changes |
| Land → Land Master | List of parcels. Click a land reference no. to open the parcel; the menu then shows the parcel's sub-menu |
| ↳ inside a parcel | **Land Master · Survey / Plot No. · Farmers / Owners · Grid Connectivity · Lease · Rent Schedule · Payment Register · Sublease · Compliance Tracker · Documents** (with row counts) |
| Masters | State Master · District Master · City / Taluka Master · Park Master · SPV Master |
| Reports | 11 reports, each with filters, search, totals, Excel and PDF |
| Administration | Users & Roles (Super Admin only; permission matrix per role) · Audit Trail |

The template's table names (LAND_MASTER, LAND_SURVEY, LAND_OWNER, LEASE_MASTER, RENT_SCHEDULE, PAYMENT_REGISTER, SUBLEASE_MASTER,
COMPLIANCE_TRACKER, DOCUMENT_REPOSITORY …) and record IDs (LND001, SRV001, OWN001, LSE001, RS0001, PAY001, SUB001, CMP001, DOC001, PRK001, SPV001)
are shown on screen exactly as in the template.

## 3. Key rules (as built)

1. **Land reference no.** = `SPV-STATE-PARK-PARCEL-YEAR`, e.g. `PGP10-GJ-BHV-A1-2024`; `NOP` replaces the park when the parcel has no park.
   It is generated on save, previewed live while typing, unique, and can never change (parcel code and year are locked after save).
2. Choosing a park fills State / District / City and lists only that park's SPVs; the SPV must belong to the park.
3. Area under lease cannot exceed the 7/12 area (sq.m ÷ 4,046.8564). The survey tab compares the survey total with the 7/12 area.
4. **Multiple farmers per parcel** with ownership %; the total can never exceed 100 %, and rent payments need exactly 100 %.
   Rent is split per farmer (rounded to the rupee; the last farmer gets the remainder so the split always adds up). Aadhaar is stored masked, bank account encrypted.
5. **Lease**: one active lease per parcel; leased area ≤ area under lease. Saving generates the rent schedule:
   annual rent = acres × rent/acre; instalment = annual × months/12 × (1 + escalation %)^(years ÷ every); TDS 194-I at the lease's rate.
   Changing rent terms regenerates unpaid instalments; paid / part-paid ones are kept. Lease status Identified/ATL signed → Leased automatically.
6. **Payments** are recorded per farmer per instalment, up to that farmer's remaining share; part payments allowed; TDS needs a challan no.
   Instalment status: Paid / Partial Payment / On hold / Overdue / Due soon (≤ 30 days) / Upcoming.
7. **Sublease (one lease → many subleases), acres and MW**:
   * area: non-terminated subleases of a lease can never exceed its leased acres (100-acre lease → at most 100 acres);
   * capacity: active subleases running on the same day can never exceed the lease MW. **200 MW lease with 100 MW subleased → 100 MW remaining**
     (shown on the Sublease tab, in the sublease form, on the lease list, dashboard and the Remaining capacity report);
   * active leases in a park can never exceed the park's installed MW; a lease's MW / acres cannot go below what is already subleased;
   * sublease dates must be inside the lease; an active sublease on a mortgaged parcel needs the lender's NOC.
8. **Compliance**: one record per parcel; a mortgage needs the lender name and marks a leased parcel as Mortgaged.
9. **Documents**: linked to the parcel or one of its records; same type + title = next version (old versions kept); 100 MB max; PDF, images, Word, Excel, ZIP.
10. Deletes are refused with a reason when child records exist (as in the template); every create / edit / delete / export / sign-in is in the audit trail.

## 4. Sample data (the template's)

* 6 states, 10 districts, 15 cities / talukas; parks **BHV** (Bhavnagar Solar Park, 420 ac, **250 MW**) and **SLP** (Solapur Solar Park, 260 ac, **75 MW**); SPVs PGP10, PGP12, PGP11.
* Parcels `PGP10-GJ-BHV-A1-2024`, `-A2-2024`, `-B1-2024`, `PGP12-MH-SLP-A1-2025` with their survey numbers and farmers (60/40, 100, 51/25/24, 50/50).
* Leases LSE001 (12.5 ac, 5 MW), **LSE002 (100 ac, 200 MW)**, LSE003 (20 ac, 8 MW); past instalments paid farmer-wise, LSE001's 1 Apr 2026 instalment overdue.
* **SUB001: 60 ac, 100 MW of LSE002 → 40 ac and 100 MW remaining**; SUB002: 20 ac, 8 MW of LSE003. Compliance CMP001 / CMP002, 4 documents.

## 5. Where things are

| | |
|---|---|
| Routes | `routes/web.php` |
| Controllers | `app/Http/Controllers` (one per module; `LandController` = parcel workspace; `LookupController` = dropdown / preview JSON) |
| Validation | `app/Http/Requests/*Request.php` (all business rules) |
| Logic | `app/Services` (LandRef, RentSchedule, Share (farmer split), Capacity (acres + MW), Report, Dashboard, Document, Audit, Export) |
| Models | `app/Models` (+ `Concerns`: audit trail, SPV visibility, template IDs) |
| Views | `resources/views` (`lands/` = parcel workspace, `lands/tabs/*`, `lands/forms/*`; `masters/*`; `reports/*`) |
| CSS | `public/css/app.css` (the template's stylesheet) + `public/css/modules/{lands,dashboard,reports,users}.css` |
| JS | `public/js/app.js` (drawer, modal, toasts, cascades) + `public/js/modules/{lands,forms,users}.js` |
| Options / permissions | `config/solar.php` (menu modules, dropdown lists, chip colours) |

## 6. Notes

* Fonts load from Google Fonts as in the template; without internet the system font is used.
* Tested on MariaDB 10.11 with PHP 8.3: every page and drawer for all four roles, 51 business-rule / CRUD checks, Excel and PDF exports, and a browser walkthrough.
* Uploads go to `storage/app/private/documents/<SPV>/<Land Ref>/`.
