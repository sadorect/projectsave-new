<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class MinistryReport extends Model
{
    use HasFactory;

    public const REPORT_TYPES = [
        'Outreach',
        'Discipleship',
        'Relief',
        'Campus Ministry',
        'Prayer Mobilization',
        'Leadership Training',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'report_type',
        'lead_team',
        'location',
        'report_date',
        'summary',
        'details',
        'featured_image',
        'gallery',
        'people_reached',
        'souls_impacted',
        'volunteers_count',
        'testimony_title',
        'testimony_quote',
        'prayer_points',
        'next_steps',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'gallery' => 'array',
        'report_date' => 'date',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $report): void {
            $report->slug = static::generateUniqueSlug($report->title);
        });

        static::updating(function (self $report): void {
            if ($report->isDirty('title')) {
                $report->slug = static::generateUniqueSlug($report->title, $report->getKey());
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->lte(now());
    }

    public static function typeOptions(): array
    {
        return self::REPORT_TYPES;
    }

    protected static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: Str::random(8);
        $slug = $baseSlug;
        $counter = 2;

        while (static::query()
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
