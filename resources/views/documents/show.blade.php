@extends('layouts.app')
@section('heading', $documentTemplate->name)
@section('content')
<div class="space-y-6">
    {{-- Template Info --}}
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
        <div class="flex items-start justify-between">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-brand-600/20 text-brand-400">{{ ucfirst($documentTemplate->type) }}</span>
                <span class="ml-2 text-xs text-slate-500">Used {{ $documentTemplate->usage_count }} time(s)</span>
            </div>
            @if(auth()->user()->isManager())
            <div class="flex items-center gap-2">
                <a href="{{ route('documents.edit', $documentTemplate) }}" class="px-3 py-1.5 text-xs font-medium text-slate-300 bg-slate-800 rounded-lg hover:bg-slate-700 transition">Edit</a>
                <form method="POST" action="{{ route('documents.destroy', $documentTemplate) }}" onsubmit="return confirm('Delete this template?')">@csrf @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-400 bg-red-500/10 rounded-lg hover:bg-red-500/20 transition">Delete</button>
                </form>
            </div>
            @endif
        </div>

        @if($documentTemplate->variables && count($documentTemplate->variables))
        <div class="mt-4">
            <div class="text-xs font-medium text-slate-500 mb-1">Custom Variables</div>
            <div class="flex flex-wrap gap-1.5">
                @foreach($documentTemplate->variables as $var)
                <span class="px-2 py-0.5 bg-slate-800 rounded text-xs text-slate-300 font-mono">@{{ {{ $var }} }}</span>
                @endforeach
            </div>
        </div>
        @endif

        <div class="mt-4 p-4 bg-slate-950 rounded-lg border border-slate-800">
            <div class="text-xs font-medium text-slate-500 mb-2">Template Preview</div>
            <div class="text-sm text-slate-300 prose prose-invert prose-sm max-w-none">{!! $documentTemplate->content !!}</div>
        </div>
    </div>

    {{-- Generate Document --}}
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-6" x-data="{ showForm: false }">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-white">Generate Document</h3>
            <button @click="showForm = !showForm" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">Generate from Template</button>
        </div>

        <form x-show="showForm" x-cloak method="POST" action="{{ route('documents.generate', $documentTemplate) }}" class="mt-4 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Document Title *</label>
                    <input type="text" name="title" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500" placeholder="e.g. Bridge Inspection Report — Jan 2026">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">Link to Project</label>
                    <select name="project_id" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                        <option value="">None</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->reference }} — {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($documentTemplate->variables && count($documentTemplate->variables))
            <div class="grid grid-cols-2 gap-4">
                @foreach($documentTemplate->variables as $var)
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1.5">{{ ucwords(str_replace('_', ' ', $var)) }}</label>
                    <input type="text" name="variables[{{ $var }}]" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500" placeholder="{{ $var }}">
                </div>
                @endforeach
            </div>
            @endif

            <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">Generate</button>
        </form>
    </div>

    {{-- Generated History --}}
    @if($documentTemplate->generatedDocuments->count())
    <div class="bg-slate-900 rounded-xl border border-slate-800 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-800">
            <h3 class="text-sm font-semibold text-white">Generated Documents</h3>
        </div>
        <table class="w-full text-sm">
            <thead><tr class="text-xs text-slate-500 uppercase tracking-wider border-b border-slate-800">
                <th class="text-left px-5 py-3">Title</th>
                <th class="text-left px-5 py-3">Project</th>
                <th class="text-left px-5 py-3">Generated By</th>
                <th class="text-left px-5 py-3">Date</th>
                <th class="px-5 py-3"></th>
            </tr></thead>
            <tbody>
            @foreach($documentTemplate->generatedDocuments as $doc)
            <tr class="border-b border-slate-800/50 table-row">
                <td class="px-5 py-3 text-slate-200 font-medium">{{ $doc->title }}</td>
                <td class="px-5 py-3 text-slate-400">{{ $doc->project?->name ?? '—' }}</td>
                <td class="px-5 py-3 text-slate-400">{{ $doc->generatedBy?->name ?? '—' }}</td>
                <td class="px-5 py-3 text-slate-500">{{ $doc->created_at->format('M j, Y') }}</td>
                <td class="px-5 py-3 text-right"><a href="{{ route('documents.preview', $doc) }}" class="text-xs text-brand-400 hover:text-brand-300">Preview</a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
