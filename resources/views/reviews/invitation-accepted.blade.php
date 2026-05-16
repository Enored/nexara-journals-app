<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Review accepted</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 p-8">
    <div class="mx-auto max-w-lg rounded-lg border border-green-200 bg-white p-6 shadow-sm">
        <h1 class="text-lg font-semibold text-green-800">Thank you</h1>
        <p class="mt-2 text-sm text-slate-700">You have accepted the review invitation for <strong>{{ $assignment->submission->title }}</strong>.</p>
        <p class="mt-4 text-sm text-slate-600">Log in to your reviewer dashboard to complete the review before the deadline ({{ $assignment->deadline->format('Y-m-d') }}).</p>
    </div>
</body>
</html>
