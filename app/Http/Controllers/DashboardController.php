<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardService $dash)
    {
        $u = $request->user();

        return view('dashboard', [
            'm' => $dash->metrics($u),
            'due' => $u->hasPermission('schedule') ? $dash->dueRent($u) : collect(),
            'parks' => $dash->parkCapacity($u),
            'leaseUse' => $dash->leaseUse($u),
            'recent' => $u->hasPermission('audit') ? AuditLog::latest('id')->limit(6)->get() : collect(),
        ]);
    }
}
