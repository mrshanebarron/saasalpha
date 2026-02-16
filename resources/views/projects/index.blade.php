@extends('layouts.app')
@section('heading', 'Projects')
@section('content')
<div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
    <table class="w-full">
        <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-3 text-left">Project</th><th class="px-5 py-3 text-left">Client</th><th class="px-5 py-3 text-left">Type</th>
            <th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-left">Progress</th><th class="px-5 py-3 text-right">Budget</th><th class="px-5 py-3 text-left">Manager</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-800/50">
            @foreach($projects as $p)
            <tr class="table-row">
                <td class="px-5 py-3"><a href="{{ route('projects.show', $p) }}" class="text-sm font-medium text-brand-400 hover:text-brand-300">{{ $p->reference }}</a><div class="text-xs text-slate-500 mt-0.5">{{ $p->name }}</div></td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $p->client_company ?? $p->client_name }}</td>
                <td class="px-5 py-3 text-sm text-slate-400 capitalize">{{ $p->project_type ?? '—' }}</td>
                <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $p->status_color }}-500/10 text-{{ $p->status_color }}-400">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</span></td>
                <td class="px-5 py-3"><div class="flex items-center gap-2"><div class="w-20 bg-slate-800 rounded-full h-1.5"><div class="bg-brand-500 h-1.5 rounded-full" style="width:{{ $p->progress }}%"></div></div><span class="text-xs text-slate-500">{{ $p->progress }}%</span></div></td>
                <td class="px-5 py-3 text-sm text-slate-400 text-right">${{ number_format($p->budget ?? 0) }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $p->manager?->name ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
