@extends('layouts.app')
@section('heading', 'Audit Trail')
@section('content')
<div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
    <table class="w-full">
        <thead><tr class="border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-3 text-left">Timestamp</th><th class="px-5 py-3 text-left">User</th><th class="px-5 py-3 text-left">Action</th>
            <th class="px-5 py-3 text-left">Entity</th><th class="px-5 py-3 text-left">Details</th><th class="px-5 py-3 text-left">IP</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-800/50">
            @foreach($logs as $log)
            <tr class="table-row">
                <td class="px-5 py-3 text-sm text-slate-500">{{ $log->created_at->format('M d, H:i') }}</td>
                <td class="px-5 py-3 text-sm text-slate-300">{{ $log->user?->name ?? 'System' }}</td>
                <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $log->action === 'created' ? 'bg-green-500/10 text-green-400' : ($log->action === 'deleted' ? 'bg-red-500/10 text-red-400' : 'bg-blue-500/10 text-blue-400') }}">{{ ucfirst($log->action) }}</span></td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $log->model_type }} #{{ $log->model_id }}</td>
                <td class="px-5 py-3 text-sm text-slate-400">{{ $log->notes ?? '—' }}</td>
                <td class="px-5 py-3 text-xs text-slate-600 font-mono">{{ $log->ip_address }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
