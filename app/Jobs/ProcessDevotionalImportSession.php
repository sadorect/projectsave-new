<?php

namespace App\Jobs;

use App\Models\DevotionalImportSession;
use App\Services\Devotionals\ProjectsaveDevotionalImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessDevotionalImportSession implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private const CHUNK_SIZE = 50;
    private const MAX_RECORDED_FAILURES = 100;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(public readonly int $sessionId)
    {
        $this->afterCommit = true;
    }

    public function handle(ProjectsaveDevotionalImportService $importService): void
    {
        $lock = Cache::lock('devotional-import-session:' . $this->sessionId, 300);

        if (! $lock->get()) {
            $this->release(15);
            return;
        }

        try {
            $session = DevotionalImportSession::query()->find($this->sessionId);

            if (! $session || $session->status === DevotionalImportSession::STATUS_COMPLETED) {
                return;
            }

            if (! Storage::disk($session->source_disk)->exists($session->source_path)) {
                $this->markSessionFailed($session, 'The uploaded devotional source file could not be found.');
                return;
            }

            if (! $session->user_id) {
                $this->markSessionFailed($session, 'The admin account that queued this import is no longer available.');
                return;
            }

            $session->forceFill([
                'status' => DevotionalImportSession::STATUS_PROCESSING,
                'started_at' => $session->started_at ?? now(),
                'finished_at' => null,
                'last_error' => null,
                'last_activity_at' => now(),
            ])->save();

            $entries = $importService->parseFile(Storage::disk($session->source_disk)->path($session->source_path));
            $totalEntries = count($entries);

            $session->forceFill([
                'total_entries' => $totalEntries,
                'last_activity_at' => now(),
            ])->save();

            if ($totalEntries === 0) {
                $this->markSessionFailed($session, 'No Projectsave devotional entries were detected in the uploaded file.');
                return;
            }

            $nextIndex = max(0, $session->last_processed_index + 1);

            if ($nextIndex >= $totalEntries) {
                $this->markSessionCompleted($session);
                return;
            }

            foreach (array_slice($entries, $nextIndex, self::CHUNK_SIZE, true) as $index => $entry) {
                try {
                    $result = $importService->importParsedEntry(
                        $entry,
                        (int) $session->user_id,
                        $session->duplicate_strategy
                    );

                    $this->recordProcessedEntry($session, (int) $index, $result);
                } catch (Throwable $exception) {
                    Log::warning('Devotional import entry failed during queued processing.', [
                        'session_id' => $session->getKey(),
                        'index' => $index,
                        'title' => $entry['title'] ?? null,
                        'error' => $exception->getMessage(),
                    ]);

                    $this->recordFailedEntry($session, (int) $index, $entry, $exception->getMessage());
                }
            }

            if ($session->last_processed_index >= ($totalEntries - 1)) {
                $this->markSessionCompleted($session);
                return;
            }

            self::dispatch($session->getKey());
        } finally {
            $lock->release();
        }
    }

    public function failed(Throwable $exception): void
    {
        $session = DevotionalImportSession::query()->find($this->sessionId);

        if (! $session || $session->status === DevotionalImportSession::STATUS_COMPLETED) {
            return;
        }

        $this->markSessionFailed($session, $exception->getMessage());
    }

    /**
     * @param  array{status: string, category_name: string, category_created: bool}  $result
     */
    private function recordProcessedEntry(DevotionalImportSession $session, int $index, array $result): void
    {
        $categoryCounts = $session->category_counts ?? [];
        $createdCategories = $session->created_categories ?? [];

        $categoryName = $result['category_name'];
        $categoryCounts[$categoryName] = ($categoryCounts[$categoryName] ?? 0) + 1;

        if (($result['category_created'] ?? false) && ! in_array($categoryName, $createdCategories, true)) {
            $createdCategories[] = $categoryName;
        }

        $statusColumn = match ($result['status']) {
            'created' => 'created_count',
            'updated' => 'updated_count',
            default => 'skipped_count',
        };

        $session->forceFill([
            'last_processed_index' => $index,
            'processed_entries' => $index + 1,
            $statusColumn => ($session->{$statusColumn} ?? 0) + 1,
            'created_categories' => array_values($createdCategories),
            'category_counts' => $categoryCounts,
            'last_activity_at' => now(),
        ])->save();
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private function recordFailedEntry(DevotionalImportSession $session, int $index, array $entry, string $reason): void
    {
        $failures = $session->failures ?? [];

        if (count($failures) < self::MAX_RECORDED_FAILURES) {
            $failures[] = [
                'index' => $index,
                'title' => $entry['title'] ?? 'Untitled devotional',
                'published_at' => optional($entry['published_at'] ?? null)?->format('Y-m-d H:i'),
                'reason' => $reason,
            ];
        }

        $session->forceFill([
            'last_processed_index' => $index,
            'processed_entries' => $index + 1,
            'failed_count' => ($session->failed_count ?? 0) + 1,
            'failures' => $failures,
            'last_activity_at' => now(),
        ])->save();
    }

    private function markSessionCompleted(DevotionalImportSession $session): void
    {
        $categoryCounts = $session->category_counts ?? [];
        $createdCategories = array_values(array_unique($session->created_categories ?? []));

        ksort($categoryCounts);
        sort($createdCategories, SORT_NATURAL | SORT_FLAG_CASE);

        $session->forceFill([
            'status' => DevotionalImportSession::STATUS_COMPLETED,
            'created_categories' => $createdCategories,
            'category_counts' => $categoryCounts,
            'finished_at' => now(),
            'last_activity_at' => now(),
            'last_error' => null,
        ])->save();
    }

    private function markSessionFailed(DevotionalImportSession $session, string $message): void
    {
        $session->forceFill([
            'status' => DevotionalImportSession::STATUS_FAILED,
            'finished_at' => now(),
            'last_activity_at' => now(),
            'last_error' => $message,
        ])->save();
    }
}
