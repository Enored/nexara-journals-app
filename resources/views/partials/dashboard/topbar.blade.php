<header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-4 border-b border-slate-200/80 bg-white/90 px-4 backdrop-blur-md sm:px-6 lg:px-8">
    <label for="dash-sidebar-toggle" class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 lg:hidden" aria-label="Open menu">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
    </label>

    <div class="min-w-0 flex-1">
        @hasSection('pageTitle')
            <h1 class="truncate text-lg font-semibold text-slate-900">@yield('pageTitle')</h1>
            @hasSection('pageDescription')
                <p class="truncate text-sm text-slate-500">@yield('pageDescription')</p>
            @endif
        @else
            <h1 class="truncate text-lg font-semibold text-slate-900">@yield('title', 'Dashboard')</h1>
        @endif
    </div>

    <div class="flex shrink-0 items-center gap-2">
        @hasSection('headerActions')
            @yield('headerActions')
        @endif
        <a href="{{ platform_url('/') }}" class="hidden items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 sm:inline-flex" title="Public site">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
            Site
        </a>
    </div>
</header>
