@extends('layouts.app')
@section('heading', $complianceDocument->title)
@section('content')
<div class="max-w-2xl bg-slate-900 rounded-xl border border-slate-800 p-6">
    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-{{ $complianceDocument->status_color }}-500/10 text-{{ $complianceDocument->status_color }}-400 mb-3">{{ ucfirst(str_replace('_', ' ', $complianceDocument->computed_status)) }}</span>
    <h2 class="text-xl font-bold text-white mb-4">{{ $complianceDocument->title }}</h2>
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div><span class="text-slate-500 block text-xs">Type</span><span class="text-slate-300 capitalize">{{ $complianceDocument->type }}</span></div>
        <div><span class="text-slate-500 block text-xs">Document #</span><span class="text-slate-300">{{ $complianceDocument->document_number ?? '—' }}</span></div>
        <div><span class="text-slate-500 block text-xs">Issue Date</span><span class="text-slate-300">{{ $complianceDocument->issue_date?->format('M d, Y') ?? '—' }}</span></div>
        <div><span class="text-slate-500 block text-xs">Expiry Date</span><span class="text-slate-300">{{ $complianceDocument->expiry_date?->format('M d, Y') ?? '—' }}</span></div>
        <div><span class="text-slate-500 block text-xs">Issuing Body</span><span class="text-slate-300">{{ $complianceDocument->issuing_body ?? '—' }}</span></div>
        <div><span class="text-slate-500 block text-xs">Holder</span><span class="text-slate-300">{{ $complianceDocument->holder?->name ?? $complianceDocument->subcontractor?->company_name ?? 'Company' }}</span></div>
        <div><span class="text-slate-500 block text-xs">Reminder</span><span class="text-slate-300">{{ $complianceDocument->reminder_days }} days before expiry</span></div>
        <div><span class="text-slate-500 block text-xs">Critical</span><span class="text-slate-300">{{ $complianceDocument->is_critical ? 'Yes' : 'No' }}</span></div>
    </div>
</div>
@endsection
