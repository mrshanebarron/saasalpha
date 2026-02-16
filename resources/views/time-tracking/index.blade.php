@extends('layouts.app')
@section('heading', 'Time Tracking')
@section('content')

<div class="grid lg:grid-cols-2 gap-6 mb-6">
    {{-- Weekly Hours by Staff --}}
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800"><h3 class="font-semibold text-white">This Week by Staff</h3></div>
        <div class="divide-y divide-slate-800/50">
            @foreach($weeklyHours as $wh)
            <div class="px-5 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-600/20 text-blue-400 flex items-center justify-center text-xs font-bold">{{ $wh->user->initials }}</div>
                    <span class="text-sm text-slate-300">{{ $wh->user->name }}</span>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-slate-300 font-medium">{{ number_format($wh->total, 1) }}h</span>
                    <span class="text-green-400 text-xs">{{ number_format($wh->billable_hours, 1) }}h billable</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Monthly Hours by Project --}}
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800"><h3 class="font-semibold text-white">This Month by Project</h3></div>
        <div class="divide-y divide-slate-800/50">
            @foreach($projectHours as $ph)
            <div class="px-5 py-3 flex items-center justify-between">
                <div><div class="text-sm text-slate-300">{{ $ph->project->name }}</div><div class="text-xs text-slate-500">{{ $ph->project->reference }}</div></div>
                <div class="text-right"><div class="text-sm font-medium text-slate-300">{{ number_format($ph->total, 1) }}h</div><div class="text-xs text-green-400">${{ number_format($ph->revenue) }}</div></div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- All Entries --}}
<div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-800"><h3 class="font-semibold text-white">Recent Time Entries</h3></div>
    <table class="w-full">
        <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-2 text-left">Date</th><th class="px-5 py-2 text-left">Staff</th><th class="px-5 py-2 text-left">Project</th>
            <th class="px-5 py-2 text-left">Description</th><th class="px-5 py-2 text-left">Category</th><th class="px-5 py-2 text-right">Hours</th><th class="px-5 py-2 text-right">Amount</th><th class="px-5 py-2 text-left">Status</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-800/50">
            @foreach($entries->take(30) as $e)
            <tr class="table-row">
                <td class="px-5 py-2 text-sm text-slate-400">{{ $e->date->format('M d') }}</td>
                <td class="px-5 py-2 text-sm text-slate-300">{{ $e->user->name }}</td>
                <td class="px-5 py-2 text-sm text-slate-400">{{ $e->project->reference }}</td>
                <td class="px-5 py-2 text-sm text-slate-400 max-w-xs truncate">{{ $e->description }}</td>
                <td class="px-5 py-2 text-sm text-slate-400 capitalize">{{ $e->category }}</td>
                <td class="px-5 py-2 text-sm text-slate-300 text-right">{{ number_format($e->hours, 1) }}</td>
                <td class="px-5 py-2 text-sm text-slate-300 text-right">{{ $e->billable ? '$' . number_format($e->amount) : '—' }}</td>
                <td class="px-5 py-2"><span class="text-xs {{ $e->status === 'approved' ? 'text-green-400' : ($e->status === 'submitted' ? 'text-yellow-400' : 'text-slate-500') }}">{{ ucfirst($e->status) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
