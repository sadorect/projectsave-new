<?php

namespace App\Support\Navigation;

use App\Models\User;

class NavigationBuilder
{
    public function build(string $surface, ?User $user): array
    {
        return match ($surface) {
            'admin'   => $this->buildAdmin($user),
            'content' => $this->buildContent($user),
            'lms'     => $this->buildLms($user),
            'public'  => $this->buildPublic($user),
            default   => [],
        };
    }

    // ---------------------------------------------------------------
    // Public navigation (header)
    // ---------------------------------------------------------------
    private function buildPublic(?User $user): array
    {
        $items = [
            $this->item('home', 'Home', null, route('home'), request()->routeIs('home')),
            $this->item('about', 'About', null, route('about'), request()->routeIs('about')),
            $this->item('asom', 'ASOM', null, route('asom'), request()->routeIs('asom')),
            $this->item('devotional', 'Devotional', null, route('blog.index'), request()->routeIs('blog*', 'posts*')),
            $this->item('events', 'Events', null, route('events.index'), request()->routeIs('events*')),
            $this->item('contact', 'Contact', null, route('contact.show'), request()->routeIs('contact*')),
            $this->item('faqs', 'FAQs', null, route('faqs.list'), request()->routeIs('faqs*')),
            $this->item('volunteer', 'Get Involved', null, route('volunteer.prayer-force'), request()->routeIs('volunteer*', 'partners*')),
        ];

        return [['label' => null, 'items' => $items]];
    }

