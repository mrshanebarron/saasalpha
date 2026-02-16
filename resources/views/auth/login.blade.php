<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — SaaS Alpha</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-950 flex items-center justify-center antialiased">
    <div class="w-full max-w-md px-6">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white">SaaS Alpha</h1>
            <p class="text-slate-500 mt-1">Engineering & Construction Platform</p>
        </div>

        <div class="bg-slate-900 rounded-xl border border-slate-800 p-6">
            <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Demo — Select User</h2>
            <div class="space-y-2">
                @foreach($users as $user)
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-lg bg-slate-800/50 hover:bg-slate-800 border border-slate-700/50 hover:border-slate-600 transition text-left group">
                        <div class="w-10 h-10 rounded-full bg-blue-600/20 text-blue-400 flex items-center justify-center text-sm font-bold flex-shrink-0">
                            {{ collect(explode(' ', $user->name))->map(fn($n) => strtoupper($n[0]))->implode('') }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-200 group-hover:text-white">{{ $user->name }}</div>
                            <div class="text-xs text-slate-500">{{ $user->job_title }}</div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $user->role === 'admin' ? 'bg-red-500/10 text-red-400' : ($user->role === 'manager' ? 'bg-amber-500/10 text-amber-400' : 'bg-slate-500/10 text-slate-400') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </button>
                </form>
                @endforeach
            </div>
        </div>

        <p class="text-center text-xs text-slate-600 mt-6">Demo instance — Northbridge Engineering Consultants</p>
    </div>
</body>
</html>
