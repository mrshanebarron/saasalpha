<?php

namespace App\Http\Controllers;

use App\Models\{Project, Enquiry, Quote, ComplianceDocument, TimeEntry, Deliverable, Notification};
use Illuminate\Http\Request;

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

    /**
     * AI Suggestion: Client name autocomplete from existing enquiries, quotes, and projects.
     */
    public function suggestClients(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $clients = collect();

        // Gather unique client names from enquiries, quotes, and projects
        $clients = $clients->merge(
            Enquiry::where('tenant_id', $tid)
                ->where('client_name', 'like', "%{$q}%")
                ->pluck('client_name')
        );
        $clients = $clients->merge(
            Quote::where('tenant_id', $tid)
                ->where('client_name', 'like', "%{$q}%")
                ->pluck('client_name')
        );
        $clients = $clients->merge(
            Project::where('tenant_id', $tid)
                ->where('client_name', 'like', "%{$q}%")
                ->pluck('client_name')
        );

        return response()->json(
            $clients->unique()->values()->take(10)
        );
    }

    /**
     * AI Suggestion: Scope of work suggestions based on past quotes for similar clients.
     */
    public function suggestScope(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $clientName = $request->get('client', '');
        $projectType = $request->get('type', '');

        $query = Quote::where('tenant_id', $tid)
            ->whereNotNull('scope_of_work')
            ->where('scope_of_work', '!=', '');

        if ($clientName) {
            $query->where('client_name', 'like', "%{$clientName}%");
        }

        $scopes = $query->orderByDesc('created_at')
            ->take(5)
            ->pluck('scope_of_work');

        // Also suggest from enquiry descriptions
        $enquiryScopes = Enquiry::where('tenant_id', $tid)
            ->whereNotNull('description')
            ->where('description', '!=', '');

        if ($projectType) {
            $enquiryScopes->where('project_type', $projectType);
        }

        $enquiryScopes = $enquiryScopes->orderByDesc('created_at')
            ->take(3)
            ->pluck('description');

        return response()->json([
            'scopes' => $scopes->merge($enquiryScopes)->unique()->values()->take(5),
            'defaults' => $this->getSmartDefaults($tid),
        ]);
    }

    /**
     * Pattern-based smart defaults: most common values from recent entries.
     */
    private function getSmartDefaults(int $tenantId): array
    {
        // Most used time entry category (last 30 days)
        $topCategory = TimeEntry::where('tenant_id', $tenantId)
            ->where('date', '>=', now()->subDays(30))
            ->selectRaw('category, COUNT(*) as cnt')
            ->groupBy('category')
            ->orderByDesc('cnt')
            ->value('category') ?? 'engineering';

        // Most common rate
        $topRate = TimeEntry::where('tenant_id', $tenantId)
            ->where('date', '>=', now()->subDays(30))
            ->selectRaw('rate, COUNT(*) as cnt')
            ->groupBy('rate')
            ->orderByDesc('cnt')
            ->value('rate') ?? 150;

        // Most common tax rate on quotes
        $topTaxRate = Quote::where('tenant_id', $tenantId)
            ->selectRaw('tax_rate, COUNT(*) as cnt')
            ->groupBy('tax_rate')
            ->orderByDesc('cnt')
            ->value('tax_rate') ?? 13;

        // Most common enquiry source
        $topSource = Enquiry::where('tenant_id', $tenantId)
            ->selectRaw('source, COUNT(*) as cnt')
            ->groupBy('source')
            ->orderByDesc('cnt')
            ->value('source') ?? 'direct';

        return [
            'time_category' => $topCategory,
            'hourly_rate' => (float) $topRate,
            'tax_rate' => (float) $topTaxRate,
            'enquiry_source' => $topSource,
        ];
    }
}
