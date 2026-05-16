@extends('layouts.app')

@section('title', 'Nexara Journals')

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Multi-journal publishing platform</h1>
            <p class="mt-3 max-w-2xl text-slate-600">
                Browse our journals below. Each journal lives on its own subdomain for submissions and public pages.
            </p>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-slate-800">Our journals</h2>
            <ul class="mt-4 grid gap-4 sm:grid-cols-2">
                @forelse ($journals as $j)
                    <li class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                        <p class="text-base font-semibold text-slate-900">{{ $j->name }}</p>
                        @if ($j->issn)
                            <p class="text-xs text-slate-500">ISSN {{ $j->issn }}</p>
                        @endif
                        <p class="mt-2 text-sm text-slate-600 line-clamp-3">{{ $j->description }}</p>
                        <p class="mt-3 text-xs text-slate-500">Subdomain: <code class="rounded bg-slate-100 px-1">{{ $j->subdomain }}</code></p>
                    </li>
                @empty
                    <li class="text-sm text-slate-600">No active journals yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
