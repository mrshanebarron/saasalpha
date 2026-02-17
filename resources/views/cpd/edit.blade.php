@extends('layouts.app')
@section('heading', 'Edit CPD Record')
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('cpd.update', $cpdRecord) }}" class="bg-slate-900 rounded-xl border border-slate-800 p-6 space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Title *</label>
            <input type="text" name="title" value="{{ old('title', $cpdRecord->title) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Category *</label>
                <select name="category" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    @foreach(['course','seminar','conference','self_study','mentoring','publication'] as $c)
                        <option value="{{ $c }}" {{ old('category', $cpdRecord->category) === $c ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $c)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Provider</label>
                <input type="text" name="provider" value="{{ old('provider', $cpdRecord->provider) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Hours *</label>
                <input type="number" name="hours" value="{{ old('hours', $cpdRecord->hours) }}" step="0.5" min="0.5" max="100" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Completed Date *</label>
                <input type="date" name="completed_date" value="{{ old('completed_date', $cpdRecord->completed_date->format('Y-m-d')) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Expiry Date</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date', $cpdRecord->expiry_date?->format('Y-m-d')) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Certificate Number</label>
            <input type="text" name="certificate_number" value="{{ old('certificate_number', $cpdRecord->certificate_number) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Description</label>
            <textarea name="description" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">{{ old('description', $cpdRecord->description) }}</textarea>
        </div>

        <div class="flex items-center justify-between pt-2">
            <div class="flex items-center gap-3">
                <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">Update Record</button>
                <a href="{{ route('cpd.index') }}" class="px-4 py-2 text-slate-400 text-sm hover:text-slate-200">Cancel</a>
            </div>
            <form method="POST" action="{{ route('cpd.destroy', $cpdRecord) }}" onsubmit="return confirm('Delete this CPD record?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 text-red-400 text-sm hover:text-red-300">Delete</button>
            </form>
        </div>
    </form>
</div>
@endsection
