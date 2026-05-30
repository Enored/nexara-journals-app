<?php

namespace App\Support;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
     * Columns required to render list/card views. Deliberately excludes the
     * `content` longText so index, related, and home queries stay light.
     *
     * @var list<string>
     */
    public const LIST_COLUMNS = [
        'id',
        'slug',
        'category',
        'cover_image',
        'title',
        'excerpt',
        'read_time',
        'published_at',
        'author_id',
    ];

    /** @var list<string> */
    public const AUTHOR_COLUMNS = ['id', 'name', 'first_name', 'last_name', 'bio', 'affiliation'];

    /**
     * The most recent published posts, mapped for cards (e.g. home "Recent posts").
     *
     * @return list<array<string, mixed>>
     */
    public static function recent(int $limit = 3): array
    {
        return Blog::query()
            ->where('is_published', true)
            ->with(['author' => fn ($q) => $q->select(self::AUTHOR_COLUMNS)])
            ->select(self::LIST_COLUMNS)
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get()
            ->map(fn (Blog $blog) => self::toListItem($blog))
            ->all();
    }

    /**
     * A single published post (with content) for the detail page.
     *
     * @return array<string, mixed>|null
     */
    public static function findPublishedBySlug(string $slug): ?array
    {
        $blog = Blog::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->with(['author' => fn ($q) => $q->select(self::AUTHOR_COLUMNS)])
            ->first();

        return $blog ? self::toFullPost($blog) : null;
    }

    /**
     * Published-post counts per category in a single grouped query.
     *
     * @return array<string, int>
     */
    public static function categoryCounts(): array
    {
        $grouped = Blog::query()
            ->where('is_published', true)
            ->whereNotNull('category')
            ->groupBy('category')
            ->select('category', DB::raw('count(*) as aggregate'))
            ->pluck('aggregate', 'category');

        $counts = ['All' => (int) $grouped->sum()];
        foreach (self::CATEGORIES as $category) {
            if ($category === 'All') {
                continue;
            }
            $counts[$category] = (int) ($grouped[$category] ?? 0);
        }

        return $counts;
    }

    /**
     * Estimated reading time in minutes from rich-text content (~200 wpm).
     */
    public static function readTimeFor(?string $content): int
    {
        $words = str_word_count(strip_tags((string) $content));

        return max(1, (int) ceil($words / 200));
    }

    /**
     * Formatted updated date for the article byline, or null when not shown.
     */
    public static function displayUpdatedDate(Blog $blog): ?string
    {
        $published = $blog->published_at;
        $updated = $blog->updated_at;

        if (! $published || ! $updated || ! $updated->isAfter($published)) {
            return null;
        }

        if ($published->isSameDay($updated)) {
            return null;
        }

        return $updated->format('j M Y');
    }

    /**
     * Light mapping for list/card views (no body content).
     *
     * @return array<string, mixed>
     */
    public static function authorInitials(?User $user): string
    {
        if (! $user) {
            return 'ED';
        }

        if (filled($user->first_name)) {
            $parts = array_filter([$user->first_name, $user->last_name]);

            return strtoupper(collect($parts)->map(fn (string $part) => mb_substr($part, 0, 1))->implode(''));
        }

        $words = preg_split('/\s+/', trim((string) $user->name)) ?: [];

        return strtoupper(collect($words)->take(2)->map(fn (string $word) => mb_substr($word, 0, 1))->implode(''));
    }

    /**
     * @return array{authorInitials: string, authorAffiliation: string|null, authorBio: string|null}
     */
    public static function authorFields(Blog $blog): array
    {
        $author = $blog->author;
        $affiliation = trim((string) ($author?->affiliation ?? ''));
        $bio = trim((string) ($author?->bio ?? ''));

        return [
            'authorInitials' => self::authorInitials($author),
            'authorAffiliation' => $affiliation !== '' ? $affiliation : null,
            'authorBio' => $bio !== '' ? $bio : null,
        ];
    }

    public static function toListItem(Blog $blog): array
    {
        return [
            'id' => $blog->id,
            'slug' => $blog->slug,
            'url' => platform_route('blogs.show', ['slug' => $blog->slug]),
            'category' => $blog->category ?: 'Editorial',
            'cover' => $blog->cover_image ?: null,
            'title' => $blog->title,
            'excerpt' => $blog->excerpt ?: '',
            'author' => $blog->author?->name ?? 'Editorial team',
            ...self::authorFields($blog),
            'readTime' => (int) ($blog->read_time ?: self::readTimeFor($blog->excerpt)),
            'date' => $blog->published_at?->format('j M Y') ?? '',
            'published' => $blog->published_at?->toDateString() ?? '',
        ];
    }

    /**
     * Full mapping for the detail page (parsed body + tags).
     *
     * @return array<string, mixed>
     */
    public static function toFullPost(Blog $blog): array
    {
        $plain = strip_tags((string) $blog->content);
        $paragraphs = $plain !== ''
            ? array_values(array_filter(array_map('trim', preg_split('/\n\s*\n/', $plain) ?: [])))
            : ['Full post coming soon.'];

        return [
            ...self::toListItem($blog),
            'excerpt' => $blog->excerpt ?: Str::limit($plain, 160),
            'coverCaption' => $blog->cover_caption ?: null,
            'updated' => self::displayUpdatedDate($blog),
            'readTime' => (int) ($blog->read_time ?: self::readTimeFor($blog->content)),
            'content' => $paragraphs,
            'tags' => $blog->tags ?? [],
        ];
    }

    /**
     * Sample content for local development. Seeded via BlogSeeder, not served at runtime.
     *
     * @return list<array<string, mixed>>
     */
    public static function demoPosts(): array
    {
        return [
            [
                'slug' => 'diamond-open-access-eighteen-years',
                'category' => 'Editorial',
                'title' => 'Why we stayed diamond open access for eighteen years — and what it costs',
                'excerpt' => 'No author fees, no paywalls, no surprise. A frank accounting of how the model is funded, where the money goes, and the trade-offs we accept to keep it that way.',
                'author' => 'Helena Vásquez',
                'tags' => ['open access', 'funding', 'policy'],
                'published' => '2026-05-27',
                'content' => [
                    'When Nexara was founded in 2003, the open-access debate was still framed as a choice between two paywalls: one the reader pays, one the author pays. We rejected both. Eighteen years later, every journal we publish remains diamond open access — free to read and free to publish in.',
                    'This piece is not a victory lap. Diamond OA is expensive, and the costs are real even when they are invisible to authors and readers.',
                    'Our funding model combines library memberships, funder grants, and in-kind contributions from host institutions. None of it passes through APCs, and we publish our annual accounts so members can see where every pound goes.',
                    'The trade-off is deliberate: we cap submission volume, we invest heavily in copy-editing and production, and we say no to journals that cannot be sustained on membership income alone. That is the price of staying diamond.',
                ],
            ],
            [
                'slug' => 'thirty-one-day-median-decision',
                'category' => 'Peer review',
                'title' => 'A 31-day median decision is not an accident. Here\'s the machinery behind it.',
                'excerpt' => 'Triage desks, a standing reviewer pool, and a hard rule about second-round requests. What actually makes fast, fair review possible at scale.',
                'author' => 'Marek Tóth',
                'tags' => ['peer review', 'editorial process'],
                'published' => '2026-05-21',
                'content' => [
                    'A thirty-one-day median decision sounds fast because most journals are slow. It is three unglamorous pieces of machinery that we maintain deliberately and at cost.',
                    'First, a triage desk. Every submission is read by a handling editor within seventy-two hours.',
                ],
            ],
            [
                'slug' => 'identifiability-checks-2025',
                'category' => 'Meta-science',
                'title' => 'We re-ran the identifiability checks on every model we published in 2025',
                'excerpt' => 'Of 214 fitted models, 38 failed at least one standard check. We publish the full audit, name the failure modes, and propose a submission-time gate.',
                'author' => 'Rohan Iyer',
                'tags' => ['reproducibility', 'model fitting', 'statistics'],
                'published' => '2026-05-14',
                'content' => [
                    'Identifiability is the unglamorous foundation of model-based cognitive science. Last year we decided to find out how often our own published models actually passed standard identifiability checks.',
                ],
            ],
            [
                'slug' => 'early-career-reviewer-cohort-2026',
                'category' => 'Announcement',
                'title' => 'Announcing the 2026 Early-Career Reviewer cohort',
                'excerpt' => 'Sixty-two reviewers from thirty-one countries join our mentored review programme this year. Applications for 2027 open in September.',
                'author' => 'Ayanna Okafor',
                'tags' => ['reviewers', 'early career', 'announcement'],
                'published' => '2026-05-06',
                'content' => [
                    'Sixty-two reviewers from thirty-one countries join our mentored Early-Career Reviewer programme this year, our largest cohort yet.',
                ],
            ],
            [
                'slug' => 'altmetric-attention-score-explainer',
                'category' => 'Explainer',
                'title' => 'What an altmetric attention score does — and does not — tell you',
                'excerpt' => 'A donut is not an impact factor. A short, honest guide to reading attention metrics without overclaiming.',
                'author' => 'Sofía Castellanos',
                'tags' => ['metrics', 'altmetrics', 'explainer'],
                'published' => '2026-04-29',
                'content' => [
                    'An altmetric attention score is a weighted count of online mentions. It is genuinely useful for one thing: telling you that people, somewhere, are talking about a paper right now.',
                ],
            ],
            [
                'slug' => 'reproducibility-editors-interview',
                'category' => 'Interview',
                'title' => 'Inside the methods desk: a conversation with our reproducibility editors',
                'excerpt' => 'Two of the editors who vet code and data before publication on what they look for, what trips authors up, and why a good README is worth a thousand emails.',
                'author' => 'Ayanna Okafor',
                'tags' => ['reproducibility', 'interview', 'open data'],
                'published' => '2026-04-22',
                'content' => [
                    'Every empirical submission to a Nexara journal passes a methods desk before it reaches reviewers.',
                ],
            ],
        ];
    }
}
