<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Decline review</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 p-8">
    <div class="mx-auto max-w-lg rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-lg font-semibold text-slate-900">Decline review invitation</h1>
        <p class="mt-2 text-sm text-slate-600">Manuscript: <strong>{{ $assignment->submission->title }}</strong></p>
        <form method="POST" action="{{ url()->signedRoute('review-invitations.decline.submit', ['assignment' => $assignment->id]) }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">Optional reason</label>
                <textarea name="reason" rows="4" class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm">{{ old('reason') }}</textarea>
            </div>
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Decline invitation</button>
        </form>
    </div>
</body>
</html>
