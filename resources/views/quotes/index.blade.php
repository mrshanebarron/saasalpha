@extends('layouts.app')
@section('heading', 'Quotes')
@section('content')
<div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
    <table class="w-full">
        <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-3 text-left">Reference</th><th class="px-5 py-3 text-left">Client</th>
            <th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-right">Total</th><th class="px-5 py-3 text-left">Valid Until</th><th class="px-5 py-3 text-left">Prepared By</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-800/50">
            @foreach($quotes as $q)
            <tr class="table-row">
                <td class="px-5 py-3"><a href="{{ route('quotes.show', $q) }}" class="text-sm font-medium text-brand-400 hover:text-brand-300">{{ $q->reference }}</a><div class="text-xs text-slate-500 mt-0.5">{{ $q->enquiry?->reference ?? '—' }}</div></td>
                <td class="px-5 py-3"><div class="text-sm text-slate-300">{{ $q->client_name }}</div><div class="text-xs text-slate-500">{{ $q->client_company }}</div></td>
                <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $q->status_color }}-500/10 text-{{ $q->status_color }}-400">{{ ucfirst($q->status) }}</span></td>
                <td class="px-5 py-3 text-sm font-medium text-white text-right">${{ number_format($q->total) }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $q->valid_until?->format('M d, Y') ?? '—' }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $q->preparedBy?->name ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
