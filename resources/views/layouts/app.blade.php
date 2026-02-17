<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SaaS Alpha') — Northbridge Engineering</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#eff6ff', 100:'#dbeafe', 200:'#bfdbfe', 300:'#93c5fd', 400:'#60a5fa', 500:'#3b82f6', 600:'#2563eb', 700:'#1d4ed8', 800:'#1e40af', 900:'#1e3a8a' },
                        slate: { 750:'#293548', 850:'#172033' },
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link { display:flex; align-items:center; gap:0.75rem; padding:0.5rem 0.75rem; border-radius:0.5rem; font-size:0.875rem; font-weight:500; color:#94a3b8; transition:all 150ms; }
        .sidebar-link:hover { color:#e2e8f0; background:rgba(255,255,255,0.05); }
        .sidebar-link.active { color:#60a5fa; background:rgba(59,130,246,0.1); }
        .stat-card { background:linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border:1px solid rgba(148,163,184,0.1); }
        .table-row:hover { background:rgba(255,255,255,0.02); }
        ::-webkit-scrollbar { width:6px; } ::-webkit-scrollbar-track { background:#0f172a; } ::-webkit-scrollbar-thumb { background:#334155; border-radius:3px; }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-200 antialiased" x-data="{ sidebarOpen: true }">
    <div class="flex h-full">
        {{-- Sidebar --}}
        <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col flex-shrink-0" x-show="sidebarOpen" x-cloak>
            {{-- Logo --}}
            <div class="p-5 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-brand-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white tracking-tight">SaaS Alpha</div>
                        <div class="text-xs text-slate-500">Engineering Platform</div>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                <div class="pt-3 pb-1 px-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Workflow</div>
                <a href="{{ route('enquiries.index') }}" class="sidebar-link {{ request()->routeIs('enquiries.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Enquiries
                </a>
                <a href="{{ route('quotes.index') }}" class="sidebar-link {{ request()->routeIs('quotes.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Quotes
                </a>
                <a href="{{ route('projects.index') }}" class="sidebar-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Projects
                </a>

                <div class="pt-3 pb-1 px-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Operations</div>
                <a href="{{ route('time-tracking.index') }}" class="sidebar-link {{ request()->routeIs('time-tracking.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Time Tracking
                </a>
                <a href="{{ route('compliance.index') }}" class="sidebar-link {{ request()->routeIs('compliance.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Compliance
                </a>
                <a href="{{ route('cpd.index') }}" class="sidebar-link {{ request()->routeIs('cpd.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    CPD Tracking
                </a>
                <a href="{{ route('subcontractors.index') }}" class="sidebar-link {{ request()->routeIs('subcontractors.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Subcontractors
                </a>

                <div class="pt-3 pb-1 px-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">System</div>
                <a href="{{ route('audit.index') }}" class="sidebar-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Audit Trail
                </a>
            </nav>

            {{-- User --}}
            <div class="p-3 border-t border-slate-800">
                <div class="flex items-center gap-3 px-2">
                    <div class="w-8 h-8 rounded-full bg-brand-600/20 text-brand-400 flex items-center justify-center text-xs font-bold">{{ auth()->user()->initials }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-slate-300 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-500 truncate">{{ auth()->user()->job_title }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button type="submit" class="text-slate-500 hover:text-slate-300" title="Sign out">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main --}}
        <main class="flex-1 overflow-y-auto">
            {{-- Top Bar --}}
            <header class="sticky top-0 z-10 bg-slate-950/80 backdrop-blur-xl border-b border-slate-800/50">
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h1 class="text-lg font-semibold text-white">@yield('heading', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-slate-400">
                        @php $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="relative text-slate-400 hover:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                                @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                @endif
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak x-transition class="absolute right-0 mt-2 w-80 bg-slate-900 border border-slate-700 rounded-xl shadow-xl overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-slate-800 flex items-center justify-between">
                                    <span class="text-sm font-medium text-white">Notifications</span>
                                    @if($unreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.mark-all-read') }}">@csrf <button type="submit" class="text-xs text-brand-400 hover:text-brand-300">Mark all read</button></form>
                                    @endif
                                </div>
                                @php $headerNotifs = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->latest()->take(5)->get(); @endphp
                                @forelse($headerNotifs as $n)
                                <div class="px-4 py-3 border-b border-slate-800/50 hover:bg-slate-800/50">
                                    <div class="text-sm text-slate-300">{{ $n->title }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $n->message }}</div>
                                    <div class="text-xs text-slate-600 mt-1">{{ $n->created_at->diffForHumans() }}</div>
                                </div>
                                @empty
                                <div class="px-4 py-6 text-center text-sm text-slate-500">No new notifications</div>
                                @endforelse
                            </div>
                        </div>
                        <span>{{ auth()->user()->tenant->name }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-brand-600/20 text-brand-400">{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <div class="p-6">
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="mb-4 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-sm flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-400/60 hover:text-green-400">&times;</button>
                </div>
                @endif
                @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-400/60 hover:text-red-400">&times;</button>
                </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
