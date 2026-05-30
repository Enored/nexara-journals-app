<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
use App\Support\BlogPayload;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /** @var array<string, array{bio: string, affiliation: string}> */
    private const AUTHOR_PROFILES = [
        'Helena Vásquez' => [
            'affiliation' => 'Editor-in-Chief, JCC',
            'bio' => 'Editor-in-Chief of the Journal of Cognitive Computation. Writes on open access, editorial policy, and the economics of diamond publishing.',
        ],
        'Marek Tóth' => [
            'affiliation' => 'Deputy Editor',
            'bio' => 'Deputy Editor overseeing triage and reviewer assignment across Nexara journals.',
        ],
        'Rohan Iyer' => [
            'affiliation' => 'Statistics Editor',
            'bio' => 'Statistics Editor; leads reproducibility audits and submission-time model checks.',
        ],
        'Ayanna Okafor' => [
            'affiliation' => 'Methods Editor',
            'bio' => 'Methods Editor and programme lead for the Early-Career Reviewer cohort.',
        ],
        'Sofía Castellanos' => [
            'affiliation' => 'Reviews Editor',
            'bio' => 'Reviews Editor; writes explainers on metrics, attention, and responsible use of altmetrics.',
        ],
    ];

    public function run(): void
    {
        foreach (BlogPayload::demoPosts() as $post) {
            $author = $this->resolveAuthor($post['author']);
            $content = collect($post['content'])
                ->map(fn (string $paragraph) => '<p>'.e($paragraph).'</p>')
                ->implode("\n");

            Blog::query()->updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'title' => $post['title'],
                    'category' => $post['category'],
                    'excerpt' => $post['excerpt'],
                    'content' => $content,
                    'tags' => $post['tags'],
                    'read_time' => BlogPayload::readTimeFor($content),
                    'is_published' => true,
                    'published_at' => Carbon::parse($post['published']),
                    'author_id' => $author->id,
                ],
            );
        }
    }

    private function resolveAuthor(string $name): User
    {
        $email = Str::slug($name).'@nexara.example';
        $profile = self::AUTHOR_PROFILES[$name] ?? [];

        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'first_name' => Str::before($name, ' '),
                'last_name' => Str::after($name, ' '),
                'name' => $name,
                'password' => 'password',
            ],
        );

        if ($profile !== []) {
            $user->update([
                'bio' => $profile['bio'],
                'affiliation' => $profile['affiliation'],
            ]);
        }

        return $user;
    }
}
