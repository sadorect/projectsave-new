<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFile extends Model
{
    protected $fillable = [
        'user_id', 'original_name', 'filename', 'path', 
        'mime_type', 'size', 'category', 'is_private', 
        'metadata', 'expires_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'is_private' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        // If using S3, return direct S3 URL for public files
        if (config('filesystems.default') === 's3' && !$this->is_private) {
            return Storage::url($this->path);
        }
        
        // For local storage or private files, use download route
        return route('files.download', $this->id);
    }

    public function getDirectUrlAttribute(): string
    {
        // Always return the direct storage URL
        return Storage::url($this->path);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getFormattedSizeAttribute(): string
    {
        return $this->formatBytes($this->size);
    }

    private function formatBytes($size, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }
}