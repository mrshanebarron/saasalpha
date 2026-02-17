@extends('layouts.app')
@section('heading', 'Log Time')
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('time-tracking.store') }}" class="bg-slate-900 rounded-xl border border-slate-800 p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Project *</label>
                <select name="project_id" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    <option value="">Select project...</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->reference }} — {{ $p->name }}</option>
                    @endforeach
                </select>
                @error('project_id')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Date *</label>
                <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Hours *</label>
                <input type="number" name="hours" value="{{ old('hours') }}" step="0.25" min="0.25" max="24" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Category *</label>
                <select name="category" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    @foreach(['engineering','review','admin','travel','meeting','design','inspection'] as $c)
                        <option value="{{ $c }}" {{ old('category') === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Rate ($/hr)</label>
                <input type="number" name="rate" value="{{ old('rate', 150) }}" step="0.01" min="0" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Description *</label>
            <textarea name="description" rows="3" required maxlength="500" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="billable" value="1" {{ old('billable', true) ? 'checked' : '' }} class="rounded bg-slate-800 border-slate-700 text-brand-600">
                Billable
            </label>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">Log Time</button>
            <a href="{{ route('time-tracking.index') }}" class="px-4 py-2 text-slate-400 text-sm hover:text-slate-200">Cancel</a>
        </div>
    </form>
</div>
@endsection
