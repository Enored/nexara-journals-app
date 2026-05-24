@extends('layouts.dashboard', ['activeNav' => 'admin-blogs'])

@section('title', 'New blog')
@section('pageTitle', 'New blog')
@section('pageDescription', 'Write and preview your blog before publishing.')

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['label' => 'Blogs', 'url' => platform_route('admin.blogs.index')],
        ['label' => 'New blog', 'aria' => true],
    ]" />
@endsection

@push('head')
    <link href="{{ dashboard_asset('plugins/quill/quill.snow.css') }}" rel="stylesheet" type="text/css">
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="card">
                <div class="card-body">
                    @include('admin.blogs.partials.form', [
                        'action' => platform_route('admin.blogs.store'),
                        'method' => 'POST',
                        'submitLabel' => 'Create blog',
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ dashboard_asset('plugins/quill/quill.js') }}"></script>
@endpush
