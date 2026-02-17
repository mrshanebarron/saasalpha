<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Deliverable;
use App\Models\ProjectMember;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::where('tenant_id', auth()->user()->tenant_id)
            ->with(['manager', 'quote'])
            ->withCount(['deliverables', 'timeEntries']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('client_name', 'like', "%{$request->search}%")
                  ->orWhere('reference', 'like', "%{$request->search}%");
            });
        }

        $projects = $query->latest()->paginate(20)->withQueryString();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $users = User::where('tenant_id', auth()->user()->tenant_id)->where('is_active', true)->get();
        return view('projects.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'project_type' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'target_date' => 'nullable|date|after_or_equal:start_date',
            'project_manager_id' => 'required|exists:users,id',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $validated['tenant_id'] = $tenantId;
        $validated['status'] = 'active';
        $validated['spent'] = 0;
        $validated['progress'] = 0;
        $validated['reference'] = 'PRJ-' . str_pad(Project::where('tenant_id', $tenantId)->count() + 1, 4, '0', STR_PAD_LEFT);

        $project = Project::create($validated);
        AuditLog::log('created', $project);

        return redirect()->route('projects.show', $project)->with('success', 'Project created.');
    }

    public function show(Project $project)
    {
        $project->load([
            'manager', 'quote.lineItems', 'deliverables.assignedTo', 'generatedDocuments',
            'members.user', 'timeEntries' => fn($q) => $q->with('user')->latest()->limit(20),
        ]);
        $timeByCategory = $project->timeEntries()->selectRaw('category, SUM(hours) as total_hours, SUM(hours * rate) as total_cost')->groupBy('category')->get();
        $timeByUser = $project->timeEntries()->selectRaw('user_id, SUM(hours) as total_hours')->groupBy('user_id')->with('user')->get();
        $users = User::where('tenant_id', auth()->user()->tenant_id)->where('is_active', true)->get();
        return view('projects.show', compact('project', 'timeByCategory', 'timeByUser', 'users'));
    }

    public function edit(Project $project)
    {
        $users = User::where('tenant_id', auth()->user()->tenant_id)->where('is_active', true)->get();
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        $old = $project->only(['status', 'budget', 'progress']);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'project_type' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:active,on_hold,completed,cancelled',
            'budget' => 'nullable|numeric|min:0',
            'progress' => 'nullable|integer|min:0|max:100',
            'start_date' => 'required|date',
            'target_date' => 'nullable|date',
            'project_manager_id' => 'required|exists:users,id',
        ]);

        if ($validated['status'] === 'completed' && !$project->completed_date) {
            $validated['completed_date'] = now();
        }

        $project->update($validated);
        AuditLog::log('updated', $project, $old, $project->only(['status', 'budget', 'progress']));

        return redirect()->route('projects.show', $project)->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        AuditLog::log('deleted', $project);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }

    public function storeDeliverable(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:report,calculation,drawing,inspection,review,other',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $validated['tenant_id'] = $project->tenant_id;
        $validated['project_id'] = $project->id;
        $validated['status'] = 'pending';

        $deliverable = Deliverable::create($validated);
        AuditLog::log('created', $deliverable);

        return back()->with('success', 'Deliverable added.');
    }

    public function updateDeliverable(Request $request, Project $project, Deliverable $deliverable)
    {
        $old = $deliverable->only(['status']);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,review,approved,delivered',
        ]);

        if (in_array($validated['status'], ['approved', 'delivered']) && !$deliverable->delivered_date) {
            $validated['delivered_date'] = now();
            $validated['reviewed_by'] = auth()->id();
        }

        $deliverable->update($validated);
        AuditLog::log('updated', $deliverable, $old, $validated);

        return back()->with('success', 'Deliverable status updated.');
    }

    public function storeMember(Request $request, Project $project)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|max:100',
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        $validated['project_id'] = $project->id;
        ProjectMember::create($validated);

        return back()->with('success', 'Team member added.');
    }

    public function destroyMember(Project $project, ProjectMember $projectMember)
    {
        $projectMember->delete();
        return back()->with('success', 'Team member removed.');
    }
}
