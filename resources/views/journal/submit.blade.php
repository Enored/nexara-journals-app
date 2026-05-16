@extends('layouts.app')

@section('title', 'Submit — '.$journal->name)

@section('content')
    <div @if($journal->primary_color) style="--journal-primary: {{ $journal->primary_color }}" @endif class="mx-auto max-w-2xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Submit to {{ $journal->name }}</h1>
            <p class="mt-2 text-sm text-slate-600">Provide manuscript metadata. File upload can be completed via the API or a follow-up release.</p>
        </div>
        <form method="POST" action="{{ route('journal.submit.store') }}" class="space-y-4 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">Title</label>
                <input name="title" value="{{ old('title') }}" required class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-journal-primary focus:ring-journal-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Abstract</label>
                <textarea name="abstract" rows="6" required class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-journal-primary focus:ring-journal-primary">{{ old('abstract') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Keywords (comma-separated)</label>
                <input name="keywords" value="{{ old('keywords') }}" required class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-journal-primary focus:ring-journal-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Article type</label>
                <input name="article_type" value="{{ old('article_type', 'Research Article') }}" required class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-journal-primary focus:ring-journal-primary">
            </div>
            <button type="submit" class="w-full rounded-md bg-journal-primary py-2 text-sm font-semibold text-white hover:opacity-90">Submit</button>
        </form>
    </div>
@endsection
