<div {{ $attributes->merge(['class' => 'dash-table-wrap']) }}>
    <table class="dash-table">
        @isset($header)
            <thead>{{ $header }}</thead>
        @endisset
        <tbody>{{ $body ?? $slot }}</tbody>
    </table>
    @isset($footer)
        <div class="border-t border-slate-100 bg-white px-4 py-3">{{ $footer }}</div>
    @endisset
</div>
