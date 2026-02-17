@extends('layouts.app')
@section('heading', 'New Document Template')
@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('documents.store') }}" class="bg-slate-900 rounded-xl border border-slate-800 p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Template Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500" placeholder="e.g. Site Inspection Report">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Type *</label>
                <select name="type" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    <option value="proposal">Proposal</option>
                    <option value="report">Report</option>
                    <option value="certificate">Certificate</option>
                    <option value="letter">Letter</option>
                    <option value="invoice">Invoice</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Variables <span class="text-slate-600">(comma-separated, used as @{{variable_name}} in content)</span></label>
            <input type="text" name="variables" value="{{ old('variables') }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500" placeholder="e.g. site_address, inspection_date, findings">
            <p class="text-xs text-slate-600 mt-1">Built-in: date, tenant_name, user_name, project_name, client_name, quote_reference, scope_of_work</p>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Template Content * <span class="text-slate-600">(HTML supported, use @{{variable_name}} for placeholders)</span></label>
            <textarea name="content" rows="16" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 font-mono focus:outline-none focus:border-brand-500">{{ old('content') }}</textarea>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">Create Template</button>
            <a href="{{ route('documents.index') }}" class="px-4 py-2 text-slate-400 text-sm hover:text-slate-200">Cancel</a>
        </div>
    </form>
</div>
@endsection
