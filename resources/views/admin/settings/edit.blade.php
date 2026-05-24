@extends('layouts.dashboard', ['activeNav' => 'admin-settings'])

@section('title', 'System settings')
@section('pageTitle', 'System settings')
@section('pageDescription', 'Platform-wide configuration')

@section('breadcrumb')
    <x-admin.breadcrumb :items="[
        ['label' => 'System settings', 'aria' => true],
    ]" />
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-7">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ platform_route('admin.settings.update') }}">
                        @csrf
                        @method('PUT')

                        <x-dash.input
                            label="Platform name"
                            name="platform_name"
                            :value="old('platform_name', $settings->platform_name)"
                            required
                        />
                        <p class="form-text text-muted mb-3">
                            Shown in the dashboard, marketing site, and browser titles.
                        </p>

                        <div class="mb-4">
                            <div class="form-check">
                                <input
                                    type="hidden"
                                    name="maintenance_mode"
                                    value="0"
                                >
                                <input
                                    type="checkbox"
                                    name="maintenance_mode"
                                    value="1"
                                    id="maintenance-mode"
                                    class="form-check-input"
                                    @checked(old('maintenance_mode', $settings->maintenance_mode))
                                >
                                <label class="form-check-label" for="maintenance-mode">
                                    Maintenance mode
                                </label>
                            </div>
                            <p class="form-text text-muted mb-0 mt-1">
                                When enabled, visitors see a maintenance page. Platform admins can still use the site and change this setting.
                            </p>
                        </div>

                        <div class="mb-4 rounded border border-dashed bg-light-subtle p-3">
                            <label class="form-label text-muted mb-1">Favicon</label>
                            <p class="small text-muted mb-0">
                                Favicon upload will be available once cloud storage (Wasabi) is configured.
                                The default favicon from dashboard assets is used for now.
                            </p>
                        </div>

                        <x-dash.form-actions>
                            <x-dash.button type="submit">Save settings</x-dash.button>
                        </x-dash.form-actions>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
