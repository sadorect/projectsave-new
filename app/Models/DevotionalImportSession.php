<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevotionalImportSession extends Model
{
    use HasFactory;

    public const STATUS_QUEUED = 'queued';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'source_disk',
        'source_path',
        'source_filename',
        'source_checksum',
        'duplicate_strategy',
        'status',
        'total_entries',
        'processed_entries',
        'last_processed_index',
        'created_count',
        'updated_count',
        'skipped_count',
        'failed_count',
        'created_categories',
        'category_counts',
        'failures',
        'last_error',
        'queued_at',
        'started_at',
        'finished_at',
        'last_activity_at',
    ];

    protected $casts = [
        'total_entries' => 'integer',
        'processed_entries' => 'integer',
        'last_processed_index' => 'integer',
        'created_count' => 'integer',
        'updated_count' => 'integer',
        'skipped_count' => 'integer',
        'failed_count' => 'integer',
        'created_categories' => 'array',
        'category_counts' => 'array',
        'failures' => 'array',
        'queued_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function progressPercentage(): int
    {
        if (($this->total_entries ?? 0) < 1) {
            return 0;
        }

        return (int) min(100, round(($this->processed_entries / $this->total_entries) * 100));
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_QUEUED, self::STATUS_PROCESSING], true);
    }

    public function isStale(int $minutes = 10): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        if (! $this->last_activity_at) {
            return true;
        }

        return $this->last_activity_at->lt(now()->subMinutes($minutes));
    }
}
