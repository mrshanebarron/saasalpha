@extends('layouts.app')
@section('heading', 'Compliance Documents')
@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card rounded-xl p-5"><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total</span><div class="text-2xl font-bold text-white mt-2">{{ $stats['total'] }}</div></div>
    <div class="stat-card rounded-xl p-5"><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Valid</span><div class="text-2xl font-bold text-green-400 mt-2">{{ $stats['valid'] }}</div></div>
    <div class="stat-card rounded-xl p-5"><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Expiring Soon</span><div class="text-2xl font-bold text-amber-400 mt-2">{{ $stats['expiring'] }}</div></div>
    <div class="stat-card rounded-xl p-5"><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Expired</span><div class="text-2xl font-bold text-red-400 mt-2">{{ $stats['expired'] }}</div></div>
</div>

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row gap-3 mb-6">
    <form method="GET" class="flex flex-1 gap-3">
        <select name="status" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none focus:border-brand-500">
            <option value="">All Statuses</option>
            <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>Valid</option>
            <option value="expiring_soon" {{ request('status') === 'expiring_soon' ? 'selected' : '' }}>Expiring Soon</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
        </select>
    </form>
    <a href="{{ route('compliance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Document
    </a>
</div>

<div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
    <table class="w-full">
        <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-3 text-left">Document</th><th class="px-5 py-3 text-left">Type</th><th class="px-5 py-3 text-left">Holder</th>
            <th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-left">Expiry</th><th class="px-5 py-3 text-left">Days Left</th><th class="px-5 py-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-slate-800/50">
            @forelse($documents as $doc)
            <tr class="table-row">
                <td class="px-5 py-3"><a href="{{ route('compliance.show', $doc) }}" class="text-sm font-medium text-brand-400 hover:text-brand-300">{{ $doc->title }}</a><div class="text-xs text-slate-500">{{ $doc->document_number ?? '—' }}</div></td>
                <td class="px-5 py-3 text-sm text-slate-400 capitalize">{{ str_replace('_', ' ', $doc->type) }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $doc->holder?->name ?? $doc->subcontractor?->company_name ?? 'Company' }}</td>
                <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $doc->status_color }}-500/10 text-{{ $doc->status_color }}-400">{{ ucfirst(str_replace('_', ' ', $doc->computed_status)) }}</span></td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $doc->expiry_date?->format('M d, Y') ?? '—' }}</td>
                <td class="px-5 py-3 text-sm font-medium {{ $doc->is_expired ? 'text-red-400' : ($doc->is_expiring_soon ? 'text-amber-400' : 'text-slate-400') }}">{{ $doc->days_until_expiry !== null ? $doc->days_until_expiry . 'd' : '—' }}</td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('compliance.edit', $doc) }}" class="text-xs text-slate-500 hover:text-brand-400">Edit</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-8 text-center text-sm text-slate-500">No compliance documents found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($documents->hasPages())
<div class="mt-4">{{ $documents->links() }}</div>
@endif
@endsection
