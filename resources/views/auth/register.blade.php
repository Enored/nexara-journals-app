<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register — {{ platform_name() }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-slate-800 hover:text-slate-900 no-underline">Nexara Journals</a>
            <p class="mt-2 text-sm text-slate-500">Create your account</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-8 shadow-lg">
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ platform_route('register') }}" class="space-y-5">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1.5">First name</label>
                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="{{ old('first_name') }}"
                            required
                            autofocus
                            autocomplete="given-name"
                            placeholder="John"
                            class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:outline-none"
                        >
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1.5">Last name</label>
                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="{{ old('last_name') }}"
                            required
                            autocomplete="family-name"
                            placeholder="Doe"
                            class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:outline-none"
                        >
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="you@example.com"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:outline-none"
                    >
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:outline-none"
                    >
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        placeholder="••••••••"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 focus:outline-none"
                    >
                </div>
                <button
                    type="submit"
                    class="w-full rounded-lg bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500/50 focus:ring-offset-2"
                >
                    Create account
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-sm text-slate-500">
            Already have an account?
            <a href="{{ platform_route('login') }}" class="font-medium text-teal-600 hover:text-teal-700 hover:underline">Sign in</a>
        </p>
    </div>
</body>
</html>
