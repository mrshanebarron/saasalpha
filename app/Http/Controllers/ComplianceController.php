<?php

namespace App\Http\Controllers;

use App\Models\ComplianceDocument;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Subcontractor;
use Illuminate\Http\Request;

class ComplianceController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = ComplianceDocument::where('tenant_id', $tenantId)
            ->with(['holder', 'subcontractor']);

        if ($request->filled('status')) {
            if ($request->status === 'expired') {
                $query->where('expiry_date', '<', now());
            } elseif ($request->status === 'expiring_soon') {
                $query->where('expiry_date', '>=', now())->where('expiry_date', '<=', now()->addDays(30));
            } elseif ($request->status === 'valid') {
                $query->where('expiry_date', '>', now()->addDays(30));
            }
        }

        $documents = $query->orderBy('expiry_date')->paginate(20)->withQueryString();

        $stats = [
            'total' => ComplianceDocument::where('tenant_id', $tenantId)->count(),
            'valid' => ComplianceDocument::where('tenant_id', $tenantId)->where('expiry_date', '>', now()->addDays(30))->count(),
            'expiring' => ComplianceDocument::where('tenant_id', $tenantId)->where('expiry_date', '>=', now())->where('expiry_date', '<=', now()->addDays(30))->count(),
            'expired' => ComplianceDocument::where('tenant_id', $tenantId)->where('expiry_date', '<', now())->count(),
        ];

        return view('compliance.index', compact('documents', 'stats'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        $users = User::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $subcontractors = Subcontractor::where('tenant_id', $tenantId)->get();
        return view('compliance.create', compact('users', 'subcontractors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:license,insurance,safety,permit,certification,other',
            'holder_id' => 'nullable|exists:users,id',
            'subcontractor_id' => 'nullable|exists:subcontractors,id',
            'issuing_body' => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:100',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'required|date',
            'reminder_days' => 'nullable|integer|min:1|max:365',
            'is_critical' => 'sometimes|boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['status'] = 'valid';
        $validated['is_critical'] = $request->boolean('is_critical');
        $validated['reminder_days'] = $validated['reminder_days'] ?? 30;

        $doc = ComplianceDocument::create($validated);
        AuditLog::log('created', $doc);

        return redirect()->route('compliance.show', $doc)->with('success', 'Compliance document added.');
    }

    public function show(ComplianceDocument $complianceDocument)
    {
        $complianceDocument->load(['holder', 'subcontractor']);
        return view('compliance.show', compact('complianceDocument'));
    }

    public function edit(ComplianceDocument $complianceDocument)
    {
        $tenantId = auth()->user()->tenant_id;
        $users = User::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $subcontractors = Subcontractor::where('tenant_id', $tenantId)->get();
        return view('compliance.edit', compact('complianceDocument', 'users', 'subcontractors'));
    }

    public function update(Request $request, ComplianceDocument $complianceDocument)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:license,insurance,safety,permit,certification,other',
            'issuing_body' => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:100',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'required|date',
            'reminder_days' => 'nullable|integer|min:1|max:365',
            'is_critical' => 'sometimes|boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_critical'] = $request->boolean('is_critical');
        $complianceDocument->update($validated);
        AuditLog::log('updated', $complianceDocument);

        return redirect()->route('compliance.show', $complianceDocument)->with('success', 'Document updated.');
    }

    public function destroy(ComplianceDocument $complianceDocument)
    {
        AuditLog::log('deleted', $complianceDocument);
        $complianceDocument->delete();
        return redirect()->route('compliance.index')->with('success', 'Document deleted.');
    }
}
