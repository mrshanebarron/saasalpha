<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Models\Project;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class TimeTrackingController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = TimeEntry::where('tenant_id', $tenantId)
            ->with(['user', 'project', 'deliverable', 'approvedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $entries = $query->latest('date')->paginate(30)->withQueryString();

        $weeklyHours = TimeEntry::where('tenant_id', $tenantId)->where('date', '>=', now()->startOfWeek())
            ->selectRaw('user_id, SUM(hours) as total, SUM(CASE WHEN billable = 1 THEN hours ELSE 0 END) as billable_hours')
            ->groupBy('user_id')->with('user')->get();

        $projectHours = TimeEntry::where('tenant_id', $tenantId)->where('date', '>=', now()->startOfMonth())
            ->selectRaw('project_id, SUM(hours) as total, SUM(hours * rate) as revenue')
            ->groupBy('project_id')->with('project')->get();

        $projects = Project::where('tenant_id', $tenantId)->where('status', 'active')->get();
        $users = User::where('tenant_id', $tenantId)->where('is_active', true)->get();

        return view('time-tracking.index', compact('entries', 'weeklyHours', 'projectHours', 'projects', 'users'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        $projects = Project::where('tenant_id', $tenantId)->where('status', 'active')->with('deliverables')->get();
        return view('time-tracking.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'deliverable_id' => 'nullable|exists:deliverables,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.25|max:24',
            'category' => 'required|in:engineering,review,admin,travel,meeting,design,inspection',
            'description' => 'required|string|max:500',
            'billable' => 'sometimes|boolean',
            'rate' => 'nullable|numeric|min:0',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'submitted';
        $validated['billable'] = $request->boolean('billable');
        $validated['rate'] = $validated['rate'] ?? 150;

        $entry = TimeEntry::create($validated);
        AuditLog::log('created', $entry);

        return redirect()->route('time-tracking.index')->with('success', 'Time entry logged.');
    }

    public function edit(TimeEntry $timeEntry)
    {
        $tenantId = auth()->user()->tenant_id;
        $projects = Project::where('tenant_id', $tenantId)->where('status', 'active')->with('deliverables')->get();
        return view('time-tracking.edit', compact('timeEntry', 'projects'));
    }

    public function update(Request $request, TimeEntry $timeEntry)
    {
        if ($timeEntry->status === 'approved') {
            return back()->with('error', 'Cannot edit an approved time entry.');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'deliverable_id' => 'nullable|exists:deliverables,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.25|max:24',
            'category' => 'required|in:engineering,review,admin,travel,meeting,design,inspection',
            'description' => 'required|string|max:500',
            'billable' => 'sometimes|boolean',
            'rate' => 'nullable|numeric|min:0',
        ]);

        $validated['billable'] = $request->boolean('billable');
        $timeEntry->update($validated);
        AuditLog::log('updated', $timeEntry);

        return redirect()->route('time-tracking.index')->with('success', 'Time entry updated.');
    }

    public function destroy(TimeEntry $timeEntry)
    {
        if ($timeEntry->status === 'approved') {
            return back()->with('error', 'Cannot delete an approved time entry.');
        }

        AuditLog::log('deleted', $timeEntry);
        $timeEntry->delete();
        return redirect()->route('time-tracking.index')->with('success', 'Time entry deleted.');
    }

    public function approve(TimeEntry $timeEntry)
    {
        $timeEntry->update(['status' => 'approved', 'approved_by' => auth()->id()]);
        AuditLog::log('approved', $timeEntry);
        return back()->with('success', 'Time entry approved.');
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:time_entries,id']);

        TimeEntry::whereIn('id', $validated['ids'])
            ->where('status', 'submitted')
            ->update(['status' => 'approved', 'approved_by' => auth()->id()]);

        return back()->with('success', count($validated['ids']) . ' entries approved.');
    }
}
