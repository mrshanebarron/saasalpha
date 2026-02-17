@extends('layouts.app')
@section('heading', 'Document: ' . $generatedDocument->title)
@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('documents.index') }}" class="text-sm text-slate-400 hover:text-slate-200">&larr; All Documents</a>
            <span class="text-slate-600">|</span>
            <span class="text-sm text-slate-500">Template: {{ $generatedDocument->template?->name ?? 'Unknown' }}</span>
            @if($generatedDocument->project)
            <span class="text-slate-600">|</span>
            <span class="text-sm text-slate-500">Project: {{ $generatedDocument->project->name }}</span>
            @endif
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print / Save PDF
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <iframe srcdoc="{{ e($content) }}" class="w-full border-0" style="min-height: 800px;" onload="this.style.height = this.contentDocument.body.scrollHeight + 40 + 'px'"></iframe>
    </div>
</div>
@endsection
