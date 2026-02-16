@extends('layouts.app')
@section('heading', 'Enquiries')
@section('content')
<div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
    <table class="w-full">
        <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-3 text-left">Reference</th><th class="px-5 py-3 text-left">Client</th><th class="px-5 py-3 text-left">Type</th>
            <th class="px-5 py-3 text-left">Priority</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-right">Est. Value</th><th class="px-5 py-3 text-left">Assigned</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-800/50">
            @foreach($enquiries as $e)
            <tr class="table-row">
                <td class="px-5 py-3"><a href="{{ route('enquiries.show', $e) }}" class="text-sm font-medium text-brand-400 hover:text-brand-300">{{ $e->reference }}</a></td>
                <td class="px-5 py-3"><div class="text-sm text-slate-300">{{ $e->client_name }}</div><div class="text-xs text-slate-500">{{ $e->client_company }}</div></td>
                <td class="px-5 py-3 text-sm text-slate-400 capitalize">{{ $e->project_type ?? '—' }}</td>
                <td class="px-5 py-3"><span class="text-xs font-medium {{ $e->priority === 'urgent' ? 'text-red-400' : ($e->priority === 'high' ? 'text-amber-400' : 'text-slate-400') }}">{{ ucfirst($e->priority) }}</span></td>
                <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $e->status_color }}-500/10 text-{{ $e->status_color }}-400">{{ ucfirst($e->status) }}</span></td>
                <td class="px-5 py-3 text-sm text-slate-400 text-right">{{ $e->estimated_value ? '$' . number_format($e->estimated_value) : '—' }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $e->assignedTo?->name ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
