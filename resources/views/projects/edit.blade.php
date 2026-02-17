@extends('layouts.app')
@section('heading', 'Edit ' . $project->reference)
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('projects.update', $project) }}" class="bg-slate-900 rounded-xl border border-slate-800 p-6 space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Project Name *</label>
            <input type="text" name="name" value="{{ old('name', $project->name) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Client Name *</label>
                <input type="text" name="client_name" value="{{ old('client_name', $project->client_name) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Project Type</label>
                <input type="text" name="project_type" value="{{ old('project_type', $project->project_type) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Description</label>
            <textarea name="description" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">{{ old('description', $project->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Status *</label>
                <select name="status" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                    @foreach(['active','on_hold','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status', $project->status) === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Progress (%)</label>
                <input type="number" name="progress" value="{{ old('progress', $project->progress) }}" min="0" max="100" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Budget</label>
                <input type="number" name="budget" value="{{ old('budget', $project->budget) }}" step="0.01" min="0" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Start Date *</label>
                <input type="date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Target Date</label>
                <input type="date" name="target_date" value="{{ old('target_date', $project->target_date?->format('Y-m-d')) }}" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Project Manager *</label>
            <select name="project_manager_id" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-brand-500">
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('project_manager_id', $project->project_manager_id) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition">Update Project</button>
            <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 text-slate-400 text-sm hover:text-slate-200">Cancel</a>
        </div>
    </form>
</div>
@endsection
