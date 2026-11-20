<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ComplianceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\LandController;
use App\Http\Controllers\LandSurveyController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\ParkController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentScheduleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SpvController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\SubleaseController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
| Masters: index · create (drawer) · store · edit (drawer, view-only without edit right) · update · destroy
*/
$crud = function (string $uri, string $controller, string $module, string $param) {
    Route::get($uri, [$controller, 'index'])->middleware("perm:$module,view")->name("$uri.index");
    Route::get("$uri/create", [$controller, 'create'])->middleware("perm:$module,add")->name("$uri.create");
    Route::post($uri, [$controller, 'store'])->middleware("perm:$module,add")->name("$uri.store");
    Route::get("$uri/{{$param}}/edit", [$controller, 'edit'])->middleware("perm:$module,view")->name("$uri.edit");
    Route::put("$uri/{{$param}}", [$controller, 'update'])->middleware("perm:$module,edit")->name("$uri.update");
    Route::delete("$uri/{{$param}}", [$controller, 'destroy'])->middleware("perm:$module,delete")->name("$uri.destroy");
};

/*
| Records inside a land parcel: create / store are nested under the parcel, the rest use the record id.
*/
$child = function (string $uri, string $controller, string $module, string $param) {
    Route::get("lands/{land}/$uri/create", [$controller, 'create'])->middleware("perm:$module,add")->name("$uri.create");
    Route::post("lands/{land}/$uri", [$controller, 'store'])->middleware("perm:$module,add")->name("$uri.store");
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
Route::middleware(['auth', 'active'])->group(function () use ($crud, $child) {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('history/{module}/{id}', [AuditLogController::class, 'record'])->name('history');

    // dropdown & live-preview lookups (JSON)
    Route::prefix('lookup')->name('lookup.')->group(function () {
        Route::get('districts', [LookupController::class, 'districts'])->name('districts');
        Route::get('cities', [LookupController::class, 'cities'])->name('cities');
        Route::get('parks', [LookupController::class, 'parks'])->name('parks');
        Route::get('spvs', [LookupController::class, 'spvs'])->name('spvs');
        Route::get('park/{park}', [LookupController::class, 'park'])->name('park');
        Route::get('land-ref', [LookupController::class, 'landRef'])->name('land-ref');
        Route::get('schedule/{schedule}/farmers', [LookupController::class, 'scheduleFarmers'])->name('schedule-farmers');
        Route::get('lands/{land}/entities', [LookupController::class, 'entities'])->name('entities');
    });

    /* masters */
    $crud('states', StateController::class, 'state', 'state');
    $crud('districts', DistrictController::class, 'district', 'district');
    $crud('cities', CityController::class, 'city', 'city');
    $crud('parks', ParkController::class, 'park', 'park');
    $crud('spvs', SpvController::class, 'spv', 'spv');

    /* land master: list, new parcel page, parcel workspace with tabs */
    Route::get('lands', [LandController::class, 'index'])->middleware('perm:land,view')->name('lands.index');
    Route::get('lands/create', [LandController::class, 'create'])->middleware('perm:land,add')->name('lands.create');
    Route::post('lands', [LandController::class, 'store'])->middleware('perm:land,add')->name('lands.store');
    Route::get('lands/{land}', [LandController::class, 'show'])->middleware('perm:land,view')->name('lands.show');
    Route::put('lands/{land}', [LandController::class, 'update'])->middleware('perm:land,edit')->name('lands.update');
    Route::delete('lands/{land}', [LandController::class, 'destroy'])->middleware('perm:land,delete')->name('lands.destroy');
    Route::put('lands/{land}/grid', [LandController::class, 'grid'])->middleware('perm:land,edit')->name('lands.grid');
    Route::get('lands/{land}/export/{tab}', [LandController::class, 'export'])->middleware('perm:land,view')->name('lands.export');

    $child('surveys', LandSurveyController::class, 'survey', 'survey');
    $child('farmers', FarmerController::class, 'farmer', 'farmer');
    $child('leases', LeaseController::class, 'lease', 'lease');
    $child('payments', PaymentController::class, 'payment', 'payment');
    $child('subleases', SubleaseController::class, 'sublease', 'sublease');
    $child('compliance', ComplianceController::class, 'compliance', 'compliance');

    Route::get('schedules/{schedule}/edit', [RentScheduleController::class, 'edit'])->middleware('perm:schedule,view')->name('schedules.edit');
    Route::put('schedules/{schedule}', [RentScheduleController::class, 'update'])->middleware('perm:schedule,edit')->name('schedules.update');
    Route::get('schedules/{schedule}/history', [RentScheduleController::class, 'history'])->middleware('perm:schedule,view')->name('schedules.history');
    Route::post('leases/{lease}/regenerate', [RentScheduleController::class, 'regenerate'])->middleware('perm:schedule,edit')->name('schedules.regenerate');

    Route::get('lands/{land}/documents/create', [DocumentController::class, 'create'])->middleware('perm:doc,add')->name('documents.create');
    Route::post('lands/{land}/documents', [DocumentController::class, 'store'])->middleware('perm:doc,add')->name('documents.store');
    Route::get('documents/{document}', [DocumentController::class, 'show'])->middleware('perm:doc,view')->name('documents.show');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->middleware('perm:doc,view')->name('documents.download');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->middleware('perm:doc,delete')->name('documents.destroy');

    /* reports */
    Route::get('reports', [ReportController::class, 'index'])->middleware('perm:report,view')->name('reports.index');
    Route::get('reports/{report}', [ReportController::class, 'show'])->middleware('perm:report,view')->name('reports.show');

    /* administration */
    Route::get('audit', [AuditLogController::class, 'index'])->middleware('perm:audit,view')->name('audit.index');
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
