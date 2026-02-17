@extends('layouts.app')
@section('heading', 'Time Tracking')
@section('content')

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row gap-3 mb-6">
    <form method="GET" class="flex flex-1 gap-3">
        <select name="status" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none focus:border-brand-500">
            <option value="">All Statuses</option>
            @foreach(['draft','submitted','approved'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="user_id" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none focus:border-brand-500">
            <option value="">All Staff</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
        <select name="project_id" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none focus:border-brand-500">
            <option value="">All Projects</option>
            @foreach($projects as $p)
                <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->reference }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('time-tracking.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Log Time
    </a>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-6">
    {{-- Weekly Hours by Staff --}}
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800"><h3 class="font-semibold text-white">This Week by Staff</h3></div>
        <div class="divide-y divide-slate-800/50">
            @forelse($weeklyHours as $wh)
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
            @empty
            <div class="px-5 py-4 text-xs text-slate-500">No entries this week.</div>
            @endforelse
        </div>
    </div>

    {{-- Monthly Hours by Project --}}
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800"><h3 class="font-semibold text-white">This Month by Project</h3></div>
        <div class="divide-y divide-slate-800/50">
            @forelse($projectHours as $ph)
            <div class="px-5 py-3 flex items-center justify-between">
                <div><div class="text-sm text-slate-300">{{ $ph->project->name }}</div><div class="text-xs text-slate-500">{{ $ph->project->reference }}</div></div>
                <div class="text-right"><div class="text-sm font-medium text-slate-300">{{ number_format($ph->total, 1) }}h</div><div class="text-xs text-green-400">${{ number_format($ph->revenue) }}</div></div>
            </div>
            @empty
            <div class="px-5 py-4 text-xs text-slate-500">No entries this month.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Bulk Approve --}}
<form method="POST" action="{{ route('time-tracking.bulk-approve') }}" id="bulkForm">
    @csrf
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-semibold text-white">Time Entries</h3>
            <button type="submit" class="px-3 py-1.5 bg-green-600/20 text-green-400 rounded text-xs font-medium hover:bg-green-600/30 transition">Approve Selected</button>
        </div>
        <table class="w-full">
            <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
                <th class="px-5 py-2 text-left w-8"><input type="checkbox" onclick="document.querySelectorAll('.entry-cb').forEach(c=>c.checked=this.checked)" class="rounded bg-slate-800 border-slate-700"></th>
                <th class="px-5 py-2 text-left">Date</th><th class="px-5 py-2 text-left">Staff</th><th class="px-5 py-2 text-left">Project</th>
                <th class="px-5 py-2 text-left">Description</th><th class="px-5 py-2 text-left">Category</th><th class="px-5 py-2 text-right">Hours</th><th class="px-5 py-2 text-right">Amount</th><th class="px-5 py-2 text-left">Status</th><th class="px-5 py-2"></th>
            </tr></thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($entries as $e)
                <tr class="table-row">
                    <td class="px-5 py-2">@if($e->status === 'submitted')<input type="checkbox" name="ids[]" value="{{ $e->id }}" class="entry-cb rounded bg-slate-800 border-slate-700">@endif</td>
                    <td class="px-5 py-2 text-sm text-slate-400">{{ $e->date->format('M d') }}</td>
                    <td class="px-5 py-2 text-sm text-slate-300">{{ $e->user->name }}</td>
                    <td class="px-5 py-2 text-sm text-slate-400">{{ $e->project->reference }}</td>
                    <td class="px-5 py-2 text-sm text-slate-400 max-w-xs truncate">{{ $e->description }}</td>
                    <td class="px-5 py-2 text-sm text-slate-400 capitalize">{{ $e->category }}</td>
                    <td class="px-5 py-2 text-sm text-slate-300 text-right">{{ number_format($e->hours, 1) }}</td>
                    <td class="px-5 py-2 text-sm text-slate-300 text-right">{{ $e->billable ? '$' . number_format($e->amount) : '—' }}</td>
                    <td class="px-5 py-2"><span class="text-xs {{ $e->status === 'approved' ? 'text-green-400' : ($e->status === 'submitted' ? 'text-yellow-400' : 'text-slate-500') }}">{{ ucfirst($e->status) }}</span></td>
                    <td class="px-5 py-2 text-right flex gap-2 justify-end">
                        @if($e->status !== 'approved')
                        <a href="{{ route('time-tracking.edit', $e) }}" class="text-xs text-slate-500 hover:text-brand-400">Edit</a>
                        @endif
                        @if($e->status === 'submitted')
                        <a href="{{ route('time-tracking.approve', $e) }}" onclick="event.preventDefault();fetch(this.href,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>location.reload())" class="text-xs text-green-400 hover:text-green-300">Approve</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="px-5 py-8 text-center text-sm text-slate-500">No time entries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>
@if($entries->hasPages())
<div class="mt-4">{{ $entries->links() }}</div>
@endif
@endsection
