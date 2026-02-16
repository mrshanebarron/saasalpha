@extends('layouts.app')
@section('heading', 'Dashboard')
@section('content')

{{-- Stats Grid --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @php $statCards = [
        ['Active Projects', $stats['active_projects'], 'text-blue-400', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
        ['Open Enquiries', $stats['open_enquiries'], 'text-amber-400', 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Pipeline Value', '$' . number_format($stats['pipeline_value']), 'text-green-400', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Hours This Week', number_format($stats['hours_this_week'], 1), 'text-purple-400', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    ]; @endphp
    @foreach($statCards as $card)
    <div class="stat-card rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">{{ $card[0] }}</span>
            <svg class="w-5 h-5 {{ $card[2] }} opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $card[3] }}"/></svg>
        </div>
        <div class="text-2xl font-bold text-white">{{ $card[1] }}</div>
    </div>
    @endforeach
</div>

{{-- Second Stats Row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="stat-card rounded-xl p-5">
        <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Monthly Revenue</span>
        <div class="text-2xl font-bold text-white mt-2">${{ number_format($stats['revenue_this_month']) }}</div>
    </div>
    <div class="stat-card rounded-xl p-5">
        <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Pending Quotes</span>
        <div class="text-2xl font-bold text-white mt-2">{{ $stats['pending_quotes'] }}</div>
    </div>
    <div class="stat-card rounded-xl p-5">
        <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Expiring Docs</span>
        <div class="text-2xl font-bold {{ $stats['expiring_docs'] > 0 ? 'text-amber-400' : 'text-white' }} mt-2">{{ $stats['expiring_docs'] }}</div>
    </div>
    <div class="stat-card rounded-xl p-5">
        <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Overdue Deliverables</span>
        <div class="text-2xl font-bold {{ $stats['overdue_deliverables'] > 0 ? 'text-red-400' : 'text-white' }} mt-2">{{ $stats['overdue_deliverables'] }}</div>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Active Projects --}}
    <div class="lg:col-span-2 bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-semibold text-white">Active Projects</h3>
            <a href="{{ route('projects.index') }}" class="text-xs text-brand-400 hover:text-brand-300">View all</a>
        </div>
        <div class="divide-y divide-slate-800/50">
            @foreach($projects as $project)
            <a href="{{ route('projects.show', $project) }}" class="flex items-center gap-4 px-5 py-3 table-row">
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-slate-200">{{ $project->name }}</div>
                    <div class="text-xs text-slate-500">{{ $project->reference }} · {{ $project->client_company ?? $project->client_name }}</div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="w-24 bg-slate-800 rounded-full h-1.5 mb-1">
                        <div class="bg-brand-500 h-1.5 rounded-full" style="width: {{ $project->progress }}%"></div>
                    </div>
                    <span class="text-xs text-slate-500">{{ $project->progress }}%</span>
                </div>
                <span class="text-xs text-slate-500 w-16 text-right">{{ $project->manager?->name ? explode(' ', $project->manager->name)[0] : '—' }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Notifications --}}
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800">
            <h3 class="font-semibold text-white">Notifications</h3>
        </div>
        <div class="divide-y divide-slate-800/50">
            @forelse($notifications as $n)
            <div class="px-5 py-3">
                <div class="text-sm text-slate-300">{{ $n->title }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $n->message }}</div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-slate-600">No new notifications</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Bottom Grid --}}
<div class="grid lg:grid-cols-2 gap-6 mt-6">
    {{-- Upcoming Deliverables --}}
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800">
            <h3 class="font-semibold text-white">Upcoming Deliverables</h3>
        </div>
        <div class="divide-y divide-slate-800/50">
            @foreach($upcomingDeliverables as $d)
            <div class="px-5 py-3 flex items-center gap-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $d->status_color }}-500/10 text-{{ $d->status_color }}-400">{{ ucfirst(str_replace('_', ' ', $d->status)) }}</span>
                <div class="flex-1 min-w-0">
                    <div class="text-sm text-slate-300 truncate">{{ $d->title }}</div>
                    <div class="text-xs text-slate-500">{{ $d->project?->reference }}</div>
                </div>
                <div class="text-xs text-slate-500 flex-shrink-0 {{ $d->is_overdue ? 'text-red-400 font-medium' : '' }}">
                    {{ $d->due_date?->format('M d') }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Compliance Alerts --}}
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-semibold text-white">Compliance Alerts</h3>
            <a href="{{ route('compliance.index') }}" class="text-xs text-brand-400 hover:text-brand-300">View all</a>
        </div>
        <div class="divide-y divide-slate-800/50">
            @forelse($expiringDocs as $doc)
            <div class="px-5 py-3 flex items-center gap-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $doc->status_color }}-500/10 text-{{ $doc->status_color }}-400">
                    {{ $doc->computed_status === 'expired' ? 'Expired' : ($doc->days_until_expiry . 'd') }}
                </span>
                <div class="flex-1 min-w-0">
                    <div class="text-sm text-slate-300 truncate">{{ $doc->title }}</div>
                    <div class="text-xs text-slate-500">{{ $doc->holder?->name ?? $doc->subcontractor?->company_name ?? 'Company' }}</div>
                </div>
                <div class="text-xs text-slate-500">{{ $doc->expiry_date?->format('M d, Y') }}</div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-slate-600">All documents current</div>
            @endforelse
        </div>
    </div>
</div>

@endsection
