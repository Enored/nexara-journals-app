<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased" style="font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif;">
    <div id="dash-app" class="flex min-h-screen w-full bg-slate-100">
        <input type="checkbox" id="dash-sidebar-toggle" class="peer/sidebar fixed opacity-0 pointer-events-none" aria-hidden="true">
        <label for="dash-sidebar-toggle" class="fixed inset-0 z-40 hidden bg-slate-900/50 backdrop-blur-sm peer-checked/sidebar:block lg:hidden" aria-label="Close menu"></label>

        @include('partials.dashboard.sidebar', ['activeNav' => $activeNav ?? ''])

        <div class="flex min-h-screen flex-1 flex-col bg-slate-100 lg:pl-64">
            @include('partials.dashboard.topbar')

            <main class="flex-1 bg-slate-100 p-4 sm:p-6 lg:p-8">
                @include('partials.dashboard.flash')
                @yield('content')
            </main>
        </div>
    </div>

    @include('partials.dashboard.toasts')

    <script>
        document.getElementById('dash-sidebar-toggle')?.addEventListener('change', function (e) {
            document.body.classList.toggle('overflow-hidden', e.target.checked);
        });

        (function () {
            const trigger = document.getElementById('dash-profile-trigger');
            const menu = document.getElementById('dash-profile-menu');
            if (!trigger || !menu) {
                return;
            }

            const close = () => {
                menu.classList.add('hidden');
                trigger.setAttribute('aria-expanded', 'false');
            };

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                menu.classList.toggle('hidden');
                trigger.setAttribute('aria-expanded', menu.classList.contains('hidden') ? 'false' : 'true');
            });

            document.addEventListener('click', function (e) {
                if (!menu.contains(e.target) && !trigger.contains(e.target)) {
                    close();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    close();
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
