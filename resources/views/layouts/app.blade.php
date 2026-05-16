<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4">
            <a href="{{ route('home') }}" class="text-lg font-semibold text-journal-primary">{{ config('app.name') }}</a>
            <nav class="flex items-center gap-3 text-sm font-medium">
                @auth
                    <a href="{{ platform_route('dashboard') }}" class="text-slate-600 hover:text-journal-primary">Dashboard</a>
                    <form method="POST" action="{{ platform_route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-600 hover:text-red-600">Log out</button>
                    </form>
                @else
                    <a href="{{ platform_route('login') }}" class="text-slate-600 hover:text-journal-primary">Log in</a>
                    <a href="{{ platform_route('register') }}" class="rounded-md bg-journal-primary px-3 py-1.5 text-white hover:opacity-90">Register</a>
                @endauth
            </nav>
        </div>
    </header>
    <main class="mx-auto max-w-7xl px-4 py-10">
        @if (session('status'))
            <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
