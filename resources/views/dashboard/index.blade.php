@extends('layouts.dashboard', ['activeNav' => 'overview'])

@section('title', 'Overview')
@section('pageTitle', 'Welcome back, '.$user->name)
@section('pageDescription', 'Choose a workspace for your role across journals.')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($sections as $section)
            <a href="{{ $section['route'] }}" class="group dash-card flex flex-col p-6 transition hover:border-teal-200 hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <span class="inline-flex rounded-lg bg-teal-50 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-teal-800">{{ $section['label'] }}</span>
                    <svg class="h-5 w-5 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </div>
                <p class="mt-4 flex-1 text-sm leading-relaxed text-slate-600">{{ $section['description'] }}</p>
                <span class="mt-5 text-sm font-semibold text-teal-700 group-hover:underline">Open workspace</span>
            </a>
        @endforeach
    </div>
@endsection
