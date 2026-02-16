@extends('layouts.app')
@section('heading', $enquiry->reference . ' — ' . $enquiry->client_name)
@section('content')
<div class="max-w-4xl">
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-{{ $enquiry->status_color }}-500/10 text-{{ $enquiry->status_color }}-400 mb-2">{{ ucfirst($enquiry->status) }}</span>
                <h2 class="text-xl font-bold text-white">{{ $enquiry->client_name }}</h2>
                <p class="text-sm text-slate-400">{{ $enquiry->client_company }} · {{ ucfirst($enquiry->project_type ?? 'General') }}</p>
            </div>
            @if($enquiry->estimated_value)
            <div class="text-right"><div class="text-xs text-slate-500">Est. Value</div><div class="text-xl font-bold text-white">${{ number_format($enquiry->estimated_value) }}</div></div>
            @endif
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-4">
            <div><span class="text-slate-500 block text-xs">Source</span><span class="text-slate-300 capitalize">{{ $enquiry->source }}</span></div>
            <div><span class="text-slate-500 block text-xs">Priority</span><span class="text-slate-300 capitalize {{ $enquiry->priority === 'urgent' ? 'text-red-400' : '' }}">{{ $enquiry->priority }}</span></div>
            <div><span class="text-slate-500 block text-xs">Contact</span><span class="text-slate-300">{{ $enquiry->client_email ?? '—' }}</span></div>
            <div><span class="text-slate-500 block text-xs">Assigned</span><span class="text-slate-300">{{ $enquiry->assignedTo?->name ?? 'Unassigned' }}</span></div>
        </div>
        <div class="border-t border-slate-800 pt-4"><h4 class="text-xs font-medium text-slate-500 uppercase mb-2">Description</h4><p class="text-sm text-slate-300 whitespace-pre-line">{{ $enquiry->description }}</p></div>
    </div>

    @if($enquiry->quotes->count())
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800"><h3 class="font-semibold text-white">Quotes</h3></div>
        <div class="divide-y divide-slate-800/50">
            @foreach($enquiry->quotes as $q)
            <a href="{{ route('quotes.show', $q) }}" class="flex items-center justify-between px-5 py-3 table-row">
                <div><div class="text-sm font-medium text-brand-400">{{ $q->reference }}</div><div class="text-xs text-slate-500">Prepared by {{ $q->preparedBy?->name ?? '—' }}</div></div>
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $q->status_color }}-500/10 text-{{ $q->status_color }}-400">{{ ucfirst($q->status) }}</span>
                    <span class="text-sm font-medium text-white">${{ number_format($q->total) }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
