@php
    use App\Support\DashboardNavigation;

    $u = auth()->user();
    $nav = DashboardNavigation::forUser($u, $activeNav ?? '');
    $showRolePicker = $nav['showRolePicker'];
    $sectionTitle = $nav['sectionTitle'];
    $activeRoleMeta = $nav['activeRoleMeta'];
    $isNavActive = fn (string $key) => ($activeNav ?? '') === $key;
@endphp

<div class="sidenav-menu">
    <a href="{{ platform_route('dashboard') }}" class="logo">
        <span class="logo logo-light">
            <span class="logo-lg" style="font-size: 1rem; font-weight: 600; color: #fff; white-space: nowrap;">Nexara Journals</span>
            <span class="logo-sm" style="font-size: 0.75rem; font-weight: 700; color: #fff;">NJ</span>
        </span>
        <span class="logo logo-dark">
            <span class="logo-lg" style="font-size: 1rem; font-weight: 600; color: #313a46; white-space: nowrap;">Nexara Journals</span>
            <span class="logo-sm" style="font-size: 0.75rem; font-weight: 700; color: #313a46;">NJ</span>
        </span>
    </a>

    <button class="button-on-hover" type="button" aria-label="Expand sidebar on hover">
        <span class="btn-on-hover-icon"></span>
    </button>

    <button class="button-close-offcanvas" type="button" aria-label="Close sidebar">
        <i data-lucide="menu" class="align-middle"></i>
    </button>

    <div class="scrollbar sidenav-scroll" data-simplebar>
        <div id="sidenav-menu">
            <ul class="side-nav">
                @if ($showRolePicker)
                    <li class="side-nav-title mt-2">Workspaces</li>
                    <p class="text-muted fs-xs px-3 mb-2">Choose where you want to work. Each workspace has its own tools.</p>
                    @foreach ($nav['roles'] as $role)
                        <li class="side-nav-item">
                            <a href="{{ $role['route'] }}" class="side-nav-link">
                                <span class="menu-icon"><i data-lucide="{{ $role['icon'] }}"></i></span>
                                <span class="menu-text">{{ $role['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                @else
                    <li class="side-nav-item">
                        <a href="{{ platform_route('dashboard') }}" class="side-nav-link">
                            <span class="menu-icon"><i data-lucide="arrow-left"></i></span>
                            <span class="menu-text">Switch workspace</span>
                        </a>
                    </li>

                    <li class="side-nav-title mt-2 d-flex align-items-center gap-2">
                        <i data-lucide="{{ $activeRoleMeta['icon'] }}" class="fs-md"></i>
                        <span>{{ $sectionTitle }}</span>
                    </li>
                    @foreach ($nav['items'] as $item)
                        <li class="side-nav-item">
                            <a href="{{ $item['route'] }}" class="side-nav-link {{ $isNavActive($item['key']) ? 'active' : '' }}">
                                <span class="menu-icon"><i data-lucide="{{ $item['icon'] }}"></i></span>
                                <span class="menu-text">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    </div>

    <div
        id="user-profile-settings"
        class="sidenav-user sidenav-user-footer"
        style="background-image: url('{{ dashboard_asset('images/user-bg-pattern.svg') }}')"
    >
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ platform_route('settings.edit') }}" class="link-reset">
                    <span class="avatar-md rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center fw-bold mb-2">
                        {{ $u->initials() }}
                    </span>
                    <span class="sidenav-user-name fw-bold d-block">{{ $u->name }}</span>
                    <span class="fs-12 fw-semibold text-muted">{{ $u->email }}</span>
                </a>
            </div>
            <div>
                <a class="dropdown-toggle drop-arrow-none link-reset sidenav-user-set-icon" data-bs-toggle="dropdown" data-bs-offset="0,12" href="#!" aria-haspopup="false" aria-expanded="false">
                    <i data-lucide="settings" class="fs-24 align-middle ms-1"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Welcome back!</h6>
                    </div>
                    <a href="{{ platform_route('settings.edit') }}" class="dropdown-item">
                        <i data-lucide="settings" class="me-1 fs-lg align-middle"></i>
                        <span class="align-middle">Account settings</span>
                    </a>
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
