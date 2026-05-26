@extends('layouts.public')

@section('title', $journal->name)

@section('content')
<section class="hero_single version_2 start_bg_zoom"
         style="background: linear-gradient(rgba(173, 209, 228, 0.5), rgba(173, 209, 228, 0.5));background-size: 300px 500px;background-position:top;">
    <div class="wrapper abstract_wrapper">
        <div class="container">
            <h3>{{ $journal->name }}</h3>
            <ul>
                @if ($journal->issn)
                    <li>ISSN: {{ $journal->issn }}</li>
                @endif
                <li>Language: English</li>
                <li>Published Articles: {{ $publishedCount }}</li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-lg-offset-2 col-md-12 text-center" id="about_sub_menu">
            <ul>
                <li class="first"><a href="{{ route('home') }}" title="{{ $journal->name }} Home">Home</a></li>
                @auth
                    <li><a href="{{ route('journal.submit.create') }}" title="Submit Manuscript">Submit</a></li>
                @else
                    <li><a href="{{ platform_route('login') }}" title="Login to Submit">Submit</a></li>
                @endauth
                @can('manageEditions', $journal)
                    <li><a href="{{ platform_route('journal.editions.index', $journal) }}" title="Issues & Volumes">Issues</a></li>
                @endcan
            </ul>
        </div>
    </div>
</section>

{{-- Featured Latest Article --}}
@php
    $featuredArticle = $publishedByEdition->flatten()->first();
@endphp
@if ($featuredArticle)
    <div class="add_top_30 add_bottom_30">
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1 col-md-12">
                <div class="row the_journal">
                    <div class="col-lg-2 col-md-2 col-sm-2 hidden-xs j_img">
                        <a href="{{ route('journal.articles.show', $featuredArticle) }}">
                            <figure style="background: linear-gradient(rgba(173, 209, 228, 0.5), rgba(173, 209, 228, 0.5)), url('{{ asset('assets/public/img/bg/2.png') }}');background-size:cover;">
                                <div class="preview">
                                    <p>{{ $journal->name }}</p>
                                </div>
                            </figure>
                        </a>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 j_overview">
                        <p class="heading" style="color:#000;">{{ $featuredArticle->title }}</p>
                        <p class="date" style="color:#000;">
                            {{ $featuredArticle->submitted_at?->format('F, Y') ?? '' }}
                            @if ($featuredArticle->edition)
                                - Vol {{ $featuredArticle->edition->volume->number ?? '' }} Num. {{ $featuredArticle->edition->issue ?? '' }}
                            @endif
                        </p>
                        <p class="desc">{{ Str::limit($featuredArticle->abstract, 400) }}</p>
                        <p class="readmore">
                            <a href="{{ route('journal.articles.show', $featuredArticle) }}">Read more</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Articles Section --}}
<div class="bg_color_1">
    <div class="container">
        <div class="main_title_2">&nbsp;</div>
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-lg-9">

                        <div class="articles_filter">
                            <ul class="nav-tab tablist">
                                <li><strong>View:</strong></li>
                                <li><a data-toggle="tab" href="#most-recent" class="active">most recent</a></li>
                                <li><a data-toggle="tab" href="#forthcoming">forthcoming</a></li>
                            </ul>
                        </div>

                        <div class="main_articles tab-content">
                            {{-- Most Recent (Published) --}}
                            <div id="most-recent" class="tab-pane fade in active">
                                @forelse ($publishedByEdition->flatten() as $article)
                                    <div class="m_article">
                                        <p class="heading">{{ $article->title }}</p>
                                        <p class="date">{{ $article->submitted_at?->format('F, Y') ?? '' }}</p>
                                        <p class="author">{{ $article->author->name ?? 'Unknown' }}</p>
                                        <div class="details">
                                            <ul>
                                                <li>Type: {{ str_replace('_', ' ', $article->article_type ?? 'research') }}</li>
                                            </ul>
                                        </div>
                                        <div class="readmore">
                                            <a class="btn btn-success" href="{{ route('journal.articles.show', $article) }}">Abstract</a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="m_article">
                                        <p class="text-muted">No published articles yet.</p>
                                    </div>
                                @endforelse
                            </div>

                            {{-- Forthcoming (Accepted) --}}
                            <div id="forthcoming" class="tab-pane fade">
                                @forelse ($forthcoming as $article)
                                    <div class="m_article">
                                        <p class="heading">{{ $article->title }}</p>
                                        <p class="date">{{ $article->submitted_at?->format('F, Y') ?? '' }}</p>
                                        <p class="author">{{ $article->author->name ?? 'Unknown' }}</p>
                                        <div class="details">
                                            <ul>
                                                <li>Status: Accepted — awaiting publication</li>
                                            </ul>
                                        </div>
                                    </div>
                                @empty
                                    <div class="m_article">
                                        <p class="text-muted">No forthcoming articles.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>

                    {{-- Sidebar --}}
                    <div class="col-lg-3">
                        <div class="article_search">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search articles...">
                                <span class="input-group-addon">
                                    <span aria-hidden="true" class="icon-search"></span>
                                </span>
                            </div>
                        </div>

                        <div class="back_to_articles">
                            @auth
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <a href="{{ route('journal.submit.create') }}">
                                            Submit Manuscript <span aria-hidden="true" class="arrow_carrot-right pull-right"></span>
                                        </a>
                                    </span>
                                </div>
                            @else
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <a href="{{ platform_route('login') }}">
                                            Login to Submit <span aria-hidden="true" class="arrow_carrot-right pull-right"></span>
                                        </a>
                                    </span>
                                </div>
                            @endauth
                            @can('manageEditions', $journal)
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <a href="{{ platform_route('journal.editions.index', $journal) }}">
                                            Manage Issues <span aria-hidden="true" class="arrow_carrot-right pull-right"></span>
                                        </a>
                                    </span>
                                </div>
                            @endcan
                        </div>

                        @if ($journal->submission_guidelines)
                            <div class="article_quick_links">
                                <h5>Submission Guidelines</h5>
                                <p style="font-size: 12px; color: #666; padding: 10px;">{{ Str::limit($journal->submission_guidelines, 300) }}</p>
                            </div>
                        @endif

                        <div class="article_quick_links">
                            <ul>
                                <li><a href="#">Open Access</a></li>
                                <li><a href="#">Creative Commons</a></li>
                                <li><a href="#">Publication Ethics</a></li>
                                <li><a href="#">Editorial Policies</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Subscribe Section --}}
<div id="subscribe_section">
    <div class="row">
        <div class="col-lg-9 col-md-12 margin_10p">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <a href="@auth{{ route('journal.submit.create') }}@else{{ platform_route('login') }}@endauth">SUBMIT MANUSCRIPT <span aria-hidden="true" class="arrow_carrot-right pull-right"></span></a>
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <a href="{{ platform_route('register') }}">REGISTER <span aria-hidden="true" class="arrow_carrot-right pull-right"></span></a>
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <a href="{{ route('home') }}">JOURNAL HOME <span aria-hidden="true" class="arrow_carrot-right pull-right"></span></a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-12 subscribe_section_images">
            <ul>
                <li><img src="{{ asset('assets/public/img/open_access.png') }}" /></li>
                <li><img src="{{ asset('assets/public/img/creative_commons.png') }}" /></li>
            </ul>
        </div>
    </div>
</div>
@endsection
