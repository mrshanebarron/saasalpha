@extends('layouts.app')
@section('heading', $complianceDocument->title)
@section('content')
<div class="max-w-2xl">
    {{-- Actions --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('compliance.edit', $complianceDocument) }}" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg text-sm hover:bg-slate-700 transition">Edit</a>
        <form method="POST" action="{{ route('compliance.destroy', $complianceDocument) }}" onsubmit="return confirm('Delete this document?')" class="ml-auto">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 text-red-400 text-sm hover:text-red-300">Delete</button>
        </form>
    </div>

    <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-{{ $complianceDocument->status_color }}-500/10 text-{{ $complianceDocument->status_color }}-400 mb-3">{{ ucfirst(str_replace('_', ' ', $complianceDocument->computed_status)) }}</span>
        @if($complianceDocument->is_critical)
        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-500/10 text-red-400 mb-3 ml-1">Critical</span>
        @endif
        <h2 class="text-xl font-bold text-white mb-4">{{ $complianceDocument->title }}</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-slate-500 block text-xs">Type</span><span class="text-slate-300 capitalize">{{ str_replace('_', ' ', $complianceDocument->type) }}</span></div>
            <div><span class="text-slate-500 block text-xs">Document #</span><span class="text-slate-300">{{ $complianceDocument->document_number ?? '—' }}</span></div>
            <div><span class="text-slate-500 block text-xs">Issue Date</span><span class="text-slate-300">{{ $complianceDocument->issue_date?->format('M d, Y') ?? '—' }}</span></div>
            <div><span class="text-slate-500 block text-xs">Expiry Date</span><span class="text-slate-300">{{ $complianceDocument->expiry_date?->format('M d, Y') ?? '—' }}</span></div>
            <div><span class="text-slate-500 block text-xs">Issuing Body</span><span class="text-slate-300">{{ $complianceDocument->issuing_body ?? '—' }}</span></div>
            <div><span class="text-slate-500 block text-xs">Holder</span><span class="text-slate-300">{{ $complianceDocument->holder?->name ?? $complianceDocument->subcontractor?->company_name ?? 'Company' }}</span></div>
            <div><span class="text-slate-500 block text-xs">Reminder</span><span class="text-slate-300">{{ $complianceDocument->reminder_days }} days before expiry</span></div>
            <div><span class="text-slate-500 block text-xs">Days Until Expiry</span><span class="text-sm font-medium {{ $complianceDocument->is_expired ? 'text-red-400' : ($complianceDocument->is_expiring_soon ? 'text-amber-400' : 'text-green-400') }}">{{ $complianceDocument->days_until_expiry !== null ? $complianceDocument->days_until_expiry . ' days' : '—' }}</span></div>
        </div>
        @if($complianceDocument->notes)
        <div class="border-t border-slate-800 pt-4 mt-4"><h4 class="text-xs font-medium text-slate-500 uppercase mb-2">Notes</h4><p class="text-sm text-slate-300 whitespace-pre-line">{{ $complianceDocument->notes }}</p></div>
        @endif
    </div>
</div>
@endsection
