<?php

namespace App\Models;

use App\Models\LessonProgress;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'content',
        'order',
        'video_url',
        'video_type',
        'attachments'
    ];

    protected $casts = [
        'attachments' => 'array'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function getEmbedVideoUrlAttribute()
    {
        if (!$this->video_url) {
            return null;
        }

        if (str_contains($this->video_url, 'youtube.com')) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $matches);
            return isset($matches[1]) ? "https://www.youtube.com/embed/{$matches[1]}" : null;
        }

        return $this->video_url;
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function isCompleted($user)
    {
        return $this->progress()
            ->where('user_id', $user->id)
            ->where('completed', true)
            ->exists();
    }

}




