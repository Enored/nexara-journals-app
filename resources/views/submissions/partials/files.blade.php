<section class="dash-card p-6">
    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Files</h2>
    @if ($submission->files->isEmpty())
        <p class="mt-2 text-sm text-slate-500">No files uploaded yet.</p>
    @else
        <p class="mt-2 text-xs text-slate-500">Newer rounds appear first. Each row keeps its version so earlier uploads stay available.</p>
        <ul class="mt-3 divide-y divide-slate-100 text-sm">
            @foreach ($submission->files as $file)
                <li class="py-2">
                    <span class="inline-flex items-center rounded bg-slate-100 px-1.5 py-0.5 text-xs font-medium text-slate-700">v{{ $file->version }}</span>
                    <span class="font-medium text-slate-900">{{ $file->original_name }}</span>
                    <span class="block text-xs text-slate-500">{{ $file->file_type->value }} · {{ number_format($file->file_size / 1024, 1) }} KB · {{ $file->created_at->format('M j, Y g:i A') }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</section>
