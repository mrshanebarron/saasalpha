@extends('layouts.app')
@section('heading', 'Edit Time Entry')
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('time-tracking.update', $timeEntry) }}" class="bg-slate-900 rounded-xl border border-slate-800 p-6 space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Project *</label>
                <select name="project_id" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id', $timeEntry->project_id) == $p->id ? 'selected' : '' }}>{{ $p->reference }} — {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Date *</label>
                <input type="date" name="date" value="{{ old('date', $timeEntry->date->format('Y-m-d')) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Hours *</label>
                <input type="number" name="hours" value="{{ old('hours', $timeEntry->hours) }}" step="0.25" min="0.25" max="24" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Category *</label>
                <select name="category" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    @foreach(['engineering','review','admin','travel','meeting','design','inspection'] as $c)
                        <option value="{{ $c }}" {{ old('category', $timeEntry->category) === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Rate ($/hr)</label>
                <input type="number" name="rate" value="{{ old('rate', $timeEntry->rate) }}" step="0.01" min="0" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Description *</label>
            <textarea name="description" rows="3" required maxlength="500" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">{{ old('description', $timeEntry->description) }}</textarea>
        </div>

        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="billable" value="1" {{ old('billable', $timeEntry->billable) ? 'checked' : '' }} class="rounded bg-slate-800 border-slate-700 text-brand-600">
                Billable
            </label>
        </div>

        <div class="flex items-center justify-between pt-2">
            <div class="flex items-center gap-3">
                <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">Update</button>
                <a href="{{ route('time-tracking.index') }}" class="px-4 py-2 text-slate-400 text-sm hover:text-slate-200">Cancel</a>
            </div>
            <form method="POST" action="{{ route('time-tracking.destroy', $timeEntry) }}" onsubmit="return confirm('Delete this entry?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 text-red-400 text-sm hover:text-red-300">Delete</button>
            </form>
        </div>
    </form>
</div>
@endsection
