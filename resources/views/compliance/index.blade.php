@extends('layouts.app')
@section('heading', 'Compliance Documents')
@section('content')

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card rounded-xl p-5"><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total</span><div class="text-2xl font-bold text-white mt-2">{{ $stats['total'] }}</div></div>
    <div class="stat-card rounded-xl p-5"><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Valid</span><div class="text-2xl font-bold text-green-400 mt-2">{{ $stats['valid'] }}</div></div>
    <div class="stat-card rounded-xl p-5"><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Expiring Soon</span><div class="text-2xl font-bold text-amber-400 mt-2">{{ $stats['expiring'] }}</div></div>
    <div class="stat-card rounded-xl p-5"><span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Expired</span><div class="text-2xl font-bold text-red-400 mt-2">{{ $stats['expired'] }}</div></div>
</div>

<div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
    <table class="w-full">
        <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-3 text-left">Document</th><th class="px-5 py-3 text-left">Type</th><th class="px-5 py-3 text-left">Holder</th>
            <th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-left">Expiry</th><th class="px-5 py-3 text-left">Days Left</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-800/50">
            @foreach($documents as $doc)
            <tr class="table-row">
                <td class="px-5 py-3"><div class="text-sm font-medium text-slate-300">{{ $doc->title }}</div><div class="text-xs text-slate-500">{{ $doc->document_number ?? '—' }}</div></td>
                <td class="px-5 py-3 text-sm text-slate-400 capitalize">{{ $doc->type }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $doc->holder?->name ?? $doc->subcontractor?->company_name ?? 'Company' }}</td>
                <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $doc->status_color }}-500/10 text-{{ $doc->status_color }}-400">{{ ucfirst(str_replace('_', ' ', $doc->computed_status)) }}</span></td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $doc->expiry_date?->format('M d, Y') ?? '—' }}</td>
                <td class="px-5 py-3 text-sm font-medium {{ $doc->is_expired ? 'text-red-400' : ($doc->is_expiring_soon ? 'text-amber-400' : 'text-slate-400') }}">{{ $doc->days_until_expiry !== null ? $doc->days_until_expiry . 'd' : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
