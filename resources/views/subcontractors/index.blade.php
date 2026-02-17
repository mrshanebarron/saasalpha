@extends('layouts.app')
@section('heading', 'Subcontractors')
@section('content')

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row gap-3 mb-6">
    <form method="GET" class="flex flex-1 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company name..." class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-500">
        <button type="submit" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg text-sm hover:bg-slate-700">Search</button>
    </form>
    <a href="{{ route('subcontractors.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Subcontractor
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    @forelse($subcontractors as $sub)
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 hover:border-slate-700 transition">
        <div class="flex items-start justify-between mb-1">
            <a href="{{ route('subcontractors.show', $sub) }}" class="text-sm font-medium text-white hover:text-brand-400">{{ $sub->company_name }}</a>
            <span class="text-xs {{ $sub->status === 'active' ? 'text-green-400' : 'text-slate-500' }}">{{ ucfirst($sub->status) }}</span>
        </div>
        <div class="text-xs text-slate-500 mb-3">{{ $sub->contact_name ?? '—' }}</div>
        <div class="flex items-center justify-between text-xs">
            <span class="text-slate-400">{{ $sub->specialty ?? '—' }}</span>
            <span class="text-brand-400">{{ $sub->compliance_documents_count }} docs</span>
        </div>
        @if($sub->default_rate)
        <div class="text-xs text-slate-500 mt-2">${{ number_format($sub->default_rate) }}/hr</div>
        @endif
        <div class="mt-3 pt-3 border-t border-slate-800 flex gap-3">
            <a href="{{ route('subcontractors.edit', $sub) }}" class="text-xs text-slate-500 hover:text-brand-400">Edit</a>
            <form method="POST" action="{{ route('subcontractors.destroy', $sub) }}" onsubmit="return confirm('Delete this subcontractor?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-slate-500 hover:text-red-400">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-4 text-center py-12 text-sm text-slate-500">No subcontractors found.</div>
    @endforelse
</div>
@if($subcontractors->hasPages())
<div class="mt-4">{{ $subcontractors->links() }}</div>
@endif
@endsection
