@extends('layouts.app')
@section('heading', 'Edit Template: ' . $documentTemplate->name)
@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('documents.update', $documentTemplate) }}" class="bg-slate-900 rounded-xl border border-slate-800 p-6 space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Template Name *</label>
                <input type="text" name="name" value="{{ old('name', $documentTemplate->name) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Type *</label>
                <select name="type" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    @foreach(['proposal', 'report', 'certificate', 'letter', 'invoice'] as $type)
                    <option value="{{ $type }}" {{ old('type', $documentTemplate->type) === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Variables <span class="text-slate-600">(comma-separated)</span></label>
            <input type="text" name="variables" value="{{ old('variables', $documentTemplate->variables ? implode(', ', $documentTemplate->variables) : '') }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            <p class="text-xs text-slate-600 mt-1">Built-in: date, tenant_name, user_name, project_name, client_name, quote_reference, scope_of_work</p>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Template Content *</label>
            <textarea name="content" rows="16" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 font-mono focus:outline-none focus:border-brand-500">{{ old('content', $documentTemplate->content) }}</textarea>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">Update Template</button>
            <a href="{{ route('documents.show', $documentTemplate) }}" class="px-4 py-2 text-slate-400 text-sm hover:text-slate-200">Cancel</a>
        </div>
    </form>
</div>
@endsection
