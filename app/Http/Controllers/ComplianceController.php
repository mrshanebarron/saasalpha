<?php

namespace App\Http\Controllers;

use App\Models\ComplianceDocument;

class ComplianceController extends Controller
{
    public function index()
    {
        $documents = ComplianceDocument::where('tenant_id', auth()->user()->tenant_id)
            ->with(['holder', 'subcontractor'])
            ->orderBy('expiry_date')
            ->get();

        $stats = [
            'total' => $documents->count(),
            'valid' => $documents->filter(fn($d) => $d->computed_status === 'valid')->count(),
            'expiring' => $documents->filter(fn($d) => $d->computed_status === 'expiring_soon')->count(),
            'expired' => $documents->filter(fn($d) => $d->computed_status === 'expired')->count(),
        ];

        return view('compliance.index', compact('documents', 'stats'));
    }

    public function show(ComplianceDocument $complianceDocument)
    {
        $complianceDocument->load(['holder', 'subcontractor']);
        return view('compliance.show', compact('complianceDocument'));
    }
}
