<?php

namespace App\Http\Controllers;

use App\Models\Subcontractor;

class SubcontractorController extends Controller
{
    public function index()
    {
        $subcontractors = Subcontractor::where('tenant_id', auth()->user()->tenant_id)
            ->withCount('complianceDocuments')
            ->get();
        return view('subcontractors.index', compact('subcontractors'));
    }

    public function show(Subcontractor $subcontractor)
    {
        $subcontractor->load('complianceDocuments');
        return view('subcontractors.show', compact('subcontractor'));
    }
}
