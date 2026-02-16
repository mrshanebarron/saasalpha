@extends('layouts.app')
@section('heading', $subcontractor->company_name)
@section('content')
<div class="max-w-3xl">
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-6 mb-6">
        <h2 class="text-xl font-bold text-white mb-4">{{ $subcontractor->company_name }}</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-slate-500 block text-xs">Contact</span><span class="text-slate-300">{{ $subcontractor->contact_name ?? '—' }}</span></div>
            <div><span class="text-slate-500 block text-xs">Email</span><span class="text-slate-300">{{ $subcontractor->email ?? '—' }}</span></div>
            <div><span class="text-slate-500 block text-xs">Phone</span><span class="text-slate-300">{{ $subcontractor->phone ?? '—' }}</span></div>
            <div><span class="text-slate-500 block text-xs">Specialty</span><span class="text-slate-300">{{ $subcontractor->specialty ?? '—' }}</span></div>
            <div><span class="text-slate-500 block text-xs">Rate</span><span class="text-slate-300">{{ $subcontractor->default_rate ? '$' . number_format($subcontractor->default_rate) . '/hr' : '—' }}</span></div>
            <div><span class="text-slate-500 block text-xs">Status</span><span class="text-slate-300 capitalize">{{ $subcontractor->status }}</span></div>
        </div>
    </div>
    @if($subcontractor->complianceDocuments->count())
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800"><h3 class="font-semibold text-white">Compliance Documents</h3></div>
        <div class="divide-y divide-slate-800/50">
            @foreach($subcontractor->complianceDocuments as $doc)
            <div class="px-5 py-3 flex items-center justify-between">
                <div><div class="text-sm text-slate-300">{{ $doc->title }}</div><div class="text-xs text-slate-500">{{ $doc->issuing_body ?? '—' }}</div></div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $doc->status_color }}-500/10 text-{{ $doc->status_color }}-400">{{ ucfirst(str_replace('_', ' ', $doc->computed_status)) }}</span>
                    <span class="text-xs text-slate-500">{{ $doc->expiry_date?->format('M d, Y') }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
