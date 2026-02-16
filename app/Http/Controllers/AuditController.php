<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;

class AuditController extends Controller
{
    public function index()
    {
        $logs = AuditLog::where('tenant_id', auth()->user()->tenant_id)
            ->with('user')
            ->latest()
            ->take(100)
            ->get();
        return view('audit.index', compact('logs'));
    }
}