    // ---------------------------------------------------------------
    // Admin navigation (back-office sidebar)
    // ---------------------------------------------------------------
    private function buildAdmin(?User $user): array
    {
        $sections = [];

        // ── Dashboard ────────────────────────────────────────────────
        $sections[] = [
            'label' => null,
            'items' => [
                $this->item('dashboard', 'Dashboard', 'bi bi-speedometer2', route('admin.dashboard'), request()->routeIs('admin.dashboard')),
            ],
        ];

        // ── People ───────────────────────────────────────────────────
        $peopleItems = [];

        if ($user?->can('manage-users') || $user?->can('view-users')) {
            $peopleItems[] = $this->item('users', 'Users', 'bi bi-people', route('admin.users.index'), request()->routeIs('admin.users*'));
        }
        if ($user?->can('manage-users')) {
            $peopleItems[] = $this->item('deletion-requests', 'Deletion Requests', 'bi bi-trash3', route('admin.deletion-requests.index'), request()->routeIs('admin.deletion-requests*'));
        }
        if ($user?->can('manage-partners')) {
            $peopleItems[] = $this->item('partners', 'Partners', 'bi bi-handshake', route('admin.partners.index'), request()->routeIs('admin.partners*'));
        }
        if ($user?->can('manage-prayer-force')) {
            $peopleItems[] = $this->item('prayer-force', 'Prayer Force', 'bi bi-hand-thumbs-up', route('admin.prayer-force.index'), request()->routeIs('admin.prayer-force*'));
        }

        if (! empty($peopleItems)) {
            $sections[] = ['label' => 'People', 'items' => $peopleItems];
        }

        // ── Content ──────────────────────────────────────────────────
        if ($user?->can('access-content-admin') || $user?->can('view-posts') || $user?->can('view-events')) {
            $contentOpen = request()->routeIs(
                'admin.posts*', 'admin.events*', 'admin.categories*',
                'admin.tags*', 'admin.faqs*', 'admin.forms*', 'admin.submissions*'
            );

            $contentChildren = [];
            if ($user?->can('view-posts') || $user?->can('access-content-admin')) {
                $contentChildren[] = $this->item('posts', 'Posts', 'bi bi-file-text', route('admin.posts.index'), request()->routeIs('admin.posts*'));
            }
            if ($user?->can('view-events') || $user?->can('access-content-admin')) {
                $contentChildren[] = $this->item('events', 'Events', 'bi bi-calendar-event', route('admin.events.index'), request()->routeIs('admin.events*'));
            }
            if ($user?->can('manage-post-taxonomy') || $user?->can('access-content-admin')) {
                $contentChildren[] = $this->item('categories', 'Categories', 'bi bi-tag', route('admin.categories.index'), request()->routeIs('admin.categories*'));
                $contentChildren[] = $this->item('tags', 'Tags', 'bi bi-tags', route('admin.tags.index'), request()->routeIs('admin.tags*'));
            }
            if ($user?->can('manage-forms') || $user?->can('access-content-admin')) {
                $contentChildren[] = $this->item('forms', 'Forms', 'bi bi-ui-checks', route('admin.forms.index'), request()->routeIs('admin.forms*', 'admin.submissions*'));
            }

            if (! empty($contentChildren)) {
                $sections[] = [
                    'label' => 'Content',
                    'items' => [
                        $this->item('content-group', 'Content', 'bi bi-newspaper', null, $contentOpen, null, $contentOpen, $contentChildren),
                    ],
                ];
            }
        }

        // ── Learning (LMS) ───────────────────────────────────────────
        if ($user?->can('access-lms-admin')) {
            $lmsOpen = request()->routeIs(
                'admin.courses*', 'admin.lessons*', 'admin.exams*',
                'admin.enrollments*', 'admin.certificates*', 'admin.exam-attempts*',
                'admin.asom-page*'
            );

            $lmsChildren = [];
            if ($user?->can('manage-courses') || $user?->can('access-lms-admin')) {
                $lmsChildren[] = $this->item('courses', 'Courses', 'bi bi-book', route('admin.courses.index'), request()->routeIs('admin.courses*'));
            }
            if ($user?->can('manage-lessons') || $user?->can('access-lms-admin')) {
                $lmsChildren[] = $this->item('lessons', 'Lessons', 'bi bi-play-circle', route('admin.lessons.index'), request()->routeIs('admin.lessons*'));
            }
            if ($user?->can('manage-exams') || $user?->can('access-lms-admin')) {
                $lmsChildren[] = $this->item('exams', 'Exams', 'bi bi-pencil-square', route('admin.exams.index'), request()->routeIs('admin.exams*', 'admin.exam-attempts*'));
            }
            if ($user?->can('manage-enrollments') || $user?->can('access-lms-admin')) {
                $lmsChildren[] = $this->item('enrollments', 'Enrollments', 'bi bi-person-check', route('admin.enrollments.index'), request()->routeIs('admin.enrollments*'));
            }
            if ($user?->can('manage-certificates') || $user?->can('access-lms-admin')) {
                $lmsChildren[] = $this->item('certificates', 'Certificates', 'bi bi-award', route('admin.certificates.index'), request()->routeIs('admin.certificates*'));
            }

            if (! empty($lmsChildren)) {
                $sections[] = [
                    'label' => 'Learning',
                    'items' => [
                        $this->item('lms-group', 'LMS', 'bi bi-mortarboard', null, $lmsOpen, null, $lmsOpen, $lmsChildren),
                    ],
                ];
            }
        }

        // ── Celebrations ─────────────────────────────────────────────
        if ($user?->can('view-reports')) {
            $celebrationsOpen = request()->routeIs('admin.celebrations*', 'admin.dashboard.celebrants*');
            $sections[] = [
                'label' => 'Celebrations',
                'items' => [
                    $this->item('celebrations', 'Celebrations', 'bi bi-balloon', null, $celebrationsOpen, null, $celebrationsOpen, [
                        $this->item('celebrants', 'Celebrants', 'bi bi-people', route('admin.dashboard.celebrants'), request()->routeIs('admin.dashboard.celebrants*')),
                        $this->item('celebrations-logs', 'Wish Logs', 'bi bi-journal', route('admin.celebrations.logs'), request()->routeIs('admin.celebrations.logs*')),
                        $this->item('celebrations-stats', 'Statistics', 'bi bi-bar-chart', route('admin.celebrations.statistics'), request()->routeIs('admin.celebrations.statistics*')),
                    ]),
                ],
            ];
        }

        // ── Communications ───────────────────────────────────────────
        if ($user?->can('manage-mail') || $user?->can('manage-mail-templates')) {
            $mailOpen = request()->routeIs('admin.mail*', 'admin.mail-templates*');
            $mailChildren = [];

            if ($user?->can('manage-mail')) {
                $mailChildren[] = $this->item('mail-compose', 'Compose', 'bi bi-pencil', route('admin.mail.compose'), request()->routeIs('admin.mail.compose'));
            }
            if ($user?->can('manage-mail-templates')) {
                $mailChildren[] = $this->item('mail-templates', 'Templates', 'bi bi-file-earmark-text', route('admin.mail-templates.index'), request()->routeIs('admin.mail-templates*'));
            }
            if ($user?->can('manage-notification-settings')) {
                $mailChildren[] = $this->item('notification-settings', 'Notification Settings', 'bi bi-bell', route('admin.notification-settings.edit'), request()->routeIs('admin.notification-settings*'));
            }

            if (! empty($mailChildren)) {
                $sections[] = [
                    'label' => 'Communications',
                    'items' => [
                        $this->item('mail-group', 'Mail', 'bi bi-envelope', null, $mailOpen, null, $mailOpen, $mailChildren),
                    ],
                ];
            }
        }

        // ── System ───────────────────────────────────────────────────
        $systemItems = [];

        if ($user?->can('view-roles') || $user?->can('manage-roles') || $user?->can('manage-user-roles')) {
            $systemItems[] = $this->item('roles', 'Roles', 'bi bi-shield', route('admin.roles.index'), request()->routeIs('admin.roles*'));
            $systemItems[] = $this->item('permissions', 'Permissions', 'bi bi-lock', route('admin.permissions.index'), request()->routeIs('admin.permissions*'));
        }
        if ($user?->can('view-audit-log') || $user?->can('manage-audit-log')) {
            $systemItems[] = $this->item('audit-log', 'Audit Log', 'bi bi-journal-text', route('admin.audit.index'), request()->routeIs('admin.audit*'));
        }
        if ($user?->can('manage-user-sessions')) {
            $systemItems[] = $this->item('sessions', 'Sessions', 'bi bi-window', route('admin.sessions.index'), request()->routeIs('admin.sessions*'));
        }
        if ($user?->can('manage-files')) {
            $systemItems[] = $this->item('admin-files', 'File Manager', 'bi bi-folder', route('admin.files.index'), request()->routeIs('admin.files*'));
        }

        if (! empty($systemItems)) {
            $sections[] = ['label' => 'System', 'items' => $systemItems];
        }

        return $sections;
    }

