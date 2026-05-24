@extends('layouts.dashboard', ['activeNav' => 'admin-blogs'])

@section('title', 'Edit blog')
@section('pageTitle', 'Edit blog')
@section('pageDescription', $blog->title)

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['label' => 'Blogs', 'url' => platform_route('admin.blogs.index')],
        ['label' => $blog->title, 'aria' => true],
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
                        'blog' => $blog,
                        'action' => platform_route('admin.blogs.update', $blog),
                        'method' => 'PUT',
                        'submitLabel' => 'Save changes',
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ dashboard_asset('plugins/quill/quill.js') }}"></script>
@endpush
