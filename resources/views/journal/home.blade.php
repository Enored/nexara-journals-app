@php
    $articles = $publishedByEdition->flatten()->values();
    $journalPayload = [
        'name' => $journal->name ?: 'Journal of Computational Cognition',
        'short' => $journal->abbreviation ?: 'J. Comput. Cognition',
        'founded' => 2008,
        'issn_online' => $journal->issn ?: '2845-1739',
        'issn_print' => '2845-1720',
        'doiPrefix' => '10.31472/jcc',
        'frequency' => 'Quarterly · 4 issues / year',
        'impact' => '6.42',
        'acceptance' => '18%',
        'citeScore' => '9.1',
        'timeToFirstDecision' => '31 days',
        'tagline' => $journal->description ?: 'A peer-reviewed, fully open-access venue for theoretical, computational, and empirical work at the intersection of cognition, learning systems, and neural computation.',
        'currentVolume' => optional(optional($articles->first())->edition)->volume->number ?: 18,
        'currentIssue' => optional($articles->first()->edition ?? null)->issue ?: 2,
        'currentDate' => now()->format('M Y'),
        'editorChief' => [
            'name' => optional($journal->owner)->name ?: 'Helena Vasquez',
            'aff' => 'Nexara Research Network',
            'init' => 'HV',
        ],
        'editors' => [
            ['name' => 'Marek Toth', 'role' => 'Deputy Editor', 'aff' => 'ETH Zurich', 'init' => 'MT'],
            ['name' => 'Ayanna Okafor', 'role' => 'Methods Editor', 'aff' => 'UCL', 'init' => 'AO'],
            ['name' => 'Rohan Iyer', 'role' => 'Statistics Editor', 'aff' => 'Stanford University', 'init' => 'RI'],
            ['name' => 'Sofia Castellanos', 'role' => 'Reviews Editor', 'aff' => 'Max Planck Inst.', 'init' => 'SC'],
        ],
    ];

    $articlePayload = $articles->take(12)->values()->map(function ($article, $index) {
        $authorName = optional($article->author)->name ?: 'Unknown Author';
        $initials = collect(explode(' ', $authorName))
            ->filter()
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->take(2)
            ->join('');

        $publishedDate = $article->submitted_at ?: now()->subDays(($index + 1) * 10);
        $receivedDate = (clone $publishedDate)->subDays(35 + $index);
        $volume = optional(optional($article->edition)->volume)->number ?: 18;
        $issue = optional($article->edition)->issue ?: 2;

        return [
            'id' => (string) $article->id,
            'type' => \Illuminate\Support\Str::headline(str_replace('_', ' ', $article->article_type ?? 'Research Article')),
            'oa' => true,
            'subject' => 'Computational Cognition',
            'title' => $article->title,
            'authors' => [
                ['name' => $authorName, 'corresp' => true, 'aff' => 1],
            ],
            'affiliations' => ['Nexara Journal Network'],
            'abstract' => $article->abstract ?: 'This article preview is shown using placeholder text because the complete abstract is not currently available in the system.',
            'doi' => '10.31472/nexara.'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
            'pages' => (10 + ($index * 6)).'-'.(15 + ($index * 6)),
            'volume' => (int) $volume,
            'issue' => (int) $issue,
            'year' => (int) $publishedDate->format('Y'),
            'publishedOn' => $publishedDate->format('M j, Y'),
            'receivedOn' => $receivedDate->format('M j, Y'),
            'citations' => 5 + ($index * 3),
            'downloads' => number_format(900 + ($index * 420)),
            'altmetric' => 40 + ($index * 17),
            'altmetricBreakdown' => ['news' => 2, 'twitter' => 18, 'blogs' => 3, 'policy' => 0],
            'keywords' => ['journal', 'research', 'open access'],
            'initials' => $initials ?: 'NA',
        ];
    });

    if ($articlePayload->isEmpty()) {
        $articlePayload = collect([
            [
                'id' => 'sample-1',
                'type' => 'Research Article',
                'oa' => true,
                'subject' => 'Computational Cognition',
                'title' => 'Sample article title for journal UI preview',
                'authors' => [['name' => 'Nexara Editorial Team', 'corresp' => true, 'aff' => 1]],
                'affiliations' => ['Nexara Journal Network'],
                'abstract' => 'Dummy article content is shown because there are no published submissions yet.',
                'doi' => '10.31472/nexara.0001',
                'pages' => '1-12',
                'volume' => 18,
                'issue' => 2,
                'year' => (int) now()->format('Y'),
                'publishedOn' => now()->format('M j, Y'),
                'receivedOn' => now()->subDays(40)->format('M j, Y'),
                'citations' => 3,
                'downloads' => '1,204',
                'altmetric' => 51,
                'altmetricBreakdown' => ['news' => 2, 'twitter' => 20, 'blogs' => 5, 'policy' => 0],
                'keywords' => ['journal', 'research', 'open access'],
            ],
        ]);
    }

    $issuesPayload = collect(range(0, 5))->map(function ($i) use ($articlePayload) {
        $first = $articlePayload->first();
        return [
            'v' => $i === 0 ? $first['volume'] : max(1, $first['volume'] - intdiv($i, 2)),
            'i' => $i === 0 ? $first['issue'] : ($i % 2 === 0 ? 2 : 1),
            'year' => (int) now()->subMonths($i * 3)->format('Y'),
            'month' => now()->subMonths($i * 3)->format('M'),
            'topic' => $i === 0 ? 'Current journal issue' : 'Archive issue',
            'current' => $i === 0,
            'articles' => $i === 0 ? max(1, $articlePayload->count()) : 10 + $i,
        ];
    })->values();

    $subjectsPayload = [
        'Reinforcement Learning',
        'Neural Computation',
        'Memory and Recall',
        'Decision Making',
        'Bayesian Cognition',
        'Language and Symbols',
        'Computational Psychiatry',
        'Reviews and Tutorials',
    ];
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $journal->name }} - {{ platform_name() }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,300;8..60,400;8..60,500;8..60,600;8..60,700&family=JetBrains+Mono:wght@400;500;600&family=Spectral:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600;6..72,700&display=swap" rel="stylesheet" />
    @viteReactRefresh
    @vite(['resources/css/journal-home.css', 'resources/js/journal-home.jsx'])
</head>
<body style="background-color: rgb(255, 255, 255)">
    <div id="root"></div>

    <script>
        window.JOURNAL = @json($journalPayload);
        window.ARTICLES = @json($articlePayload->values());
        window.ISSUES = @json($issuesPayload);
        window.SUBJECTS = @json($subjectsPayload);
    </script>
</body>
</html>
