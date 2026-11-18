<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GridConnectivityController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\OwnershipController;
use App\Http\Controllers\ParkController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\PlotController;
use App\Http\Controllers\RentPaymentController;
use App\Http\Controllers\RentScheduleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SpvController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\SubLeaseController;
use App\Http\Controllers\TalukaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
| Standard CRUD block used by every module that opens its form in the drawer:
| index · create (drawer) · store · edit (drawer, view-only without edit right) · update · destroy
*/
$crud = function (string $uri, string $controller, string $module, string $param) {
    Route::get($uri, [$controller, 'index'])->middleware("perm:$module,view")->name("$uri.index");
    Route::get("$uri/create", [$controller, 'create'])->middleware("perm:$module,add")->name("$uri.create");
    Route::post($uri, [$controller, 'store'])->middleware("perm:$module,add")->name("$uri.store");
    Route::get("$uri/{{$param}}/edit", [$controller, 'edit'])->middleware("perm:$module,view")->name("$uri.edit");
    Route::put("$uri/{{$param}}", [$controller, 'update'])->middleware("perm:$module,edit")->name("$uri.update");
    Route::delete("$uri/{{$param}}", [$controller, 'destroy'])->middleware("perm:$module,delete")->name("$uri.destroy");
};

/* ---------- guest ---------- */
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:20,1');
    Route::get('forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'email'])->middleware('throttle:6,1')->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

/* ---------- signed in ---------- */
Route::middleware(['auth', 'active'])->group(function () use ($crud) {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // record history modal (audit rows of one record)
    Route::get('history/{module}/{id}', [AuditLogController::class, 'record'])->name('history');

    // dropdown & live-calculation lookups (JSON)
    Route::prefix('lookup')->name('lookup.')->group(function () {
        Route::get('cities', [LookupController::class, 'cities'])->name('cities');
        Route::get('talukas', [LookupController::class, 'talukas'])->name('talukas');
        Route::get('spvs', [LookupController::class, 'spvs'])->name('spvs');
        Route::get('land-ref-preview', [LookupController::class, 'landRefPreview'])->name('land-ref');
        Route::get('plot/{plot}', [LookupController::class, 'plot'])->name('plot');
        Route::get('lease/{lease}/capacity', [LookupController::class, 'leaseCapacity'])->name('lease-capacity');
        Route::get('park/{park}/capacity', [LookupController::class, 'parkCapacity'])->name('park-capacity');
        Route::get('schedule/{schedule}/allocations', [LookupController::class, 'allocations'])->name('allocations');
    });

    /* masters */
    $crud('states', StateController::class, 'state', 'state');
    $crud('cities', CityController::class, 'city', 'city');
    $crud('talukas', TalukaController::class, 'taluka', 'taluka');
    $crud('parties', PartyController::class, 'party', 'party');
    $crud('parks', ParkController::class, 'park', 'park');
    $crud('spvs', SpvController::class, 'spv', 'spv');

    /* plots / survey numbers: full-page workspace instead of the drawer */
    Route::get('plots', [PlotController::class, 'index'])->middleware('perm:plot,view')->name('plots.index');
    Route::get('plots/create', [PlotController::class, 'create'])->middleware('perm:plot,add')->name('plots.create');
    Route::post('plots', [PlotController::class, 'store'])->middleware('perm:plot,add')->name('plots.store');
    Route::get('plots/{plot}', [PlotController::class, 'show'])->middleware('perm:plot,view')->name('plots.show');
    Route::put('plots/{plot}', [PlotController::class, 'update'])->middleware('perm:plot,edit')->name('plots.update');
    Route::delete('plots/{plot}', [PlotController::class, 'destroy'])->middleware('perm:plot,delete')->name('plots.destroy');
    Route::put('plots/{plot}/grid', [GridConnectivityController::class, 'update'])->middleware('perm:grid,edit')->name('plots.grid');

    /* landowners & shares */
    Route::post('plots/{plot}/ownership', [OwnershipController::class, 'startDraft'])->middleware('perm:ownership,add')->name('ownership.draft');
    Route::get('ownership/{version}/shares/create', [OwnershipController::class, 'createShare'])->middleware('perm:ownership,add')->name('ownership.shares.create');
    Route::post('ownership/{version}/shares', [OwnershipController::class, 'storeShare'])->middleware('perm:ownership,add')->name('ownership.shares.store');
    Route::get('ownership-shares/{share}/edit', [OwnershipController::class, 'editShare'])->middleware('perm:ownership,edit')->name('ownership.shares.edit');
    Route::put('ownership-shares/{share}', [OwnershipController::class, 'updateShare'])->middleware('perm:ownership,edit')->name('ownership.shares.update');
    Route::delete('ownership-shares/{share}', [OwnershipController::class, 'destroyShare'])->middleware('perm:ownership,delete')->name('ownership.shares.destroy');
    Route::get('ownership/{version}/finalise', [OwnershipController::class, 'finaliseForm'])->middleware('perm:ownership,edit')->name('ownership.finalise.form');
    Route::post('ownership/{version}/finalise', [OwnershipController::class, 'finalise'])->middleware('perm:ownership,edit')->name('ownership.finalise');
    Route::delete('ownership/{version}', [OwnershipController::class, 'discard'])->middleware('perm:ownership,delete')->name('ownership.discard');

    /* leasing */
    $crud('leases', LeaseController::class, 'lease', 'lease');
    Route::get('schedules', [RentScheduleController::class, 'index'])->middleware('perm:schedule,view')->name('schedules.index');
    Route::get('schedules/{schedule}/edit', [RentScheduleController::class, 'edit'])->middleware('perm:schedule,view')->name('schedules.edit');
    Route::put('schedules/{schedule}', [RentScheduleController::class, 'update'])->middleware('perm:schedule,edit')->name('schedules.update');
    Route::get('schedules/{schedule}/payments', [RentScheduleController::class, 'payments'])->middleware('perm:schedule,view')->name('schedules.payments');
    $crud('payments', RentPaymentController::class, 'payment', 'payment');
    $crud('subleases', SubLeaseController::class, 'sublease', 'sublease');

    /* records */
    $crud('compliance', ComplianceController::class, 'compliance', 'compliance');
    Route::get('documents', [DocumentController::class, 'index'])->middleware('perm:document,view')->name('documents.index');
    Route::get('documents/create', [DocumentController::class, 'create'])->middleware('perm:document,add')->name('documents.create');
    Route::post('documents', [DocumentController::class, 'store'])->middleware('perm:document,add')->name('documents.store');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->middleware('perm:document,view')->name('documents.download');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->middleware('perm:document,delete')->name('documents.destroy');

    /* reports */
    Route::get('reports', [ReportController::class, 'index'])->middleware('perm:report,view')->name('reports.index');
    Route::get('reports/{report}', [ReportController::class, 'show'])->middleware('perm:report,view')->name('reports.show');

    /* administration */
    Route::get('audit', [AuditLogController::class, 'index'])->middleware('perm:audit,view')->name('audit.index');
    Route::get('settings', [SettingController::class, 'index'])->middleware('perm:setting,view')->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->middleware('perm:setting,edit')->name('settings.update');

    Route::middleware('super')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::put('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});
