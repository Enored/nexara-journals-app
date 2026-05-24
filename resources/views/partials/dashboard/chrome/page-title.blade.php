<div class="page-title-head d-flex align-items-center">
    <div class="flex-grow-1">
        @hasSection('pageTitle')
            <h4 class="page-main-title m-0">@yield('pageTitle')</h4>
        @else
            <h4 class="page-main-title m-0">@yield('title', 'Dashboard')</h4>
        @endif
        @hasSection('pageDescription')
            <p class="text-muted mb-0 mt-1 fs-sm">@yield('pageDescription')</p>
        @endif
    </div>
    @if (View::hasSection('breadcrumb') || View::hasSection('headerActions'))
        <div class="text-end">
            @hasSection('breadcrumb')
                @yield('breadcrumb')
            @endif
            @hasSection('headerActions')
                <div class="d-flex flex-wrap gap-2 justify-content-end @if (View::hasSection('breadcrumb')) mt-2 @endif">
                    @yield('headerActions')
                </div>
            @endif
        </div>
    @endif
</div>
