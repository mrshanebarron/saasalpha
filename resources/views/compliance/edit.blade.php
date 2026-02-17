@extends('layouts.app')
@section('heading', 'Edit — ' . $complianceDocument->title)
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('compliance.update', $complianceDocument) }}" class="bg-slate-900 rounded-xl border border-slate-800 p-6 space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Document Name *</label>
                <input type="text" name="title" value="{{ old('title', $complianceDocument->title) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Type *</label>
                <select name="type" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    @foreach(['license','insurance','safety','permit','certification','other'] as $t)
                        <option value="{{ $t }}" {{ old('type', $complianceDocument->type) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Issuing Authority</label>
                <input type="text" name="issuing_body" value="{{ old('issuing_body', $complianceDocument->issuing_body) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Document Number</label>
                <input type="text" name="document_number" value="{{ old('document_number', $complianceDocument->document_number) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Issue Date</label>
                <input type="date" name="issue_date" value="{{ old('issue_date', $complianceDocument->issue_date?->format('Y-m-d')) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Expiry Date *</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date', $complianceDocument->expiry_date?->format('Y-m-d')) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Reminder (days)</label>
                <input type="number" name="reminder_days" value="{{ old('reminder_days', $complianceDocument->reminder_days) }}" min="1" max="365" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="is_critical" value="1" {{ old('is_critical', $complianceDocument->is_critical) ? 'checked' : '' }} class="rounded bg-slate-800 border-slate-700 text-brand-600">
                Critical Document
            </label>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Notes</label>
            <textarea name="notes" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">{{ old('notes', $complianceDocument->notes) }}</textarea>
        </div>

        <div class="flex items-center justify-between pt-2">
            <div class="flex items-center gap-3">
                <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">Update Document</button>
                <a href="{{ route('compliance.show', $complianceDocument) }}" class="px-4 py-2 text-slate-400 text-sm hover:text-slate-200">Cancel</a>
            </div>
            <form method="POST" action="{{ route('compliance.destroy', $complianceDocument) }}" onsubmit="return confirm('Delete this document?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 text-red-400 text-sm hover:text-red-300">Delete</button>
            </form>
        </div>
    </form>
</div>
@endsection
