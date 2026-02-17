@extends('layouts.app')
@section('heading', 'Enquiries')
@section('content')

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row gap-3 mb-6">
    <form method="GET" class="flex flex-1 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search client, company, reference..." class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-500">
        <select name="status" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none focus:border-brand-500">
            <option value="">All Statuses</option>
            @foreach(['new','reviewing','qualified','converted','declined'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg text-sm hover:bg-slate-700">Filter</button>
    </form>
    <a href="{{ route('enquiries.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Enquiry
    </a>
</div>

<div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
    <table class="w-full">
        <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-3 text-left">Reference</th><th class="px-5 py-3 text-left">Client</th><th class="px-5 py-3 text-left">Type</th>
            <th class="px-5 py-3 text-left">Priority</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-right">Est. Value</th><th class="px-5 py-3 text-left">Assigned</th><th class="px-5 py-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-slate-800/50">
            @forelse($enquiries as $e)
            <tr class="table-row">
                <td class="px-5 py-3"><a href="{{ route('enquiries.show', $e) }}" class="text-sm font-medium text-brand-400 hover:text-brand-300">{{ $e->reference }}</a></td>
                <td class="px-5 py-3"><div class="text-sm text-slate-300">{{ $e->client_name }}</div><div class="text-xs text-slate-500">{{ $e->client_company }}</div></td>
                <td class="px-5 py-3 text-sm text-slate-400 capitalize">{{ $e->project_type ?? '—' }}</td>
                <td class="px-5 py-3"><span class="text-xs font-medium {{ $e->priority === 'urgent' ? 'text-red-400' : ($e->priority === 'high' ? 'text-amber-400' : 'text-slate-400') }}">{{ ucfirst($e->priority) }}</span></td>
                <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $e->status_color }}-500/10 text-{{ $e->status_color }}-400">{{ ucfirst($e->status) }}</span></td>
                <td class="px-5 py-3 text-sm text-slate-400 text-right">{{ $e->estimated_value ? '$' . number_format($e->estimated_value) : '—' }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $e->assignedTo?->name ?? '—' }}</td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('enquiries.edit', $e) }}" class="text-xs text-slate-500 hover:text-brand-400">Edit</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-5 py-8 text-center text-sm text-slate-500">No enquiries found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($enquiries->hasPages())
<div class="mt-4">{{ $enquiries->links() }}</div>
@endif
@endsection
