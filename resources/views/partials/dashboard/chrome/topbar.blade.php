@php($u = auth()->user())

<header class="app-topbar">
    <div class="container-fluid topbar-menu">
        <div class="d-flex align-items-center gap-2">
            <div class="logo-topbar">
                <a href="{{ platform_route('dashboard') }}" class="logo-light">
                    <span class="logo-lg"><img src="{{ dashboard_asset('images/logo.png') }}" alt="{{ config('app.name') }}"></span>
                    <span class="logo-sm"><img src="{{ dashboard_asset('images/logo-sm.png') }}" alt="{{ config('app.name') }}"></span>
                </a>
                <a href="{{ platform_route('dashboard') }}" class="logo-dark">
                    <span class="logo-lg"><img src="{{ dashboard_asset('images/logo-black.png') }}" alt="{{ config('app.name') }}"></span>
                    <span class="logo-sm"><img src="{{ dashboard_asset('images/logo-sm.png') }}" alt="{{ config('app.name') }}"></span>
                </a>
            </div>

            <button class="sidenav-toggle-button btn btn-default btn-icon" type="button" aria-label="Toggle sidebar">
                <i data-lucide="menu"></i>
            </button>
        </div>

        <div class="d-flex align-items-center gap-1 ms-auto">
            <div class="topbar-item d-none d-sm-flex">
                <a href="{{ platform_url('/') }}" class="topbar-link" title="Public site">
                    <i data-lucide="external-link" class="topbar-link-icon"></i>
                </a>
            </div>

            <div id="fullscreen-toggler" class="topbar-item d-none d-md-flex">
                <button class="topbar-link" type="button" data-toggle="fullscreen" aria-label="Fullscreen">
                    <i data-lucide="maximize" class="topbar-link-icon"></i>
                    <i data-lucide="minimize" class="topbar-link-icon d-none"></i>
                </button>
            </div>

            <div class="topbar-item">
                <button
                    type="button"
                    class="topbar-link"
                    data-dash-theme-toggle
                    aria-label="Switch to dark mode"
                    title="Toggle light/dark mode"
                >
                    <i data-lucide="sun" class="topbar-link-icon dash-theme-icon-light"></i>
                    <i data-lucide="moon" class="topbar-link-icon dash-theme-icon-dark d-none"></i>
                </button>
            </div>

            <div id="simple-user-dropdown" class="topbar-item nav-user">
                <div class="dropdown">
                    <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown" href="#!" aria-haspopup="false" aria-expanded="false">
                        <span class="avatar-sm rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center fw-semibold me-lg-2">
                            {{ $u->initials() }}
                        </span>
                        <div class="d-lg-flex align-items-center gap-1 d-none">
                            <h5 class="my-0">{{ $u->name }}</h5>
                            <i data-lucide="chevron-down" class="align-middle"></i>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome back!</h6>
                        </div>
                        <a href="{{ platform_route('settings.edit') }}" class="dropdown-item">
                            <i data-lucide="settings" class="me-1 fs-lg align-middle"></i>
                            <span class="align-middle">Account settings</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ platform_route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger fw-semibold w-100 text-start border-0 bg-transparent">
                                <i data-lucide="log-out" class="me-1 fs-lg align-middle"></i>
                                <span class="align-middle">Log out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
