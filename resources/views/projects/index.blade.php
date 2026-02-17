@extends('layouts.app')
@section('heading', 'Projects')
@section('content')

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row gap-3 mb-6">
    <form method="GET" class="flex flex-1 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search project, client, reference..." class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-500">
        <select name="status" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none focus:border-brand-500">
            <option value="">All Statuses</option>
            @foreach(['active','on_hold','completed','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg text-sm hover:bg-slate-700">Filter</button>
    </form>
    <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Project
    </a>
</div>

<div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
    <table class="w-full">
        <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-3 text-left">Project</th><th class="px-5 py-3 text-left">Client</th><th class="px-5 py-3 text-left">Type</th>
            <th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-left">Progress</th><th class="px-5 py-3 text-right">Budget</th><th class="px-5 py-3 text-left">Manager</th><th class="px-5 py-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-slate-800/50">
            @forelse($projects as $p)
            <tr class="table-row">
                <td class="px-5 py-3"><a href="{{ route('projects.show', $p) }}" class="text-sm font-medium text-brand-400 hover:text-brand-300">{{ $p->reference }}</a><div class="text-xs text-slate-500 mt-0.5">{{ $p->name }}</div></td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $p->client_company ?? $p->client_name }}</td>
                <td class="px-5 py-3 text-sm text-slate-400 capitalize">{{ $p->project_type ?? '—' }}</td>
                <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $p->status_color }}-500/10 text-{{ $p->status_color }}-400">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</span></td>
                <td class="px-5 py-3"><div class="flex items-center gap-2"><div class="w-20 bg-slate-800 rounded-full h-1.5"><div class="bg-brand-500 h-1.5 rounded-full" style="width:{{ $p->progress }}%"></div></div><span class="text-xs text-slate-500">{{ $p->progress }}%</span></div></td>
                <td class="px-5 py-3 text-sm text-slate-400 text-right">${{ number_format($p->budget ?? 0) }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $p->manager?->name ?? '—' }}</td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('projects.edit', $p) }}" class="text-xs text-slate-500 hover:text-brand-400">Edit</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-5 py-8 text-center text-sm text-slate-500">No projects found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($projects->hasPages())
<div class="mt-4">{{ $projects->links() }}</div>
@endif
@endsection
