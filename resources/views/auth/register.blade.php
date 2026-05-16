@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="mx-auto max-w-md rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-xl font-semibold text-slate-900">Create account</h1>
        <form method="POST" action="{{ platform_route('register') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">Name</label>
                <input name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-journal-primary focus:ring-journal-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-journal-primary focus:ring-journal-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Password</label>
                <input type="password" name="password" required class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-journal-primary focus:ring-journal-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Confirm password</label>
                <input type="password" name="password_confirmation" required class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-journal-primary focus:ring-journal-primary">
            </div>
            <button type="submit" class="w-full rounded-md bg-journal-primary py-2 text-sm font-semibold text-white hover:opacity-90">Register</button>
        </form>
        <p class="mt-4 text-center text-sm text-slate-600">
            Already registered?
            <a href="{{ platform_route('login') }}" class="font-medium text-journal-primary hover:underline">Log in</a>
        </p>
    </div>
@endsection
