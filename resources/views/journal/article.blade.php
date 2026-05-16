@extends('layouts.app')

@section('title', $submission->title)

@section('content')
    <article @if($journal->primary_color) style="--journal-primary: {{ $journal->primary_color }}" @endif class="prose prose-slate mx-auto max-w-3xl">
        <p class="text-sm text-slate-500 not-prose"><a href="{{ route('home') }}" class="text-journal-primary hover:underline">{{ $journal->name }}</a></p>
        @if ($submission->edition)
            <p class="text-sm text-slate-600 not-prose">Vol. {{ $submission->edition->volume }}, No. {{ $submission->edition->issue }}</p>
        @endif
        <h1 class="not-prose text-3xl font-bold tracking-tight text-slate-900">{{ $submission->title }}</h1>
        <p class="not-prose text-slate-600">{{ $submission->author->name }}</p>
        <h2>Abstract</h2>
        <p>{{ $submission->abstract }}</p>
        @if (! empty($submission->keywords))
            <h2>Keywords</h2>
            <p>{{ implode(', ', $submission->keywords) }}</p>
        @endif
    </article>
@endsection
