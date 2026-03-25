<?php

namespace App\Support\Lms;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AsomPageSettings
{
    private const SETTINGS_KEY = 'asom_page_settings';
    private const CACHE_KEY    = 'asom_page_settings_content';
    private const CACHE_TTL    = 3600; // 1 hour

    /**
     * Retrieve current page content, merging DB-stored overrides with defaults.
     */
    public static function current(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $stored = DB::table('app_settings')
                ->where('key', self::SETTINGS_KEY)
                ->value('value');

            $override = $stored ? (json_decode($stored, true) ?? []) : [];

            return array_replace_recursive(self::defaults(), $override);
        });
    }

    /**
     * Persist updated page content to the database and flush the cache.
     */
    public static function save(array $data): void
    {
        DB::table('app_settings')->updateOrInsert(
            ['key' => self::SETTINGS_KEY],
            ['value' => json_encode($data), 'updated_at' => now(), 'created_at' => now()]
        );

        Cache::forget(self::CACHE_KEY);
    }

    // ---------------------------------------------------------------
    // Default content (used when nothing is stored in the database)
    // ---------------------------------------------------------------
    public static function defaults(): array
    {
        $heroBase = [
            'eyebrow'                    => 'ASOM School of Ministry',
            'title'                      => 'Grow in knowledge. Grow in ministry.',
            'lead'                       => 'Equipping believers with the Word of God for faithful, effective service.',
            'body'                       => 'A structured, Scripture-centred programme designed to shape ministry leaders.',
            'image_url'                  => '',
            'welcome_title'              => 'Welcome to ASOM',
            'welcome_copy'               => 'Enrol in courses, complete exams, and earn your Diploma in Ministry.',
            'primary_cta_label'          => 'Start learning',
            'primary_cta_url'            => '/login',
            'secondary_cta_label'        => 'Browse courses',
            'secondary_cta_url'          => '/learn',
            'authenticated_primary_label' => 'Go to dashboard',
            'authenticated_primary_url'   => '/learn/dashboard',
            'visual_label'               => 'Available courses',
            'identity_eyebrow'           => 'ASOM',
            'identity_copy'              => 'A ministry school for every believer.',
            'featured_label'             => 'Featured courses',
            'featured_lessons_label'     => 'lessons',
            'featured_exams_label'       => 'exams',
            'stats' => [
                ['label' => 'Published courses'],
                ['label' => 'Total lessons'],
                ['label' => 'Active exams'],
            ],
            'pathways' => [],
        ];

        return [
            // ── Landing page hero ──────────────────────────────────────
            'landing_hero' => $heroBase,

            // ── Catalog hero (courses index) ───────────────────────────
            'catalog_hero' => array_merge($heroBase, [
                'eyebrow'      => 'Course Catalogue',
                'title'        => 'Explore the ASOM curriculum',
                'lead'         => 'Choose from a range of ministry-focused courses.',
                'body'         => 'Complete all six diploma courses to earn your Diploma in Ministry.',
                'welcome_title' => 'The full catalogue',
                'welcome_copy' => 'Every published course is listed below.',
                'visual_label' => 'Learning pathways',
                'pathways'     => [
                    ['title' => 'New students',    'copy' => 'Start with Bible Introduction'],
                    ['title' => 'Continuing',      'copy' => 'Pick up where you left off'],
                ],
            ]),

            // ── Bridge section ─────────────────────────────────────────
            'bridge_section' => [
                'eyebrow' => 'Ministry made accessible',
                'title'   => 'Practical training for every believer',
                'copy'    => 'Whether you are just starting out or looking to deepen your foundations, ASOM provides structured, biblically-grounded training at your own pace.',
            ],
            'bridge_links' => [
                ['title' => 'About the programme', 'copy' => 'Learn what ASOM is about.', 'url' => '/about',   'cta' => 'Read more'],
                ['title' => 'Contact us',           'copy' => 'Get in touch with the team.', 'url' => '/contact', 'cta' => 'Send a message'],
            ],

            // ── Review / testimonials section ─────────────────────────
            'review_section' => [
                'eyebrow' => 'Student experience',
                'title'   => 'Grow in faith and ministry',
            ],
            'review_checks' => [
                ['title' => 'Scripture-rooted teaching',  'copy' => 'Every course is grounded in biblical truth.'],
                ['title' => 'Flexible, self-paced learning', 'copy' => 'Study at whatever pace suits you.'],
                ['title' => 'Ministry-ready outcomes',    'copy' => 'Practical tools for effective service.'],
            ],

            // ── Formation pillars ──────────────────────────────────────
            'formation_pillars' => [
                ['icon' => 'bi bi-book',       'title' => 'Biblical Foundation', 'copy' => 'Every course is rooted in Scripture and theological truth.'],
                ['icon' => 'bi bi-people',     'title' => 'Community',           'copy' => 'Learn alongside fellow believers committed to kingdom service.'],
                ['icon' => 'bi bi-mortarboard','title' => 'Practical Training',  'copy' => 'Equip yourself with tools you can use in ministry today.'],
                ['icon' => 'bi bi-award',      'title' => 'Accreditation',       'copy' => 'Earn a recognised Diploma in Ministry on completion.'],
            ],

            // ── Course preview section ─────────────────────────────────
            'preview_section' => [
                'eyebrow'      => 'Our catalogue',
                'title'        => 'Courses designed for ministry',
                'copy'         => 'A structured curriculum covering the foundations of Christian ministry.',
                'button_label' => 'View all courses',
                'button_url'   => '/learn',
            ],

            // ── Learning journey section ───────────────────────────────
            'learning_section' => [
                'eyebrow' => 'How it works',
                'title'   => 'Your path to ministry',
            ],
            'learning_steps' => [
                'Create an account and enrol in any course for free.',
                'Work through video and reading lessons at your own pace.',
                'Complete all diploma courses and qualifying exams to graduate.',
            ],

            // ── Programme milestones section ───────────────────────────
            'program_section' => [
                'eyebrow' => 'Diploma in Ministry',
                'title'   => 'Earn your qualification',
                'copy'    => 'Complete all six ASOM courses and qualifying exams to receive your Diploma in Ministry.',
            ],
            'program_milestones' => [
                '6 diploma courses covering core ministry topics',
                'Qualifying exam for each course',
                'Diploma in Ministry certificate on completion',
            ],

            // ── Outcomes section ───────────────────────────────────────
            'outcomes_section' => [
                'eyebrow'      => 'What you will gain',
                'title'        => 'Ministry-ready outcomes',
                'copy'         => 'Leave equipped to serve your community and the wider body of Christ.',
                'button_label' => 'Start learning',
                'button_url'   => '/register',
            ],
            'outcomes' => [
                'A firm grounding in biblical truth and sound doctrine',
                'Confidence in preaching, teaching, and communication',
                'Practical pastoral care and counselling tools',
                'Understanding of your spiritual gifts and calling',
            ],

            // ── CTA section ────────────────────────────────────────────
            'cta_section' => [
                'eyebrow'              => 'Ready to begin?',
                'title'                => 'Start your ministry journey today.',
                'copy'                 => 'Enrol in your first course and take the first step toward your Diploma in Ministry.',
                'guest_primary_label'  => 'Enrol now',
                'guest_primary_url'    => '/register',
                'guest_secondary_label' => 'Browse courses',
                'guest_secondary_url'  => '/learn',
                'authenticated_label'  => 'Go to dashboard',
                'authenticated_url'    => '/learn/dashboard',
            ],
        ];
    }
}
