@extends('layouts.app')
@section('heading', 'Quotes')
@section('content')

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row gap-3 mb-6">
    <form method="GET" class="flex flex-1 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search client or reference..." class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-500">
        <select name="status" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none focus:border-brand-500">
            <option value="">All Statuses</option>
            @foreach(['draft','sent','accepted','rejected','expired'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg text-sm hover:bg-slate-700">Filter</button>
    </form>
    <a href="{{ route('quotes.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Quote
    </a>
</div>

<div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
    <table class="w-full">
        <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-3 text-left">Reference</th><th class="px-5 py-3 text-left">Client</th>
            <th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-right">Total</th><th class="px-5 py-3 text-left">Valid Until</th><th class="px-5 py-3 text-left">Prepared By</th><th class="px-5 py-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-slate-800/50">
            @forelse($quotes as $q)
            <tr class="table-row">
                <td class="px-5 py-3"><a href="{{ route('quotes.show', $q) }}" class="text-sm font-medium text-brand-400 hover:text-brand-300">{{ $q->reference }}</a><div class="text-xs text-slate-500 mt-0.5">{{ $q->enquiry?->reference ?? '—' }}</div></td>
                <td class="px-5 py-3"><div class="text-sm text-slate-300">{{ $q->client_name }}</div><div class="text-xs text-slate-500">{{ $q->client_company }}</div></td>
                <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $q->status_color }}-500/10 text-{{ $q->status_color }}-400">{{ ucfirst($q->status) }}</span></td>
                <td class="px-5 py-3 text-sm font-medium text-white text-right">${{ number_format($q->total) }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $q->valid_until?->format('M d, Y') ?? '—' }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $q->preparedBy?->name ?? '—' }}</td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('quotes.edit', $q) }}" class="text-xs text-slate-500 hover:text-brand-400">Edit</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-8 text-center text-sm text-slate-500">No quotes found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($quotes->hasPages())
<div class="mt-4">{{ $quotes->links() }}</div>
@endif
@endsection