    // ---------------------------------------------------------------
    // Content management navigation
    // ---------------------------------------------------------------
    private function buildContent(?User $user): array
    {
        $items = [
            $this->item('posts', 'Posts', 'bi bi-file-text', route('admin.posts.index'), request()->routeIs('admin.posts*')),
            $this->item('events', 'Events', 'bi bi-calendar-event', route('admin.events.index'), request()->routeIs('admin.events*')),
            $this->item('categories', 'Categories', 'bi bi-tag', route('admin.categories.index'), request()->routeIs('admin.categories*')),
            $this->item('tags', 'Tags', 'bi bi-tags', route('admin.tags.index'), request()->routeIs('admin.tags*')),
            $this->item('forms', 'Forms', 'bi bi-ui-checks', route('admin.forms.index'), request()->routeIs('admin.forms*')),
        ];

        return [['label' => 'Content', 'items' => $items]];
    }

    // ---------------------------------------------------------------
    // LMS student navigation
    // ---------------------------------------------------------------
    private function buildLms(?User $user): array
    {
        $items = [
            $this->item('lms-dashboard', 'Dashboard', 'bi bi-speedometer2', route('lms.dashboard'), request()->routeIs('lms.dashboard')),
            $this->item('lms-courses', 'Courses', 'bi bi-book', route('lms.courses.index'), request()->routeIs('lms.courses*')),
            $this->item('lms-exams', 'Exams', 'bi bi-pencil-square', route('lms.exams.index'), request()->routeIs('lms.exams*')),
            $this->item('lms-certificates', 'My Certificates', 'bi bi-award', route('lms.certificates.index'), request()->routeIs('lms.certificates*')),
        ];

        return [['label' => null, 'items' => $items]];
    }

    // ---------------------------------------------------------------
    // Helper
    // ---------------------------------------------------------------
    private function item(
        string $id,
        string $label,
        ?string $icon,
        ?string $url,
        bool $active,
        mixed $badge = null,
        bool $open = false,
        array $children = []
    ): array {
        return [
            'id'       => $id,
            'label'    => $label,
            'icon'     => $icon,
            'url'      => $url,
            'active'   => $active,
            'badge'    => $badge,
            'open'     => $open,
            'children' => $children,
        ];
    }
}
