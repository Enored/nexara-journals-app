@php
    $articles = $publishedByEdition->flatten();
    $primary = $journal->primary_color ?? '#0F4C81';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $journal->name }} : {{ platform_name() }} Journals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,400;8..60,600;8..60,700&display=swap" rel="stylesheet">
    <style>
        :root {
            --journal-primary: {{ $primary }};
            --journal-primary-soft: color-mix(in srgb, var(--journal-primary) 10%, white);
            --journal-primary-tint: color-mix(in srgb, var(--journal-primary) 18%, white);
            --journal-primary-ring: color-mix(in srgb, var(--journal-primary) 30%, white);
        }
        html, body { font-family: 'Inter', system-ui, sans-serif; color: #1f2937; background: #ffffff; }
        [x-cloak] { display: none !important; }
        .font-serif-display { font-family: 'Source Serif 4', Georgia, serif; }
        .j-bg { background-color: var(--journal-primary); }
        .j-bg-soft { background-color: var(--journal-primary-soft); }
        .j-text { color: var(--journal-primary); }
        .j-border { border-color: var(--journal-primary); }
        .j-btn { background-color: var(--journal-primary); color: #fff; transition: filter .15s ease; }
        .j-btn:hover { filter: brightness(0.88); }
        .j-btn-outline { color: var(--journal-primary); border: 1px solid var(--journal-primary); }
        .j-btn-outline:hover { background-color: var(--journal-primary-soft); }
        .j-focus:focus { outline: none; box-shadow: 0 0 0 3px var(--journal-primary-ring); }
        .card-soft { box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 4px 16px rgba(15, 23, 42, 0.06); }
        .card-hover { transition: box-shadow .18s ease, transform .18s ease; }
        .card-hover:hover { box-shadow: 0 2px 4px rgba(15, 23, 42, 0.06), 0 12px 28px rgba(15, 23, 42, 0.10); transform: translateY(-1px); }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-4 { display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .container-x { width: 100%; max-width: 1280px; margin-left: auto; margin-right: auto; padding-left: 1.25rem; padding-right: 1.25rem; }
    </style>
</head>
<body class="antialiased">

    <div class="bg-slate-900 text-slate-200 text-xs">
        <div class="container-x flex justify-between items-center h-9">
            <div class="flex items-center gap-5">
                <span class="hidden sm:inline-flex items-center gap-1.5"><i class="fa-solid fa-building-columns text-[10px]"></i> Institutional Access</span>
                <a href="#" class="hover:text-white">Help</a>
            </div>
            <div class="flex items-center gap-5">
                <a href="#" class="hover:text-white">Browse Journals</a>
                <a href="#" class="hover:text-white hidden sm:inline">For Authors</a>
                @guest
                    <a href="{{ platform_route('login') }}" class="hover:text-white inline-flex items-center gap-1"><i class="fa-regular fa-user text-[10px]"></i> Sign in</a>
                @endguest
            </div>
        </div>
    </div>

    <header class="bg-white border-b border-slate-200">
        <div class="container-x flex items-center justify-between gap-6 h-16">
            <a href="{{ platform_url('/') }}" class="flex items-center gap-3 shrink-0">
                <div class="h-9 w-9 rounded-md bg-slate-900 text-white grid place-items-center font-serif-display font-bold text-lg">{{ substr(platform_name(), 0, 1) }}</div>
                <div class="leading-tight">
                    <div class="font-serif-display font-bold text-slate-900 text-[17px] tracking-tight">{{ platform_name() }}</div>
                    <div class="text-[11px] text-slate-500 uppercase tracking-[0.14em]">Journals</div>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-5 text-sm text-slate-600">
                <a href="{{ platform_url('/') }}" class="hover:text-slate-900">Home</a>
                <a href="#" class="hover:text-slate-900">Journals</a>
                <a href="#" class="hover:text-slate-900">Support</a>
            </div>

            <div class="flex items-center gap-4 text-sm">
                <a href="#" class="hidden lg:inline-flex items-center gap-1.5 text-slate-600 hover:text-slate-900"><i class="fa-regular fa-bell"></i> Alerts</a>
                @auth
                    @php
                        $currentUser = auth()->user();
                    @endphp
                    <div x-data="{ open: false }" x-on:click.outside="open = false" x-on:keydown.escape.window="open = false" class="relative">
                        <button type="button" x-on:click="open = !open" x-bind:aria-expanded="open.toString()"
                                class="flex items-center gap-2 py-1 pr-2 pl-1 rounded-md hover:bg-slate-100 transition-colors focus:outline-none">
                            <span class="h-8 w-8 rounded-full j-bg text-white grid place-items-center text-xs font-semibold">
                                {{ $currentUser->initials() }}
                            </span>
                            <span class="hidden sm:inline text-slate-800 font-medium">{{ $currentUser->first_name ?: $currentUser->name }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-500"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition.opacity.duration.150ms
                             class="absolute right-0 mt-2 w-56 bg-white rounded-md border border-slate-200 card-soft py-1 z-50">
                            <div class="px-4 py-3 border-b border-slate-100">
                                <div class="text-sm font-semibold text-slate-900 truncate">{{ $currentUser->name }}</div>
                                <div class="text-xs text-slate-500 truncate">{{ $currentUser->email }}</div>
                            </div>
                            <a href="{{ platform_route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-gauge-high text-slate-400 w-4 text-center"></i> Dashboard
                            </a>
                            <a href="{{ platform_route('settings.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-gear text-slate-400 w-4 text-center"></i> Settings
                            </a>
                            <div class="border-t border-slate-100"></div>
                            <form method="POST" action="{{ platform_route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <i class="fa-solid fa-right-from-bracket text-slate-400 w-4 text-center"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ platform_route('login') }}" class="j-btn-outline px-4 py-1.5 rounded-md text-sm font-medium">Sign in</a>
                @endauth
            </div>
        </div>
    </header>

    <div class="h-[5px] j-bg"></div>

    <section class="bg-slate-50 border-b border-slate-200">
        <div class="container-x py-8">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <div class="shrink-0">
                    @if($journal->logo_path)
                        <img src="{{ asset($journal->logo_path) }}" alt="{{ $journal->name }}" class="h-28 w-24 rounded-md object-cover card-soft bg-white">
                    @else
                        <div class="h-28 w-24 rounded-md j-bg text-white grid place-items-center font-serif-display font-bold text-4xl card-soft">
                            {{ strtoupper(substr($journal->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <h1 class="font-serif-display font-bold text-slate-900 text-3xl md:text-[34px] leading-tight tracking-tight">
                        {{ $journal->name }}
                    </h1>
                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-600">
                        <span class="inline-flex items-center gap-1.5 j-text font-semibold">
                            <i class="fa-solid fa-unlock-keyhole text-[11px]"></i> Open access
                        </span>
                        @if($journal->issn)
                            <span class="text-slate-300">|</span>
                            <span>ISSN: <span class="font-medium text-slate-700">{{ $journal->issn }}</span></span>
                        @endif
                    </div>
                </div>

                <div class="shrink-0">
                    <a href="#" class="j-btn px-5 py-2.5 rounded-md text-sm font-semibold whitespace-nowrap inline-flex items-center">
                        Submission Guidelines
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="container-x flex flex-col md:flex-row md:items-center gap-3 py-3">
            <button type="button" class="text-sm font-semibold text-slate-800 inline-flex items-center gap-2 hover:text-slate-900">
                Articles &amp; Issues <i class="fa-solid fa-chevron-down text-[10px] text-slate-500"></i>
            </button>
            <form action="#" method="GET" class="flex-1 md:max-w-xl md:ml-4">
                <div class="relative flex">
                    <input type="text" placeholder="Search in this journal"
                           class="w-full text-sm border border-slate-300 rounded-l-md px-3 py-2 j-focus">
                    <button type="submit" class="j-btn px-4 rounded-r-md text-sm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <main class="container-x py-10 grid grid-cols-1 lg:grid-cols-12 gap-8">

        <div class="lg:col-span-8 min-w-0 space-y-8">

            <section id="about" class="rounded-lg p-6 bg-white card-soft">
                <h2 class="font-serif-display font-semibold text-xl text-slate-900 mb-3">About the journal</h2>
                <p class="text-[15px] text-slate-700 leading-relaxed">
                    {{ $journal->description ?? 'A peer-reviewed academic journal publishing original research, reviews, and editorials across its disciplinary scope.' }}
                </p>
            </section>

            <section id="articles" x-data="{ tab: 'latest' }">
                <h2 class="font-serif-display font-semibold text-2xl text-slate-900 mb-4">Articles</h2>

                <div class="flex border-b border-slate-200 overflow-x-auto hide-scrollbar mb-2">
                    @php
                        $tabs = [
                            ['id' => 'latest',     'label' => 'Latest published'],
                            ['id' => 'top-cited',  'label' => 'Top cited'],
                            ['id' => 'downloaded', 'label' => 'Most downloaded'],
                            ['id' => 'popular',    'label' => 'Most popular'],
                        ];
                    @endphp
                    @foreach($tabs as $t)
                        <button type="button"
                                x-on:click="tab = '{{ $t['id'] }}'"
                                x-bind:class="tab === '{{ $t['id'] }}' ? 'j-text border-b-2 j-border' : 'text-slate-600 border-b-2 border-transparent hover:text-slate-900'"
                                class="px-5 py-3 text-sm font-semibold whitespace-nowrap transition-colors -mb-px">
                            {{ $t['label'] }}
                        </button>
                    @endforeach
                </div>

                @if($articles->isEmpty())
                    <div class="bg-white rounded-lg p-12 text-center card-soft mt-6">
                        <i class="fa-regular fa-folder-open text-4xl text-slate-300"></i>
                        <p class="mt-3 text-sm text-slate-500">No articles have been published yet.</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-200">
                        @foreach($articles as $article)
                            <li class="py-5">
                                <div class="flex items-center flex-wrap text-[12px] text-slate-600 mb-2">
                                    <span class="inline-flex items-center gap-1.5 j-text font-semibold">
                                        <i class="fa-solid fa-unlock-keyhole text-[10px]"></i> Open access
                                    </span>
                                    <span class="mx-3 text-slate-300">|</span>
                                    <span>{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $article->article_type ?? 'Research Article')) }}</span>
                                    <span class="mx-3 text-slate-300">|</span>
                                    <span>First published {{ $article->submitted_at?->format('M j, Y') ?? '—' }}</span>
                                </div>

                                <a href="{{ route('journal.articles.show', $article) }}" class="block group">
                                    <h3 class="font-serif-display font-semibold text-slate-900 text-[18px] leading-snug group-hover:j-text transition-colors">
                                        {{ $article->title }}
                                    </h3>
                                </a>

                                <p class="mt-1.5 text-sm text-slate-600">
                                    {{ $article->author->name ?? 'Unknown Author' }}
                                </p>

                                @if($article->edition)
                                    <p class="mt-1.5 text-xs text-slate-500">
                                        Volume {{ $article->edition->volume->number }}, Issue {{ $article->edition->issue }}
                                    </p>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    <div class="pt-6 border-t border-slate-200 flex flex-wrap gap-3">
                        <a href="#" class="j-btn-outline px-5 py-2 rounded-md text-sm font-semibold">View Current Issue</a>
                        <a href="#" class="j-btn-outline px-5 py-2 rounded-md text-sm font-semibold">View All Issues</a>
                    </div>
                @endif
            </section>

        </div>

        <aside class="lg:col-span-4 space-y-5">

            <div class="bg-white rounded-lg p-5 card-soft">
                <h3 class="j-text font-serif-display font-semibold text-base pb-2 border-b border-slate-200 mb-3">Browse journal</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="text-slate-700 hover:j-text">Current issue</a></li>
                    <li><a href="#" class="text-slate-700 hover:j-text">All issues</a></li>
                </ul>
            </div>

            <div class="bg-white rounded-lg p-5 card-soft">
                <h3 class="j-text font-serif-display font-semibold text-base pb-2 border-b border-slate-200 mb-3">Journal information</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="text-slate-700 hover:j-text">Overview and metrics</a></li>
                    <li><a href="#" class="text-slate-700 hover:j-text">Submission guidelines</a></li>
                </ul>
            </div>

            <div class="bg-white rounded-lg p-5 card-soft">
                <h3 class="j-text font-serif-display font-semibold text-base pb-2 border-b border-slate-200 mb-3">Keep up to date</h3>
                <ul class="space-y-2.5 text-sm mb-5">
                    <li><a href="#" class="text-slate-700 hover:j-text inline-flex items-center gap-2"><i class="fa-brands fa-facebook text-blue-600 w-4"></i> Facebook</a></li>
                    <li><a href="#" class="text-slate-700 hover:j-text inline-flex items-center gap-2"><i class="fa-brands fa-x-twitter text-slate-900 w-4"></i> X</a></li>
                    <li><a href="#" class="text-slate-700 hover:j-text inline-flex items-center gap-2"><i class="fa-brands fa-linkedin text-blue-700 w-4"></i> LinkedIn</a></li>
                    <li><a href="#" class="text-slate-700 hover:j-text inline-flex items-center gap-2"><i class="fa-brands fa-youtube text-red-600 w-4"></i> YouTube</a></li>
                    <li><a href="#" class="text-slate-700 hover:j-text inline-flex items-center gap-2"><i class="fa-solid fa-rss text-orange-500 w-4"></i> RSS feed</a></li>
                </ul>
                <h4 class="font-semibold text-sm text-slate-800 mb-1">Email alerts</h4>
                <p class="text-xs text-slate-500 mb-3">Sign up to receive email alerts:</p>
                <ul class="list-disc pl-5 text-xs text-slate-500 space-y-1 mb-4">
                    <li>With the latest table of contents</li>
                    <li>When new articles are published online</li>
                </ul>
                <button type="button" class="j-btn w-full py-2 rounded-md text-sm font-semibold">Sign Up</button>
            </div>

        </aside>

        <section class="lg:col-span-12 rounded-lg p-8 bg-white card-soft flex flex-col md:flex-row items-center gap-6">
            <div class="h-14 w-14 rounded-full j-bg-soft j-text grid place-items-center shrink-0">
                <i class="fa-solid fa-bullhorn text-xl"></i>
            </div>
            <div class="flex-1 text-center md:text-left">
                <h3 class="font-serif-display font-semibold text-xl text-slate-900">More opportunities to publish your research</h3>
                <p class="text-sm text-slate-600 mt-1">Explore open calls for papers and special issues from this journal.</p>
            </div>
            <a href="#" class="j-btn-outline px-5 py-2.5 rounded-md text-sm font-semibold whitespace-nowrap">
                Browse open Calls for Papers
            </a>
        </section>

    </main>

    <footer class="bg-slate-900 text-slate-300 mt-12">
        <div class="container-x py-10 grid grid-cols-1 md:grid-cols-4 gap-8 text-sm">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="h-8 w-8 rounded-md bg-white text-slate-900 grid place-items-center font-serif-display font-bold">{{ substr(platform_name(), 0, 1) }}</div>
                    <span class="font-serif-display font-bold text-white text-base">{{ platform_name() }}</span>
                </div>
                @if($journal->issn)
                    <p class="text-xs text-slate-400">ISSN: {{ $journal->issn }}</p>
                @endif
                <p class="text-xs text-slate-400 mt-1">&copy; {{ date('Y') }} {{ platform_name() }}. All rights reserved.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold text-xs uppercase tracking-wider mb-3">For authors</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="#" class="hover:text-white">Author guidelines</a></li>
                    <li><a href="#" class="hover:text-white">Track your paper</a></li>
                    <li><a href="#" class="hover:text-white">Rights &amp; permissions</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold text-xs uppercase tracking-wider mb-3">For editors</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="#" class="hover:text-white">Resources for editors</a></li>
                    <li><a href="#" class="hover:text-white">Publishing ethics</a></li>
                    <li><a href="#" class="hover:text-white">Guest editors</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold text-xs uppercase tracking-wider mb-3">For reviewers</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="#" class="hover:text-white">Resources for reviewers</a></li>
                    <li><a href="#" class="hover:text-white">Reviewer recognition</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800">
            <div class="container-x py-5 flex flex-col md:flex-row justify-between items-center gap-3 text-xs text-slate-400">
                <div class="flex gap-5">
                    <a href="#" class="hover:text-white">About {{ platform_name() }}</a>
                    <a href="#" class="hover:text-white">Contact</a>
                    <a href="#" class="hover:text-white">Privacy</a>
                    <a href="#" class="hover:text-white">Terms</a>
                </div>
                <div>All content on this site is licensed under the relevant licensing terms.</div>
            </div>
        </div>
    </footer>

</body>
</html>
