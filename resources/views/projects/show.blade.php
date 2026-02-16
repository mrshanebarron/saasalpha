@extends('layouts.app')
@section('heading', $project->reference . ' — ' . $project->name)
@section('content')

{{-- Project Header --}}
<div class="bg-slate-900 rounded-xl border border-slate-800 p-6 mb-6">
    <div class="flex items-start justify-between mb-4">
        <div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-{{ $project->status_color }}-500/10 text-{{ $project->status_color }}-400 mb-2">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
            <h2 class="text-xl font-bold text-white">{{ $project->name }}</h2>
            <p class="text-sm text-slate-400 mt-1">{{ $project->client_company ?? $project->client_name }} · {{ ucfirst($project->project_type ?? 'General') }}</p>
        </div>
        <div class="text-right">
            <div class="text-xs text-slate-500">Budget</div>
            <div class="text-xl font-bold text-white">${{ number_format($project->budget ?? 0) }}</div>
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
        <div><span class="text-slate-500 block text-xs">Manager</span><span class="text-slate-300">{{ $project->manager?->name ?? '—' }}</span></div>
        <div><span class="text-slate-500 block text-xs">Start</span><span class="text-slate-300">{{ $project->start_date?->format('M d, Y') ?? '—' }}</span></div>
        <div><span class="text-slate-500 block text-xs">Target</span><span class="text-slate-300">{{ $project->target_date?->format('M d, Y') ?? '—' }}</span></div>
        <div><span class="text-slate-500 block text-xs">Spent</span><span class="text-slate-300">${{ number_format($project->spent) }} ({{ $project->budget_used_percent }}%)</span></div>
        <div><span class="text-slate-500 block text-xs">Hours Logged</span><span class="text-slate-300">{{ number_format($project->total_hours, 1) }}</span></div>
    </div>
    @if($project->description)
    <p class="text-sm text-slate-400 mt-4 border-t border-slate-800 pt-4">{{ $project->description }}</p>
    @endif
    <div class="mt-4"><div class="flex items-center gap-2"><span class="text-xs text-slate-500">Progress</span><div class="flex-1 bg-slate-800 rounded-full h-2"><div class="bg-brand-500 h-2 rounded-full transition-all" style="width:{{ $project->progress }}%"></div></div><span class="text-xs text-slate-400">{{ $project->progress }}%</span></div></div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    {{-- Deliverables --}}
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800"><h3 class="font-semibold text-white">Deliverables ({{ $project->deliverables->count() }})</h3></div>
        <div class="divide-y divide-slate-800/50">
            @foreach($project->deliverables as $d)
            <div class="px-5 py-3 flex items-center gap-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $d->status_color }}-500/10 text-{{ $d->status_color }}-400">{{ ucfirst(str_replace('_', ' ', $d->status)) }}</span>
                <div class="flex-1"><div class="text-sm text-slate-300">{{ $d->title }}</div><div class="text-xs text-slate-500">Rev {{ $d->revision }} · {{ ucfirst($d->type) }} · {{ $d->assignedTo?->name ?? 'Unassigned' }}</div></div>
                <span class="text-xs {{ $d->is_overdue ? 'text-red-400 font-medium' : 'text-slate-500' }}">{{ $d->due_date?->format('M d') }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Team & Time --}}
    <div class="space-y-6">
        <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-800"><h3 class="font-semibold text-white">Team</h3></div>
            <div class="divide-y divide-slate-800/50">
                @foreach($project->members as $m)
                <div class="px-5 py-3 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-brand-600/20 text-brand-400 flex items-center justify-center text-xs font-bold">{{ $m->user->initials }}</div>
                    <div class="flex-1"><div class="text-sm text-slate-300">{{ $m->user->name }}</div><div class="text-xs text-slate-500">{{ $m->user->job_title }}</div></div>
                    <span class="text-xs text-slate-500 capitalize">{{ $m->role }}</span>
                    <span class="text-xs text-slate-500">${{ number_format($m->hourly_rate) }}/hr</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-800"><h3 class="font-semibold text-white">Time by Category</h3></div>
            <div class="p-5 space-y-3">
                @foreach($timeByCategory as $tc)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-400 capitalize">{{ $tc->category }}</span>
                    <div class="flex items-center gap-4">
                        <span class="text-slate-300">{{ number_format($tc->total_hours, 1) }}h</span>
                        <span class="text-slate-500">${{ number_format($tc->total_cost) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Recent Time Entries --}}
<div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden mt-6">
    <div class="px-5 py-4 border-b border-slate-800"><h3 class="font-semibold text-white">Recent Time Entries</h3></div>
    <table class="w-full">
        <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-2 text-left">Date</th><th class="px-5 py-2 text-left">Staff</th><th class="px-5 py-2 text-left">Description</th>
            <th class="px-5 py-2 text-left">Category</th><th class="px-5 py-2 text-right">Hours</th><th class="px-5 py-2 text-left">Status</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-800/50">
            @foreach($project->timeEntries->take(10) as $te)
            <tr class="table-row">
                <td class="px-5 py-2 text-sm text-slate-400">{{ $te->date->format('M d') }}</td>
                <td class="px-5 py-2 text-sm text-slate-300">{{ $te->user->name }}</td>
                <td class="px-5 py-2 text-sm text-slate-400">{{ $te->description }}</td>
                <td class="px-5 py-2 text-sm text-slate-400 capitalize">{{ $te->category }}</td>
                <td class="px-5 py-2 text-sm text-slate-300 text-right">{{ number_format($te->hours, 1) }}</td>
                <td class="px-5 py-2"><span class="text-xs {{ $te->status === 'approved' ? 'text-green-400' : 'text-yellow-400' }}">{{ ucfirst($te->status) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
