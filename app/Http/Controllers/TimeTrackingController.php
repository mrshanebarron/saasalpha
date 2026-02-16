<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;

class TimeTrackingController extends Controller
{
    public function index()
    {
        $tid = auth()->user()->tenant_id;
        $entries = TimeEntry::where('tenant_id', $tid)->with(['user', 'project', 'deliverable'])->latest('date')->take(100)->get();

        $weeklyHours = TimeEntry::where('tenant_id', $tid)->where('date', '>=', now()->startOfWeek())
            ->selectRaw('user_id, SUM(hours) as total, SUM(CASE WHEN billable = 1 THEN hours ELSE 0 END) as billable_hours')
            ->groupBy('user_id')->with('user')->get();

        $projectHours = TimeEntry::where('tenant_id', $tid)->where('date', '>=', now()->startOfMonth())
            ->selectRaw('project_id, SUM(hours) as total, SUM(hours * rate) as revenue')
            ->groupBy('project_id')->with('project')->get();

        return view('time-tracking.index', compact('entries', 'weeklyHours', 'projectHours'));
    }
}
