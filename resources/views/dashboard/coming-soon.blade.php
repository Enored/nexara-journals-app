@extends('layouts.dashboard', ['activeNav' => $activeNav])

@section('title', $title)
@section('pageTitle', $pageTitle)
@section('pageDescription', $pageDescription)

@section('content')
    <x-dash.empty
        title="Coming soon"
        description="We are building this dashboard. Use the menu to open your existing workspaces in the meantime."
    />
@endsection
