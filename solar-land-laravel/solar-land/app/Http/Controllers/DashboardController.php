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

        return view('dashboard', ['d' => $dash->build($u), 'recent' => $u->hasPermission('audit') ? AuditLog::latest('id')->limit(6)->get() : collect()]);
    }
}
