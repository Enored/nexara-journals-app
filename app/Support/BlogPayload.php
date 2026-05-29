<?php

namespace App\Support;

use App\Models\Blog;
use Illuminate\Support\Str;

class BlogPayload
{
    /** @var list<string> */
    public const CATEGORIES = [
        'All',
        'Editorial',
        'Peer review',
        'Meta-science',
        'Explainer',
        'Announcement',
        'Interview',
        'Behind the cover',
        'Community',
    ];

    /**
     * @return list<array<string, mixed>>
     */
    public static function forPublic(): array
    {
        $fromDb = Blog::query()
            ->where('is_published', true)
            ->with('author:id,name')
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (Blog $blog) => self::fromModel($blog));

        $posts = $fromDb->isNotEmpty()
            ? $fromDb->values()->all()
            : self::demoPosts();

        return self::withUrls($posts);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findBySlug(string $slug): ?array
    {
        foreach (self::forPublic() as $post) {
            if (($post['slug'] ?? '') === $slug) {
                return $post;
            }
        }

        return null;
    }

    /**
     * @param  list<array<string, mixed>>  $posts
     * @return list<array<string, mixed>>
     */
    private static function withUrls(array $posts): array
    {
        return array_map(function (array $post): array {
            $post['url'] = platform_route('blogs.show', ['slug' => $post['slug']]);

            return $post;
        }, $posts);
    }

    /**
     * @return array<string, mixed>
     */
    public static function fromModel(Blog $blog): array
    {
        $plain = strip_tags((string) $blog->content);
        $paragraphs = $plain !== ''
            ? array_values(array_filter(preg_split('/\n\s*\n/', $plain) ?: []))
            : ['Full post coming soon.'];

        return [
            'id' => $blog->id,
            'slug' => $blog->slug,
            'category' => 'News',
            'title' => $blog->title,
            'excerpt' => $blog->excerpt ?: Str::limit($plain, 160),
            'author' => $blog->author?->name ?? 'Editorial team',
            'role' => platform_name(),
            'readTime' => max(3, (int) ceil(str_word_count($plain) / 200)),
            'date' => $blog->published_at?->format('j M Y') ?? '',
            'published' => $blog->published_at?->toDateString() ?? '',
            'content' => $paragraphs,
            'tags' => [],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function demoPosts(): array
    {
        return [
            [
                'id' => 'demo-p1',
                'slug' => 'diamond-open-access-eighteen-years',
                'category' => 'Editorial',
                'title' => 'Why we stayed diamond open access for eighteen years — and what it costs',
                'excerpt' => 'No author fees, no paywalls, no surprise. A frank accounting of how the model is funded, where the money goes, and the trade-offs we accept to keep it that way.',
                'author' => 'Helena Vásquez',
                'role' => 'Editor-in-Chief, JCC',
                'readTime' => 9,
                'date' => '27 May 2026',
                'published' => '2026-05-27',
                'tags' => ['open access', 'funding', 'policy'],
                'content' => [
                    'When Nexara was founded in 2003, the open-access debate was still framed as a choice between two paywalls: one the reader pays, one the author pays. We rejected both. Eighteen years later, every journal we publish remains diamond open access — free to read and free to publish in.',
                    'This piece is not a victory lap. Diamond OA is expensive, and the costs are real even when they are invisible to authors and readers.',
                    'Our funding model combines library memberships, funder grants, and in-kind contributions from host institutions. None of it passes through APCs, and we publish our annual accounts so members can see where every pound goes.',
                    'The trade-off is deliberate: we cap submission volume, we invest heavily in copy-editing and production, and we say no to journals that cannot be sustained on membership income alone. That is the price of staying diamond.',
                ],
            ],
            [
                'id' => 'demo-p2',
                'slug' => 'thirty-one-day-median-decision',
                'category' => 'Peer review',
                'title' => 'A 31-day median decision is not an accident. Here\'s the machinery behind it.',
                'excerpt' => 'Triage desks, a standing reviewer pool, and a hard rule about second-round requests. What actually makes fast, fair review possible at scale.',
                'author' => 'Marek Tóth',
                'role' => 'Deputy Editor',
                'readTime' => 6,
                'date' => '21 May 2026',
                'published' => '2026-05-21',
                'tags' => ['peer review', 'editorial process'],
                'content' => [
                    'A thirty-one-day median decision sounds fast because most journals are slow. It is three unglamorous pieces of machinery that we maintain deliberately and at cost.',
                    'First, a triage desk. Every submission is read by a handling editor within seventy-two hours.',
                ],
            ],
            [
                'id' => 'demo-p3',
                'slug' => 'identifiability-checks-2025',
                'category' => 'Meta-science',
                'title' => 'We re-ran the identifiability checks on every model we published in 2025',
                'excerpt' => 'Of 214 fitted models, 38 failed at least one standard check. We publish the full audit, name the failure modes, and propose a submission-time gate.',
                'author' => 'Rohan Iyer',
                'role' => 'Statistics Editor',
                'readTime' => 11,
                'date' => '14 May 2026',
                'published' => '2026-05-14',
                'tags' => ['reproducibility', 'model fitting', 'statistics'],
                'content' => [
                    'Identifiability is the unglamorous foundation of model-based cognitive science. Last year we decided to find out how often our own published models actually passed standard identifiability checks.',
                ],
            ],
            [
                'id' => 'demo-p4',
                'slug' => 'early-career-reviewer-cohort-2026',
                'category' => 'Announcement',
                'title' => 'Announcing the 2026 Early-Career Reviewer cohort',
                'excerpt' => 'Sixty-two reviewers from thirty-one countries join our mentored review programme this year. Applications for 2027 open in September.',
                'author' => 'Ayanna Okafor',
                'role' => 'Methods Editor',
                'readTime' => 4,
                'date' => '6 May 2026',
                'published' => '2026-05-06',
                'tags' => ['reviewers', 'early career', 'announcement'],
                'content' => [
                    'Sixty-two reviewers from thirty-one countries join our mentored Early-Career Reviewer programme this year, our largest cohort yet.',
                ],
            ],
            [
                'id' => 'demo-p5',
                'slug' => 'altmetric-attention-score-explainer',
                'category' => 'Explainer',
                'title' => 'What an altmetric attention score does — and does not — tell you',
                'excerpt' => 'A donut is not an impact factor. A short, honest guide to reading attention metrics without overclaiming.',
                'author' => 'Sofía Castellanos',
                'role' => 'Reviews Editor',
                'readTime' => 5,
                'date' => '29 April 2026',
                'published' => '2026-04-29',
                'tags' => ['metrics', 'altmetrics', 'explainer'],
                'content' => [
                    'An altmetric attention score is a weighted count of online mentions. It is genuinely useful for one thing: telling you that people, somewhere, are talking about a paper right now.',
                ],
            ],
            [
                'id' => 'demo-p6',
                'slug' => 'reproducibility-editors-interview',
                'category' => 'Interview',
                'title' => 'Inside the methods desk: a conversation with our reproducibility editors',
                'excerpt' => 'Two of the editors who vet code and data before publication on what they look for, what trips authors up, and why a good README is worth a thousand emails.',
                'author' => 'Ayanna Okafor',
                'role' => 'Methods Editor',
                'readTime' => 8,
                'date' => '22 April 2026',
                'published' => '2026-04-22',
                'tags' => ['reproducibility', 'interview', 'open data'],
                'content' => [
                    'Every empirical submission to a Nexara journal passes a methods desk before it reaches reviewers.',
                ],
            ],
        ];
    }
}
