<?php

namespace App\Http\Controllers;

use App\Models\Subcontractor;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SubcontractorController extends Controller
{
    public function index(Request $request)
    {
        $query = Subcontractor::where('tenant_id', auth()->user()->tenant_id)
            ->withCount('complianceDocuments');

        if ($request->filled('search')) {
            $query->where('company_name', 'like', "%{$request->search}%");
        }

        $subcontractors = $query->orderBy('company_name')->paginate(20)->withQueryString();
        return view('subcontractors.index', compact('subcontractors'));
    }

    public function create()
    {
        return view('subcontractors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'specialty' => 'nullable|string|max:255',
            'default_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['status'] = 'active';

        $sub = Subcontractor::create($validated);
        AuditLog::log('created', $sub);

        return redirect()->route('subcontractors.show', $sub)->with('success', 'Subcontractor added.');
    }

    public function show(Subcontractor $subcontractor)
    {
        $subcontractor->load('complianceDocuments');
        return view('subcontractors.show', compact('subcontractor'));
    }

    public function edit(Subcontractor $subcontractor)
    {
        return view('subcontractors.edit', compact('subcontractor'));
    }

    public function update(Request $request, Subcontractor $subcontractor)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'specialty' => 'nullable|string|max:255',
            'default_rate' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $subcontractor->update($validated);
        AuditLog::log('updated', $subcontractor);

        return redirect()->route('subcontractors.show', $subcontractor)->with('success', 'Subcontractor updated.');
    }

    public function destroy(Subcontractor $subcontractor)
    {
        AuditLog::log('deleted', $subcontractor);
        $subcontractor->delete();
        return redirect()->route('subcontractors.index')->with('success', 'Subcontractor deleted.');
    }
}
