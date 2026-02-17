<?php

namespace App\Http\Controllers;

use App\Models\CpdRecord;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class CpdController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = CpdRecord::where('tenant_id', $tenantId)
            ->with(['user', 'verifiedBy']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $records = $query->latest('completed_date')->paginate(20)->withQueryString();

        $userSummary = User::where('tenant_id', $tenantId)->where('is_active', true)->get()->map(function ($user) {
            $cpd = $user->cpdRecords;
            return (object)[
                'user' => $user,
                'total_hours' => $cpd->sum('hours'),
                'verified_hours' => $cpd->where('verified', true)->sum('hours'),
                'records_count' => $cpd->count(),
            ];
        });

        return view('cpd.index', compact('records', 'userSummary'));
    }

    public function create()
    {
        $users = User::where('tenant_id', auth()->user()->tenant_id)->where('is_active', true)->get();
        return view('cpd.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'category' => 'required|in:course,seminar,conference,self_study,mentoring,publication',
            'provider' => 'nullable|string|max:255',
            'hours' => 'required|numeric|min:0.5|max:100',
            'completed_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:completed_date',
            'certificate_number' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['verified'] = false;

        $record = CpdRecord::create($validated);
        AuditLog::log('created', $record);

        return redirect()->route('cpd.index')->with('success', 'CPD record added.');
    }

    public function edit(CpdRecord $cpdRecord)
    {
        $users = User::where('tenant_id', auth()->user()->tenant_id)->where('is_active', true)->get();
        return view('cpd.edit', compact('cpdRecord', 'users'));
    }

    public function update(Request $request, CpdRecord $cpdRecord)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:course,seminar,conference,self_study,mentoring,publication',
            'provider' => 'nullable|string|max:255',
            'hours' => 'required|numeric|min:0.5|max:100',
            'completed_date' => 'required|date',
            'expiry_date' => 'nullable|date',
            'certificate_number' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $cpdRecord->update($validated);
        AuditLog::log('updated', $cpdRecord);

        return redirect()->route('cpd.index')->with('success', 'CPD record updated.');
    }

    public function destroy(CpdRecord $cpdRecord)
    {
        AuditLog::log('deleted', $cpdRecord);
        $cpdRecord->delete();
        return redirect()->route('cpd.index')->with('success', 'CPD record deleted.');
    }

    public function verify(CpdRecord $cpdRecord)
    {
        $cpdRecord->update(['verified' => true, 'verified_by' => auth()->id()]);
        AuditLog::log('verified', $cpdRecord);
        return back()->with('success', 'CPD record verified.');
    }
}
