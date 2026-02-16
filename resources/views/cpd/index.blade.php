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

{{-- Records Table --}}
<div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
    <table class="w-full">
        <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-3 text-left">Title</th><th class="px-5 py-3 text-left">Staff</th><th class="px-5 py-3 text-left">Category</th>
            <th class="px-5 py-3 text-left">Provider</th><th class="px-5 py-3 text-right">Hours</th><th class="px-5 py-3 text-left">Completed</th><th class="px-5 py-3 text-center">Verified</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-800/50">
            @foreach($records as $r)
            <tr class="table-row">
                <td class="px-5 py-3 text-sm text-slate-300">{{ $r->title }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $r->user->name }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $r->category_label }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $r->provider ?? '—' }}</td>
                <td class="px-5 py-3 text-sm text-slate-300 text-right">{{ number_format($r->hours) }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $r->completed_date->format('M d, Y') }}</td>
                <td class="px-5 py-3 text-center">
                    @if($r->verified)<span class="text-green-400">&#10003;</span>@else<span class="text-slate-600">&#8212;</span>@endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
