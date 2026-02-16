@extends('layouts.app')
@section('heading', 'Subcontractors')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach($subcontractors as $sub)
    <a href="{{ route('subcontractors.show', $sub) }}" class="bg-slate-900 rounded-xl border border-slate-800 p-5 hover:border-slate-700 transition">
        <div class="text-sm font-medium text-white mb-1">{{ $sub->company_name }}</div>
        <div class="text-xs text-slate-500 mb-3">{{ $sub->contact_name ?? '—' }}</div>
        <div class="flex items-center justify-between text-xs">
            <span class="text-slate-400">{{ $sub->specialty ?? '—' }}</span>
            <span class="text-brand-400">{{ $sub->compliance_documents_count }} docs</span>
        </div>
        @if($sub->default_rate)
        <div class="text-xs text-slate-500 mt-2">${{ number_format($sub->default_rate) }}/hr</div>
        @endif
    </a>
    @endforeach
</div>
@endsection
