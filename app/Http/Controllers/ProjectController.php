<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('tenant_id', auth()->user()->tenant_id)
            ->with('manager')
            ->withCount('deliverables', 'timeEntries')
            ->latest()
            ->get();
        return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        $project->load(['manager', 'members.user', 'deliverables.assignedTo', 'timeEntries' => fn($q) => $q->with('user')->latest()->take(20), 'quote.lineItems', 'generatedDocuments']);
        $timeByCategory = $project->timeEntries()->selectRaw('category, SUM(hours) as total_hours, SUM(hours * rate) as total_cost')->groupBy('category')->get();
        $timeByUser = $project->timeEntries()->selectRaw('user_id, SUM(hours) as total_hours')->groupBy('user_id')->with('user')->get();
        return view('projects.show', compact('project', 'timeByCategory', 'timeByUser'));
    }
}
