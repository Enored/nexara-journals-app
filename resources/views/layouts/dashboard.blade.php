<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-skin="default"
    data-bs-theme="light"
    data-menu-color="light"
    data-topbar-color="light"
    data-layout-width="fluid"
    data-layout-position="fixed"
    data-sidenav-size="default"
    data-sidenav-user="true"
    dir="ltr"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ ubold_asset('images/favicon.ico') }}">
    <script src="{{ ubold_asset('js/config.js') }}"></script>
    <link href="{{ ubold_asset('css/vendors.min.css') }}" rel="stylesheet" type="text/css">
    <link id="app-style" href="{{ ubold_asset('css/app.min.css') }}" rel="stylesheet" type="text/css">
    @vite(['resources/css/dashboard-ubold-bridge.css', 'resources/js/dashboard.js'])
    @stack('head')
</head>
<body>
    <div class="wrapper">
        @include('partials.dashboard.ubold.topbar')
        @include('partials.dashboard.ubold.sidenav')

        <div class="content-page">
            <div class="container-fluid">
                @include('partials.dashboard.impersonation-banner')
                @include('partials.dashboard.ubold.page-title')
                @yield('content')
            </div>
        </div>
    </div>

    @stack('modals')

    @include('partials.dashboard.admin-action-confirm-modal')

    @include('partials.dashboard.ubold.theme-offcanvas')
    @include('partials.dashboard.toasts')

    <script src="{{ ubold_asset('js/vendors.min.js') }}"></script>
    <script src="{{ ubold_asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
