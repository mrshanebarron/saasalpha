<?php

namespace App\Http\Controllers;

use App\Models\{Project, Enquiry, Quote, ComplianceDocument, TimeEntry, Deliverable, Notification};

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $tid = $tenant->id;

        $stats = [
            'active_projects' => Project::where('tenant_id', $tid)->where('status', 'active')->count(),
            'open_enquiries' => Enquiry::where('tenant_id', $tid)->whereNotIn('status', ['converted', 'declined'])->count(),
            'pending_quotes' => Quote::where('tenant_id', $tid)->whereIn('status', ['draft', 'sent'])->count(),
            'pipeline_value' => Quote::where('tenant_id', $tid)->whereIn('status', ['draft', 'sent'])->sum('total'),
            'expiring_docs' => ComplianceDocument::where('tenant_id', $tid)->where('expiry_date', '<=', now()->addDays(30))->count(),
            'hours_this_week' => TimeEntry::where('tenant_id', $tid)->where('date', '>=', now()->startOfWeek())->sum('hours'),
            'revenue_this_month' => TimeEntry::where('tenant_id', $tid)->where('billable', true)->where('date', '>=', now()->startOfMonth())->selectRaw('SUM(hours * rate) as total')->value('total') ?? 0,
            'overdue_deliverables' => Deliverable::where('tenant_id', $tid)->where('due_date', '<', now())->whereNotIn('status', ['approved', 'delivered'])->count(),
        ];

        $projects = Project::where('tenant_id', $tid)->where('status', 'active')->with('manager')->latest()->take(5)->get();
        $enquiries = Enquiry::where('tenant_id', $tid)->whereNotIn('status', ['converted', 'declined'])->with('assignedTo')->latest()->take(5)->get();
        $expiringDocs = ComplianceDocument::where('tenant_id', $tid)->where('expiry_date', '<=', now()->addDays(45))->orderBy('expiry_date')->take(5)->get();
        $recentTime = TimeEntry::where('tenant_id', $tid)->with(['user', 'project'])->latest()->take(8)->get();
        $notifications = Notification::where('user_id', auth()->id())->where('is_read', false)->latest()->take(5)->get();
        $upcomingDeliverables = Deliverable::where('tenant_id', $tid)->whereNotIn('status', ['approved', 'delivered'])->with(['project', 'assignedTo'])->orderBy('due_date')->take(5)->get();

        return view('dashboard.index', compact('stats', 'projects', 'enquiries', 'expiringDocs', 'recentTime', 'notifications', 'upcomingDeliverables', 'tenant'));
    }
}
