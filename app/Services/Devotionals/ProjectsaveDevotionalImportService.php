<?php

namespace App\Services\Devotionals;

use App\Models\Category;
use App\Models\Post;
use App\Services\HtmlSanitizer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class ProjectsaveDevotionalImportService
{
    /**
     * @var array<string, Category>
     */
    private array $categoryCache = [];

    private bool $postsHasNewsletterSentAt;
    private bool $postsHasViewCount;

    public function __construct()
    {
        $this->postsHasNewsletterSentAt = Schema::hasTable('posts') && Schema::hasColumn('posts', 'newsletter_sent_at');
        $this->postsHasViewCount = Schema::hasTable('posts') && Schema::hasColumn('posts', 'view_count');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function parseFile(string $path): array
    {
        $lines = @file($path, FILE_IGNORE_NEW_LINES);

        if ($lines === false) {
            throw new RuntimeException('Unable to read the uploaded devotional file.');
        }

        $entries = [];
        $current = [];

        foreach ($lines as $line) {
            if ($this->isTimestampLine($line)) {
                if ($current !== []) {
                    $parsed = $this->parseEntry($current);
                    if ($parsed !== null) {
                        $entries[] = $parsed;
                    }
                }

                $current = [$line];
                continue;
            }

            if ($current === []) {
                continue;
            }

            if ($this->isExtractorArtifactLine($line)) {
                continue;
            }

            $current[] = $line;
        }

        if ($current !== []) {
            $parsed = $this->parseEntry($current);
            if ($parsed !== null) {
                $entries[] = $parsed;
            }
        }

        return $entries;
    }

    /**
     * @return array<string, mixed>
     */
    public function importFile(string $path, int $userId, string $duplicateStrategy = 'update'): array
    {
        $entries = $this->parseFile($path);
        $summary = $this->makeSummary(count($entries));

        foreach ($entries as $entry) {
            try {
                $result = $this->importParsedEntry($entry, $userId, $duplicateStrategy);
                $this->applyResultToSummary($summary, $result);
            } catch (\Throwable $exception) {
                $this->appendFailureToSummary($summary, $entry, $exception->getMessage());

                Log::warning('Devotional import entry failed.', [
                    'title' => $entry['title'] ?? null,
                    'published_at' => optional($entry['published_at'] ?? null)?->toIso8601String(),
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        return $this->finalizeSummary($summary);
    }

    /**
     * @param  array<string, mixed>  $entry
     * @return array{status: string, category_name: string, category_created: bool}
     */
    public function importParsedEntry(array $entry, int $userId, string $duplicateStrategy = 'update'): array
    {
        /** @var Carbon $publishedAt */
        $publishedAt = $entry['published_at'];

        $existing = Post::query()
            ->where('title', (string) $entry['title'])
            ->where('published_at', $publishedAt)
            ->first();

        if ($existing && $duplicateStrategy === 'skip') {
            return [
                'status' => 'skipped',
                'category_name' => trim((string) ($entry['category_name'] ?? '')) ?: 'Christian Living',
                'category_created' => false,
            ];
        }

        [$category, $categoryCreated] = $this->resolveCategory((string) $entry['category_name']);

        return DB::transaction(function () use ($entry, $userId, $publishedAt, $category, $categoryCreated, $existing): array {
            $payload = [
                'title' => (string) $entry['title'],
                'scripture' => $entry['scripture'] ?: null,
                'bible_text' => $entry['bible_text'] ?: null,
                'subtitle' => $entry['subtitle'] ?: null,
                'details' => (string) $entry['details'],
                'action_point' => $entry['action_point'] ?: null,
                'author' => $entry['author'] ?: 'Projectsave International',
                'user_id' => $userId,
                'status' => 'published',
                'published_at' => $publishedAt,
            ];

            if ($this->postsHasNewsletterSentAt) {
                $payload['newsletter_sent_at'] = $publishedAt;
            }

            if ($this->postsHasViewCount) {
                $payload['view_count'] = $existing?->view_count ?? 0;
            }

            if ($existing) {
                $existing->forceFill($payload)->save();
                $existing->categories()->sync([$category->id]);

                return [
                    'status' => 'updated',
                    'category_name' => $category->name,
                    'category_created' => $categoryCreated,
                ];
            }

            $post = new Post();
            $post->forceFill(array_merge($payload, [
                'slug' => $this->generateUniqueSlug((string) $entry['title'], $publishedAt),
                'comments_count' => 0,
                'created_at' => $publishedAt,
                'updated_at' => $publishedAt,
            ]));
            $post->save();
            $post->categories()->sync([$category->id]);

            return [
                'status' => 'created',
                'category_name' => $category->name,
                'category_created' => $categoryCreated,
            ];
        });
    }

    /**
     * @param  array<int, string>  $entryLines
     * @return array<string, mixed>|null
     */
    private function parseEntry(array $entryLines): ?array
    {
        $header = array_shift($entryLines);

        if (! is_string($header) || ! preg_match('/projectsave\s+devotional\b/i', $header)) {
            return null;
        }

        $publishedAt = $this->parseTimestamp($header);
        $author = $this->parseAuthor($header);

        if (! $publishedAt) {
            return null;
        }

        $lines = $this->trimBlankEdges($entryLines);
        $lines = array_values(array_filter($lines, fn (string $line) => ! $this->isExtractorArtifactLine($line)));

        while ($lines !== [] && $this->isDateHeadingLine($lines[0])) {
            array_shift($lines);
            $lines = $this->trimBlankEdges($lines);
        }

        if ($lines === []) {
            return null;
        }

        $title = $this->normalizeTitle((string) array_shift($lines));
        $lines = $this->trimBlankEdges($lines);

        if ($title === '') {
            return null;
        }

        $lines = $this->removeTrailingFooterLines($lines);

        [$bibleTextLines, $lines] = $this->extractLeadingScriptureLines($lines);

        $actionStart = $this->findLastMarkerIndex($lines, [
            '/^\*?\s*ACTION POINT\s*\*?\s*:/i',
            '/^\*?\s*ACTION\s*\*?\s*:/i',
        ]);

        $prayerStart = $this->findLastMarkerIndex($lines, [
            '/^\*?\s*PRAYER\s*\*?\s*:/i',
            '/^\*?\s*PRAYER POINTS?\s*\*?\s*:?\s*$/i',
            '/^\s*Prayer Points\s*:?\s*$/i',
        ]);

        $actionLines = [];
        if ($actionStart !== null) {
            $actionLines = array_slice($lines, $actionStart);
            $lines = array_slice($lines, 0, $actionStart);
        } elseif ($prayerStart !== null) {
            $actionLines = array_slice($lines, $prayerStart);
            $lines = array_slice($lines, 0, $prayerStart);
        }

        $detailsText = trim(implode("\n", $this->trimBlankEdges($lines)));
        $bibleText = $this->cleanLeadingLabel(implode("\n", $bibleTextLines));
        $actionText = $this->cleanLeadingLabel(implode("\n", $actionLines));

        if ($detailsText === '' && $actionText !== '') {
            $detailsText = $actionText;
            $actionText = '';
        }

        if ($detailsText === '') {
            $detailsText = 'Content missing from source export. Please review this devotional manually during cleanup.';
        }

        $categoryName = $this->suggestCategoryName($title, trim($detailsText . "\n" . $actionText . "\n" . $bibleText));

        return [
            'title' => $title,
            'scripture' => $this->extractScriptureReference($bibleText),
            'bible_text' => $bibleText !== '' ? HtmlSanitizer::clean($this->convertPlainTextToHtml($bibleText)) : null,
            'subtitle' => null,
            'details' => HtmlSanitizer::clean($this->convertPlainTextToHtml($detailsText)),
            'action_point' => $actionText !== '' ? HtmlSanitizer::clean($this->convertPlainTextToHtml($actionText)) : null,
            'author' => $author ?: 'Projectsave International',
            'published_at' => $publishedAt,
            'category_name' => $categoryName,
        ];
    }

    private function parseTimestamp(string $header): ?Carbon
    {
        $normalized = str_replace(["\u{202F}", "\u{00A0}"], ' ', trim($header));

        if (! preg_match('/^(\d{1,2}\/\d{1,2}\/\d{2,4},\s+\d{1,2}:\d{2}\s*[AP]M)\s+-/i', $normalized, $matches)) {
            return null;
        }

        $timestamp = preg_replace('/\s+/', ' ', strtoupper($matches[1]));

        foreach (['n/j/y, g:i A', 'n/j/Y, g:i A'] as $format) {
            try {
                return Carbon::createFromFormat($format, $timestamp);
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    private function parseAuthor(string $header): ?string
    {
        if (! preg_match('/ - (.*?):/u', $header, $matches)) {
            return null;
        }

        return trim($matches[1]) ?: null;
    }

    private function isTimestampLine(string $line): bool
    {
        return (bool) preg_match('/^\d{1,2}\/\d{1,2}\/\d{2,4},\s+\d{1,2}:\d{2}.*? - .*?:/u', $line);
    }

    private function isExtractorArtifactLine(string $line): bool
    {
        return (bool) preg_match('/^(?:={10,}|-{10,}|DEVOTIONAL \d{4} \| Source start line:|EXTRACTED PROJECTSAVE DAILY DEVOTIONALS|Source file:|Matches found:|First matched date:|Last matched date:|Senders captured:|Pattern used:|Each block below is preserved)/i', trim($line));
    }

    private function isDateHeadingLine(string $line): bool
    {
        $trimmed = trim($line, " \t\n\r\0\x0B,.");

        if ($trimmed === '') {
            return false;
        }

        return (bool) preg_match(
            '/^(?:' .
                '(monday|tuesday|wednesday|thursday|friday|saturday|sunday)' .
                '|' .
                '(january|february|march|april|may|june|july|august|september|october|november|december)' .
                '(?:\s+\d{1,2}(?:st|nd|rd|th)?(?:,\s*\d{4})?)?' .
                '|' .
                '\d{1,2}(?:st|nd|rd|th)?' .
            ')$/i',
            $trimmed
        );
    }

    /**
     * @param  array<int, string>  $lines
     * @return array{0: array<int, string>, 1: array<int, string>}
     */
    private function extractLeadingScriptureLines(array $lines): array
    {
        $lines = $this->trimBlankEdges($lines);

        if ($lines === []) {
            return [[], []];
        }

        $block = [];
        $index = 0;

        while ($index < count($lines) && trim($lines[$index]) !== '') {
            $block[] = $lines[$index];
            $index++;
        }

        if ($block === [] || ! $this->looksLikeScriptureBlock($block)) {
            return [[], $lines];
        }

        $remaining = array_slice($lines, $index);

        return [$block, $this->trimBlankEdges($remaining)];
    }

    /**
     * @param  array<int, string>  $block
     */
    private function looksLikeScriptureBlock(array $block): bool
    {
        $firstLine = trim($block[0] ?? '');
        $combined = trim(implode(' ', $block));

        if ($firstLine === '') {
            return false;
        }

        if ((bool) preg_match('/^\*?\s*Text\s*\*?\s*:/i', $firstLine)) {
            return true;
        }

        return (bool) preg_match('/^((?:[1-3]\s*)?[A-Za-z]+(?:\s+[A-Za-z]+){0,4}\s*\d+(?::\d+(?:-\d+)?)?)/', $combined);
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function removeTrailingFooterLines(array $lines): array
    {
        $lines = $this->trimBlankEdges($lines);

        while ($lines !== []) {
            $last = trim($lines[array_key_last($lines)]);

            if ($last === '') {
                array_pop($lines);
                continue;
            }

            if (! $this->isFooterLine($last)) {
                break;
            }

            array_pop($lines);
        }

        return $this->trimBlankEdges($lines);
    }

    private function isFooterLine(string $line): bool
    {
        return (bool) preg_match(
            '/^(?:Projectsave Int\'?l Ministry\.?|©\s*Projectsave.*|©PROJECTSAVE.*|IG:\s*@Projectsave_ministries.*|Shalom\.?|https?:\/\/\S+|\*?N:B\b.*)$/i',
            trim($line)
        );
    }

    /**
     * @param  array<int, string>  $lines
     * @param  array<int, string>  $patterns
     */
    private function findLastMarkerIndex(array $lines, array $patterns): ?int
    {
        for ($index = count($lines) - 1; $index >= 0; $index--) {
            $trimmed = trim($lines[$index]);

            if ($trimmed === '') {
                continue;
            }

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $trimmed)) {
                    return $index;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<int, string>  $lines
     * @return array<int, string>
     */
    private function trimBlankEdges(array $lines): array
    {
        while ($lines !== [] && trim($lines[0]) === '') {
            array_shift($lines);
        }

        while ($lines !== [] && trim($lines[array_key_last($lines)]) === '') {
            array_pop($lines);
        }

        return array_values($lines);
    }

    private function normalizeTitle(string $title): string
    {
        $title = trim($title);
        $title = preg_replace('/^\*?\s*Topic\s*\*?\s*:/i', '', $title);
        $title = trim($title, " \t\n\r\0\x0B*");

        return preg_replace('/\s+/', ' ', $title) ?? $title;
    }

    private function cleanLeadingLabel(string $text): string
    {
        $lines = preg_split('/\R/u', trim($text)) ?: [];

        if ($lines === []) {
            return '';
        }

        $lines[0] = preg_replace('/^\*?\s*(?:ACTION POINT|ACTION|PRAYER POINTS?|PRAYER|TEXT)\s*\*?\s*:\s*/i', '', $lines[0]) ?? $lines[0];

        if (trim($lines[0]) === '') {
            array_shift($lines);
        }

        return trim(implode("\n", $lines));
    }

    private function extractScriptureReference(string $text): ?string
    {
        $plain = trim(strip_tags($text));

        if ($plain === '') {
            return null;
        }

        if (preg_match('/^((?:[1-3]\s*)?[A-Za-z]+(?:\s+[A-Za-z]+){0,4}\s*\d+(?::\d+(?:-\d+)?)?)/', $plain, $matches)) {
            return trim($matches[1]);
        }

        return Str::limit($plain, 255, '');
    }

    private function convertPlainTextToHtml(string $text): string
    {
        $text = trim(str_replace(["\r\n", "\r"], "\n", $text));

        if ($text === '') {
            return '';
        }

        $blocks = preg_split("/\n{2,}/", $text) ?: [];
        $htmlBlocks = [];

        foreach ($blocks as $block) {
            $lines = array_values(array_filter(array_map('trim', explode("\n", $block)), fn (string $line) => $line !== ''));

            if ($lines === []) {
                continue;
            }

            $bulletItems = [];
            $allBullets = true;

            foreach ($lines as $line) {
                if (preg_match('/^(?:[-*•]+|\d+[.)])\s+(.+)$/u', $line, $matches)) {
                    $bulletItems[] = '<li>' . $this->formatInlineText($matches[1]) . '</li>';
                    continue;
                }

                $allBullets = false;
                break;
            }

            if ($allBullets && $bulletItems !== []) {
                $htmlBlocks[] = '<ul>' . implode('', $bulletItems) . '</ul>';
                continue;
            }

            $htmlBlocks[] = '<p>' . implode('<br>', array_map(fn (string $line) => $this->formatInlineText($line), $lines)) . '</p>';
        }

        return implode("\n", $htmlBlocks);
    }

    private function formatInlineText(string $text): string
    {
        $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return preg_replace('/\*((?:[^*]|\\\\\*)+)\*/u', '<strong>$1</strong>', $escaped) ?? $escaped;
    }

    private function suggestCategoryName(string $title, string $context): string
    {
        $titleHaystack = Str::lower($title);
        $contextHaystack = Str::lower($context);

        $categories = [
            'Prayer' => ['prayer', 'pray', 'fasting', 'intercession', 'tongues'],
            'Marriage & Relationships' => ['marriage', 'relationship', 'courtship', 'spouse', 'wife', 'husband', 'fiance', 'before marriage'],
            'Purpose & Calling' => ['purpose', 'calling', 'assignment', 'meaningful life', 'destiny'],
            'Missions & Evangelism' => ['mission', 'missionary', 'evangel', 'gospel', 'souls', 'unreached', 'kingdom-minded'],
            'Holiness & Purity' => ['lust', 'purity', 'sin', 'sexual', 'masturbation', 'holiness'],
            'Family & Parenting' => ['child', 'children', 'parent', 'father', 'mother', 'home training'],
            'Wisdom & Discernment' => ['wisdom', 'discern', 'decision', 'direction', 'confusion', 'inward witness', 'guidance'],
            'Faith & Trust' => ['faith', 'trust', 'believe', 'fear', 'doubt'],
            'Ministry & Leadership' => ['pastor', 'leadership', 'leader', 'ministry', 'mentor', 'church', 'servant of god'],
            'Stewardship & Work' => ['money', 'finance', 'financial', 'work', 'job', 'business', 'capacity', 'steward'],
            'Eternity & Hope' => ['heaven', 'eternity', 'rapture', 'appearing', 'return of christ'],
            'Gratitude & Praise' => ['gratitude', 'thanksgiving', 'praise', 'thank god'],
        ];

        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($titleHaystack, Str::lower($keyword))) {
                    return $category;
                }
            }
        }

        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($contextHaystack, Str::lower($keyword))) {
                    return $category;
                }
            }
        }

        return 'Christian Living';
    }

    /**
     * @return array{0: Category, 1: bool}
     */
    private function resolveCategory(string $name): array
    {
        $normalizedName = trim($name) !== '' ? trim($name) : 'Christian Living';
        $cacheKey = Str::lower($normalizedName);

        if (isset($this->categoryCache[$cacheKey])) {
            return [$this->categoryCache[$cacheKey], false];
        }

        $existing = Category::query()
            ->whereRaw('LOWER(name) = ?', [$cacheKey])
            ->first();

        if ($existing) {
            return [$this->categoryCache[$cacheKey] = $existing, false];
        }

        $slugBase = Str::slug($normalizedName);
        $slug = $slugBase;
        $counter = 2;

        while (Category::query()->where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        $category = Category::query()->create([
            'name' => $normalizedName,
            'slug' => $slug,
        ]);

        return [$this->categoryCache[$cacheKey] = $category, true];
    }

    /**
     * @return array<string, mixed>
     */
    private function makeSummary(int $detected): array
    {
        return [
            'detected' => $detected,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
            'created_categories' => [],
            'category_counts' => [],
            'failures' => [],
        ];
    }

    /**
     * @param  array<string, mixed>  $summary
     * @param  array{status: string, category_name: string, category_created: bool}  $result
     */
    private function applyResultToSummary(array &$summary, array $result): void
    {
        $status = $result['status'];

        if (isset($summary[$status])) {
            $summary[$status]++;
        }

        $categoryName = $result['category_name'];
        $summary['category_counts'][$categoryName] = ($summary['category_counts'][$categoryName] ?? 0) + 1;

        if ($result['category_created'] ?? false) {
            $summary['created_categories'][] = $categoryName;
        }
    }

    /**
     * @param  array<string, mixed>  $summary
     * @param  array<string, mixed>  $entry
     */
    private function appendFailureToSummary(array &$summary, array $entry, string $reason): void
    {
        $summary['failed']++;
        $summary['failures'][] = [
            'title' => $entry['title'] ?? 'Untitled devotional',
            'published_at' => optional($entry['published_at'] ?? null)?->format('Y-m-d H:i'),
            'reason' => $reason,
        ];
    }

    /**
     * @param  array<string, mixed>  $summary
     * @return array<string, mixed>
     */
    private function finalizeSummary(array $summary): array
    {
        ksort($summary['category_counts']);
        $summary['created_categories'] = array_values(array_unique($summary['created_categories']));

        return $summary;
    }

    private function generateUniqueSlug(string $title, Carbon $publishedAt, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;

        if ($this->slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $publishedAt->format('Y-m-d');
        }

        $counter = 2;
        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $publishedAt->format('Y-m-d') . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        return Post::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }
}
