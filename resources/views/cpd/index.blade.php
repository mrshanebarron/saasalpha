@extends('layouts.app')
@section('heading', 'CPD Tracking')
@section('content')

{{-- Staff Summary --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    @foreach($userSummary->where('records_count', '>', 0) as $us)
    <div class="stat-card rounded-xl p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-full bg-blue-600/20 text-blue-400 flex items-center justify-center text-xs font-bold">{{ $us->user->initials }}</div>
            <div><div class="text-sm font-medium text-white">{{ $us->user->name }}</div><div class="text-xs text-slate-500">{{ $us->user->job_title }}</div></div>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <span class="text-slate-400">{{ number_format($us->total_hours) }}h total</span>
            <span class="text-green-400">{{ number_format($us->verified_hours) }}h verified</span>
            <span class="text-slate-500">{{ $us->records_count }} records</span>
        </div>
    </div>
    @endforeach
</div>

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row gap-3 mb-6">
    <form method="GET" class="flex flex-1 gap-3">
        <select name="user_id" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none focus:border-brand-500">
            <option value="">All Staff</option>
            @foreach($userSummary as $us)
                <option value="{{ $us->user->id }}" {{ request('user_id') == $us->user->id ? 'selected' : '' }}>{{ $us->user->name }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('cpd.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add CPD Record
    </a>
</div>

{{-- Records Table --}}
<div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
    <table class="w-full">
        <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-3 text-left">Title</th><th class="px-5 py-3 text-left">Staff</th><th class="px-5 py-3 text-left">Category</th>
            <th class="px-5 py-3 text-left">Provider</th><th class="px-5 py-3 text-right">Hours</th><th class="px-5 py-3 text-left">Completed</th><th class="px-5 py-3 text-center">Verified</th><th class="px-5 py-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-slate-800/50">
            @forelse($records as $r)
            <tr class="table-row">
                <td class="px-5 py-3 text-sm text-slate-300">{{ $r->title }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $r->user->name }}</td>
                <td class="px-5 py-3 text-sm text-slate-400 capitalize">{{ str_replace('_', ' ', $r->category) }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $r->provider ?? '—' }}</td>
                <td class="px-5 py-3 text-sm text-slate-300 text-right">{{ number_format($r->hours, 1) }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $r->completed_date->format('M d, Y') }}</td>
                <td class="px-5 py-3 text-center">
                    @if($r->verified)
                        <span class="text-green-400">&#10003;</span>
                    @else
                        <form method="POST" action="{{ route('cpd.verify', $r) }}" class="inline">@csrf
                            <button type="submit" class="text-xs text-brand-400 hover:text-brand-300">Verify</button>
                        </form>
                    @endif
                </td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('cpd.edit', $r) }}" class="text-xs text-slate-500 hover:text-brand-400">Edit</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-5 py-8 text-center text-sm text-slate-500">No CPD records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($records->hasPages())
<div class="mt-4">{{ $records->links() }}</div>
@endif
@endsection
