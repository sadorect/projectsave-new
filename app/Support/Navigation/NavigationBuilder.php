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
        if ($this->canAny($user, ['access-admin-dashboard'])) {
            $sections[] = [
                'label' => null,
                'items' => [
                    $this->item('dashboard', 'Dashboard', 'bi bi-speedometer2', route('admin.dashboard'), request()->routeIs('admin.dashboard')),
                ],
            ];
        }

        // ── People ───────────────────────────────────────────────────
        $peopleItems = [];

        if ($this->canAny($user, ['manage-users', 'view-users'])) {
            $peopleItems[] = $this->item('users', 'Users', 'bi bi-people', route('admin.users.index'), request()->routeIs('admin.users*'));
        }
        if ($this->canAny($user, ['manage-users'])) {
            $peopleItems[] = $this->item('deletion-requests', 'Deletion Requests', 'bi bi-trash3', route('admin.deletion-requests.index'), request()->routeIs('admin.deletion-requests*'));
        }
        if ($this->canAny($user, ['manage-partners'])) {
            $peopleItems[] = $this->item('partners', 'Partners', 'bi bi-handshake', route('admin.partners.index'), request()->routeIs('admin.partners*'));
        }
        if ($this->canAny($user, ['manage-prayer-force'])) {
            $peopleItems[] = $this->item('prayer-force', 'Prayer Force', 'bi bi-hand-thumbs-up', route('admin.prayer-force.index'), request()->routeIs('admin.prayer-force*'));
        }

        if (! empty($peopleItems)) {
            $sections[] = ['label' => 'People', 'items' => $peopleItems];
        }

        // ── Content ──────────────────────────────────────────────────
        if ($this->canAny($user, ['access-content-admin', 'edit-content', 'view-posts', 'view-events', 'view-faqs', 'manage-forms'])) {
            $contentOpen = request()->routeIs(
                'admin.posts*', 'admin.events*', 'admin.categories*',
                'admin.tags*', 'admin.faqs*', 'admin.forms*', 'admin.submissions*', 'admin.ai-images.settings*', 'news.*', 'videos.*'
            );

            $contentChildren = [];
            if ($this->canAny($user, ['view-posts', 'access-content-admin', 'edit-content'])) {
                $contentChildren[] = $this->item('posts', 'Posts', 'bi bi-file-text', route('admin.posts.index'), request()->routeIs('admin.posts*'));
            }
            if ($this->canAny($user, ['manage-ai-image-settings'])) {
                $contentChildren[] = $this->item('ai-images', 'AI Images', 'bi bi-stars', route('admin.ai-images.settings.edit'), request()->routeIs('admin.ai-images.settings*'));
            }
            if ($this->canAny($user, ['view-events', 'access-content-admin', 'edit-content'])) {
                $contentChildren[] = $this->item('events', 'Events', 'bi bi-calendar-event', route('admin.events.index'), request()->routeIs('admin.events*'));
            }
            if ($this->canAny($user, ['view-faqs', 'create-faqs', 'edit-faqs', 'delete-faqs', 'publish-faqs', 'access-content-admin', 'edit-content'])) {
                $contentChildren[] = $this->item('faqs', 'FAQs', 'bi bi-patch-question', route('admin.faqs.index'), request()->routeIs('admin.faqs*'));
            }
            if ($this->canAny($user, ['manage-post-taxonomy', 'access-content-admin', 'edit-content'])) {
                $contentChildren[] = $this->item('categories', 'Categories', 'bi bi-tag', route('admin.categories.index'), request()->routeIs('admin.categories*'));
                $contentChildren[] = $this->item('tags', 'Tags', 'bi bi-tags', route('admin.tags.index'), request()->routeIs('admin.tags*'));
            }
            if ($this->canAny($user, ['access-content-admin', 'edit-content'])) {
                $contentChildren[] = $this->item('news-updates', 'News Updates', 'bi bi-megaphone', route('news.index'), request()->routeIs('news.*'));
                $contentChildren[] = $this->item('videos', 'Videos', 'bi bi-camera-reels', route('videos.index'), request()->routeIs('videos.*'));
            }
            if ($this->canAny($user, ['manage-forms', 'access-content-admin', 'edit-content'])) {
                $contentChildren[] = $this->item('forms', 'Forms', 'bi bi-ui-checks', route('admin.forms.index'), request()->routeIs('admin.forms*', 'admin.submissions*'));
                $contentChildren[] = $this->item('submissions', 'Submissions', 'bi bi-inbox', route('admin.submissions.index'), request()->routeIs('admin.submissions*'));
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
        if ($this->canAny($user, ['access-lms-admin', 'manage-courses', 'manage-lessons', 'manage-exams', 'manage-enrollments', 'manage-certificates'])) {
            $lmsOpen = request()->routeIs(
                'admin.courses*', 'admin.lessons*', 'admin.exams*',
                'admin.enrollments*', 'admin.certificates*', 'admin.exam-attempts*',
                'admin.asom-page*', 'admin.certificate-settings*'
            );

            $lmsChildren = [];
            if ($this->canAny($user, ['manage-courses', 'access-lms-admin'])) {
                $lmsChildren[] = $this->item('courses', 'Courses', 'bi bi-book', route('admin.courses.index'), request()->routeIs('admin.courses*'));
            }
            if ($this->canAny($user, ['manage-lessons', 'access-lms-admin'])) {
                $lmsChildren[] = $this->item('lessons', 'Lessons', 'bi bi-play-circle', route('admin.lessons.index'), request()->routeIs('admin.lessons*'));
            }
            if ($this->canAny($user, ['manage-exams', 'access-lms-admin'])) {
                $lmsChildren[] = $this->item('exams', 'Exams', 'bi bi-pencil-square', route('admin.exams.index'), request()->routeIs('admin.exams*', 'admin.exam-attempts*'));
                $lmsChildren[] = $this->item('exam-attempts', 'Exam Attempts', 'bi bi-clipboard-data', route('admin.exam-attempts.index'), request()->routeIs('admin.exam-attempts*'));
            }
            if ($this->canAny($user, ['manage-enrollments', 'access-lms-admin'])) {
                $lmsChildren[] = $this->item('enrollments', 'Enrollments', 'bi bi-person-check', route('admin.enrollments.index'), request()->routeIs('admin.enrollments*'));
            }
            if ($this->canAny($user, ['manage-certificates', 'access-lms-admin'])) {
                $lmsChildren[] = $this->item('certificates', 'Certificates', 'bi bi-award', route('admin.certificates.index'), request()->routeIs('admin.certificates*'));
                $lmsChildren[] = $this->item('certificate-settings', 'Certificate Settings', 'bi bi-sliders', route('admin.certificate-settings'), request()->routeIs('admin.certificate-settings*'));
            }
            if ($this->canAny($user, ['manage-courses', 'access-lms-admin'])) {
                $lmsChildren[] = $this->item('asom-page', 'ASOM Page', 'bi bi-layout-text-window', route('admin.asom-page.edit'), request()->routeIs('admin.asom-page*'));
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
        if ($this->canAny($user, ['view-reports'])) {
            $celebrationsOpen = request()->routeIs('admin.celebrations*', 'admin.dashboard.celebrants*');
            $sections[] = [
                'label' => 'Celebrations',
                'items' => [
                    $this->item('celebrations', 'Celebrations', 'bi bi-balloon', null, $celebrationsOpen, null, $celebrationsOpen, [
                        $this->item('celebrants', 'Celebrants', 'bi bi-people', route('admin.dashboard.celebrants'), request()->routeIs('admin.dashboard.celebrants*')),
                        $this->item('celebrations-logs', 'Wish Logs', 'bi bi-journal', route('admin.celebrations.logs'), request()->routeIs('admin.celebrations.logs*')),
                        $this->item('celebrations-stats', 'Statistics', 'bi bi-bar-chart', route('admin.celebrations.statistics'), request()->routeIs('admin.celebrations.statistics*')),
                        $this->item('celebrations-calendar', 'Calendar', 'bi bi-calendar3', route('admin.celebrations.calendar'), request()->routeIs('admin.celebrations.calendar*')),
                    ]),
                ],
            ];
        }

        // ── Communications ───────────────────────────────────────────
        if ($this->canAny($user, ['manage-mail', 'manage-mail-templates', 'manage-notification-settings'])) {
            $mailOpen = request()->routeIs('admin.mail*', 'admin.mail-templates*', 'admin.notification-settings*');
            $mailChildren = [];

            if ($this->canAny($user, ['manage-mail'])) {
                $mailChildren[] = $this->item('mail-compose', 'Compose', 'bi bi-pencil', route('admin.mail.compose'), request()->routeIs('admin.mail.compose'));
            }
            if ($this->canAny($user, ['manage-mail-templates'])) {
                $mailChildren[] = $this->item('mail-templates', 'Templates', 'bi bi-file-earmark-text', route('admin.mail-templates.index'), request()->routeIs('admin.mail-templates*'));
            }
            if ($this->canAny($user, ['manage-notification-settings'])) {
                $mailChildren[] = $this->item('notification-settings', 'Notification Settings', 'bi bi-bell', route('admin.notification-settings.edit'), request()->routeIs('admin.notification-settings*'));
                $mailChildren[] = $this->item('event-reminders', 'Event Reminders', 'bi bi-alarm', route('admin.notification-settings.event-reminders'), request()->routeIs('admin.notification-settings.event-reminders*'));
                $mailChildren[] = $this->item('reminder-logs', 'Reminder Logs', 'bi bi-clock-history', route('admin.notification-settings.reminder-logs'), request()->routeIs('admin.notification-settings.reminder-logs*'));
            }

            if (! empty($mailChildren)) {
                $sections[] = [
                    'label' => 'Communications',
                    'items' => [
                        $this->item('mail-group', 'Communications', 'bi bi-envelope', null, $mailOpen, null, $mailOpen, $mailChildren),
                    ],
                ];
            }
        }

        // ── System ───────────────────────────────────────────────────
        $systemItems = [];

        if ($this->canAny($user, ['view-roles', 'manage-roles', 'manage-user-roles'])) {
            $systemItems[] = $this->item('roles', 'Roles', 'bi bi-shield', route('admin.roles.index'), request()->routeIs('admin.roles*'));
        }
        if ($this->canAny($user, ['view-roles', 'manage-roles', 'manage-user-roles'])) {
            $systemItems[] = $this->item('permissions', 'Permissions', 'bi bi-lock', route('admin.permissions.index'), request()->routeIs('admin.permissions*'));
        }
        if ($this->canAny($user, ['view-audit-log', 'manage-audit-log'])) {
            $systemItems[] = $this->item('audit-log', 'Audit Log', 'bi bi-journal-text', route('admin.audit.index'), request()->routeIs('admin.audit*'));
        }
        if ($this->canAny($user, ['manage-user-sessions'])) {
            $systemItems[] = $this->item('sessions', 'Sessions', 'bi bi-window', route('admin.sessions.index'), request()->routeIs('admin.sessions*'));
        }
        if ($this->canAny($user, ['manage-files'])) {
            $systemItems[] = $this->item('admin-files', 'File Manager', 'bi bi-folder', route('admin.files.index'), request()->routeIs('admin.files*'));
            $systemItems[] = $this->item('admin-files-analysis', 'Storage Analysis', 'bi bi-pie-chart', route('admin.files.analysis'), request()->routeIs('admin.files.analysis'));
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

        if ($this->canAny($user, ['manage-ai-image-settings'])) {
            array_splice($items, 1, 0, [
                $this->item('ai-images', 'AI Images', 'bi bi-stars', route('admin.ai-images.settings.edit'), request()->routeIs('admin.ai-images.settings*')),
            ]);
        }

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

    private function canAny(?User $user, array $permissions): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isAdmin() || $user->hasRole('admin')) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }
}
