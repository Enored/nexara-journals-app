<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-skin="modern"
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
    <title>@yield('title', 'Dashboard') — {{ platform_name() }}</title>
    <link rel="shortcut icon" href="{{ dashboard_asset('images/favicon.ico') }}">
    @vite(['resources/js/dashboard-theme.js'])
    <link href="{{ dashboard_asset('css/vendors.min.css') }}" rel="stylesheet" type="text/css">
    <link id="app-style" href="{{ dashboard_asset('css/app.min.css') }}" rel="stylesheet" type="text/css">
    @vite(['resources/css/dashboard-bridge.css', 'resources/js/dashboard.js'])
    @stack('head')
</head>
<body>
    <div class="wrapper">
        @include('partials.dashboard.chrome.topbar')
        @include('partials.dashboard.chrome.sidenav')

        <div class="content-page">
            <div class="container-fluid">
                @include('partials.dashboard.impersonation-banner')
                @include('partials.dashboard.chrome.page-title')
                @yield('content')
            </div>
        </div>
    </div>

    @stack('modals')

    @include('partials.dashboard.admin-action-confirm-modal')

    @include('partials.dashboard.toasts')

    <script src="{{ dashboard_asset('js/vendors.min.js') }}"></script>
    <script src="{{ dashboard_asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
