<?php

namespace App\Support;

use App\Enums\SubmissionStatus;
use App\Models\Journal;
use App\Models\Submission;
use App\Models\User;

final class AboutPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function build(): array
    {
        $press = platform_name();
        $journalCount = Journal::query()->where('is_active', true)->count();
        $articleCount = Submission::query()->where('status', SubmissionStatus::Published)->count();
        $authorCount = Submission::query()->distinct('author_id')->count('author_id');

        return [
            'pageTitle' => 'About — '.$press,
            'about' => [
                'hero' => [
                    'eyebrow' => 'About '.$press,
                    'title' => 'A non-profit publisher for the science of mind, brain & behaviour.',
                    'lead' => 'Founded in 2003, owned by a consortium of research universities, and accountable to the researchers we publish — not to shareholders, advertisers, or article-processing fees.',
                ],
                'mission' => [
                    'heading' => 'Who we are',
                    'standfirst' => 'An editorially independent, diamond open-access publisher — free for everyone to read, and free for everyone to publish in.',
                    'paragraphs' => [
                        $press.' exists for one reason: to make the best work in the cognitive, brain, and behavioural sciences available to anyone who wants to read it, regardless of who they are or where they work. We have published every paper in every journal under an open licence since the day we began.',
                        'We are owned by the universities that read us. Our editorial decisions are made by working scientists with no financial interest in whether a paper is accepted or rejected. The model is unglamorous, slow to build, and — we think — the only one that can survive the next twenty years of scholarly publishing intact.',
                        'We are also small. '.$journalCount.' journals, a dedicated editorial community, two offices. We grow only when a discipline asks us to, and only when we can do the work well.',
                    ],
                ],
                'stats' => [
                    ['v' => (string) max($journalCount, 1), 'l' => 'Peer-reviewed journals'],
                    ['v' => $articleCount > 0 ? number_format($articleCount) : '0', 'l' => 'Articles published'],
                    ['v' => '11.6M', 'l' => 'Downloads · last 12 months'],
                    ['v' => $authorCount > 0 ? number_format($authorCount) : '0', 'l' => 'Contributing authors'],
                    ['v' => '168', 'l' => 'Countries reached'],
                    ['v' => '$0', 'l' => 'Article processing charge'],
                ],
                'leadership' => [
                    ['id' => 'vasquez', 'name' => 'Helena Vásquez', 'role' => 'Editor-in-Chief · President of the Press', 'bio' => 'Computational neuroscientist; previously ETH Zürich and the Allen Institute. Joined Nexara in 2014.', 'initials' => 'HV'],
                    ['id' => 'toth', 'name' => 'Marek Tóth', 'role' => 'Deputy Editor', 'bio' => 'Editorial systems and peer-review reform. Built our triage-desk model and runs the review-a-thon programme.', 'initials' => 'MT'],
                    ['id' => 'okafor', 'name' => 'Ayanna Okafor', 'role' => 'Methods Editor', 'bio' => 'Open-data and code review across the Press. Founder of the Early-Career Reviewer programme.', 'initials' => 'AO'],
                    ['id' => 'iyer', 'name' => 'Rohan Iyer', 'role' => 'Statistics Editor', 'bio' => 'Identifiability, replication, and model-comparison standards. Co-author of our submission-time methods checklist.', 'initials' => 'RI'],
                    ['id' => 'castellanos', 'name' => 'Sofía Castellanos', 'role' => 'Reviews Editor', 'bio' => 'Commissioning editor for review and synthesis articles. Previously at the Max Planck Institute for Human Cognitive and Brain Sciences.', 'initials' => 'SC'],
                    ['id' => 'klein', 'name' => 'Ben Klein', 'role' => 'Director of Operations', 'bio' => 'Runs the consortium relationship, the endowment, and the systems that keep the lights on so the editors don\'t have to.', 'initials' => 'BK'],
                ],
                'timeline' => [
                    ['year' => 2003, 'title' => 'Founded', 'body' => $press.' is established by an initial group of nine universities as a non-profit, fully open-access publisher.'],
                    ['year' => 2004, 'title' => 'First journal launches', 'body' => 'Neural Systems & Circuits publishes its first issue under a CC BY licence — a rarity at the time.'],
                    ['year' => 2008, 'title' => 'Journal of Computational Cognition', 'body' => 'Our flagship journal launches under founding editor Helena Vásquez.'],
                    ['year' => 2014, 'title' => 'Consortium expands to 40 universities', 'body' => 'The funding base broadens, making the diamond-OA model durable beyond any single institution.'],
                    ['year' => 2017, 'title' => 'Open Methods journal', 'body' => 'Open Methods in Cognitive Science begins peer-reviewing software, tools, and reproducibility infrastructure as first-class research outputs.'],
                    ['year' => 2021, 'title' => 'Median decision: 31 days', 'body' => 'A redesigned triage and reviewer-pool system brings median time to first decision below five weeks for the first time.'],
                    ['year' => 2026, 'title' => 'Open Citations initiative', 'body' => 'Nexara joins the Open Citations initiative; the full reference list of every Nexara article is now openly available in bulk.'],
                ],
                'offices' => [
                    ['city' => 'Zürich', 'country' => 'Switzerland', 'line' => 'Editorial headquarters', 'address' => 'Rämistrasse 101, 8092 Zürich', 'phone' => '+41 44 632 11 00'],
                    ['city' => 'Edinburgh', 'country' => 'United Kingdom', 'line' => 'Operations & open science', 'address' => '5 Forrest Hill, Edinburgh EH1 2QN', 'phone' => '+44 131 651 1000'],
                ],
                'contact' => [
                    'heading' => 'Get in touch',
                    'standfirst' => 'Editorial questions go to the journals; everything else comes to us.',
                    'items' => [
                        ['label' => 'General enquiries', 'value' => 'hello@nexarapress.org', 'href' => 'mailto:hello@nexarapress.org'],
                        ['label' => 'Press & media', 'value' => 'press@nexarapress.org', 'href' => 'mailto:press@nexarapress.org'],
                        ['label' => 'Librarians & institutions', 'value' => 'libraries@nexarapress.org', 'href' => 'mailto:libraries@nexarapress.org'],
                        ['label' => 'Bug reports & accessibility', 'value' => 'support@nexarapress.org', 'href' => 'mailto:support@nexarapress.org'],
                    ],
                ],
            ],
        ];
    }
}
