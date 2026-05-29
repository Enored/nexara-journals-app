@extends('layouts.dashboard', ['activeNav' => 'admin-blogs'])

@section('title', 'Blogs')
@section('pageTitle', 'Blogs')
@section('pageDescription', 'Manage blog posts with rich text editing and live preview.')

@section('headerActions')
    <x-dash.button :href="platform_route('admin.blogs.create')">
        <i data-lucide="plus" class="fs-sm me-1"></i>
        New blog
    </x-dash.button>
@endsection

@section('content')
    <x-dash.list-partial-zone>
        @include('admin.blogs.partials.list')
    </x-dash.list-partial-zone>
@endsection
