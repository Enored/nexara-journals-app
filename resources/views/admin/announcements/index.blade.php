@extends('layouts.dashboard', ['activeNav' => 'admin-announcements'])

@section('title', 'Announcements')
@section('pageTitle', 'Announcements')
@section('pageDescription', 'Manage global and per-journal announcements shown on journal home pages.')

@section('headerActions')
    <x-dash.button :href="platform_route('admin.announcements.create')">
        New announcement
    </x-dash.button>
@endsection

@section('content')
    <x-dash.list-partial-zone>
        @include('admin.announcements.partials.list')
    </x-dash.list-partial-zone>
@endsection
