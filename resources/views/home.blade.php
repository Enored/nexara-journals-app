@extends('layouts.public')

@section('title', platform_name() . ' - Home')

@section('content')
<div id="home_slider">
    <div class="slider home_slider">
        <div>
            <div class="image" style="background-image:linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.1)), url('{{ asset('assets/public/img/bg/2.png') }}');">
                <div class="container">
                    <div class="content">
                        <h2>Accelerating Discovery</h2>
                        <p>{{ platform_name() }} accelerates the dissemination of knowledge through the publication of high quality research articles using the open access model.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-lg-offset-2 col-md-12 text-center glow" id="our_journal_search">
            <form action="{{ route('home') }}" class="form-horizontal" method="get">
                <div class="input-group">
                    <input name="q" value="" class="form-control master-search-el" placeholder="Search for article" type="text" />
                    <span class="input-group-addon">
                        <span aria-hidden="true" class="icon-search"></span>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container home_quick_info">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 child child-overrun">
            <div class="news_conf">
                <div class="heading">
                    <div class="row">
                        <a href="@auth{{ platform_route('author.submissions') }}@else{{ platform_route('login') }}@endauth">
                            <div class="col-lg-3 col-md-2 col-xs-2">
                                <img class="img img-responsive hidden-xs" src="{{ asset('assets/public/img/submit_manuscript.png') }}">
                                <div class="menu-level-b">
                                    <span class="glyphicon glyphicon-upload hidden-sm hidden-md hidden-lg"></span>
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-10 col-xs-10">
                                <div class="menu-strap">SUBMIT MANUSCRIPT</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 child child-overrun">
            <div class="news_conf">
                <div class="heading">
                    <div class="row">
                        <a href="#journals">
                            <div class="col-lg-3 col-md-2 col-xs-2">
                                <img class="img img-responsive hidden-xs" src="{{ asset('assets/public/img/latest_news.png') }}">
                                <div class="menu-level-b">
                                    <span class="glyphicon glyphicon-folder-close hidden-sm hidden-md hidden-lg"></span>
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-10 col-xs-10">
                                <div class="menu-strap">JOURNALS</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 child child-overrun">
            <div class="news_conf">
                <div class="heading">
                    <div class="row">
                        <a href="#latest-articles">
                            <div class="col-lg-3 col-md-2 col-xs-2">
                                <img class="img img-responsive hidden-xs" src="{{ asset('assets/public/img/latest_news.png') }}">
                                <div class="menu-level-b">
                                    <span class="glyphicon glyphicon-folder-close hidden-sm hidden-md hidden-lg"></span>
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-10 col-xs-10">
                                <div class="menu-strap">LATEST ARTICLES</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Our Journals --}}
<div class="bg_color_1" id="journals">
    <div class="container">
        <div class="main_title_2"></div>

        <div class="row">
            <div class="col-lg-10 col-lg-offset-1 col-md-12">
                <div class="slider journals_scroller">
                    <div>
                        <div class="row six_j journal_boxes">
                            @forelse ($journals as $journal)
                                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 sub_child">
                                    <a href="{{ journal_front_url($journal) }}">
                                        <figure style="background: linear-gradient(rgba(173, 209, 228, 0.5), rgba(173, 209, 228, 0.5)), url('{{ asset('assets/public/img/bg/2.png') }}');">
                                            <div class="preview">
                                                <p>{{ $journal->name }}</p>
                                            </div>
                                        </figure>
                                    </a>
                                </div>
                            @empty
                                <div class="col-xs-12">
                                    <p class="text-center text-muted">No active journals yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Latest Published Articles --}}
<div class="container" id="latest-articles">
    <div class="main_title_22">
        <h3 class="black">Latest Published Articles</h3>
    </div>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="slider latest_articles">
                @forelse ($latestArticles as $article)
                    <div>
                        <div class="row the_journal">
                            <div class="col-lg-3 col-md-3 col-sm-3 hidden-xs j_img">
                                <a href="{{ route('journal.articles.show', $article) }}">
                                    <figure style="background: linear-gradient(rgba(173, 209, 228, 0.5), rgba(173, 209, 228, 0.5)), url('{{ asset('assets/public/img/bg/2.png') }}');">
                                        <div class="preview">
                                            <p>{{ $article->journal->name ?? 'Journal' }}</p>
                                        </div>
                                    </figure>
                                </a>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 j_overview">
                                <p class="heading orange">{{ $article->title }}</p>
                                <p class="date orange">{{ $article->submitted_at?->format('F Y') ?? '' }}</p>
                                <p class="desc">{{ Str::limit($article->abstract, 300) }}</p>
                                <p class="readmore btn btn-success">
                                    <a href="{{ route('journal.articles.show', $article) }}">Read more</a>
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div>
                        <div class="row the_journal">
                            <div class="col-xs-12 j_overview">
                                <p class="text-center text-muted">No published articles yet.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
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
                            <a href="@auth{{ platform_route('author.submissions') }}@else{{ platform_route('login') }}@endauth">SUBMIT MANUSCRIPT <span aria-hidden="true" class="arrow_carrot-right pull-right"></span></a>
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <a href="#journals">VIEW JOURNALS <span aria-hidden="true" class="arrow_carrot-right pull-right"></span></a>
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
