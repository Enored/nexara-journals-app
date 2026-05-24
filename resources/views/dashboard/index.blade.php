@extends('layouts.dashboard', ['activeNav' => 'overview'])

@section('title', 'Overview')
@section('pageTitle', 'Welcome back, '.$user->name)
@section('pageDescription', 'Pick a workspace below or from the sidebar.')

@section('content')
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
        @foreach ($sections as $section)
            <div class="col">
                <a href="{{ $section['route'] }}" class="card h-100 text-decoration-none text-body">
                    <div class="card-body">
                        <h5 class="card-title mb-2">{{ $section['label'] }}</h5>
                        <p class="card-text text-muted mb-0">{{ $section['description'] }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
