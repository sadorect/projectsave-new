<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'scripture',
        'bible_text',
        'subtitle',
        'details',
        'action_point',
        'image',
        'featured_image_candidate_path',
        'featured_image_source',
        'featured_image_generation_enabled',
        'featured_image_generation_status',
        'featured_image_approval_status',
        'featured_image_provider',
        'featured_image_preset',
        'featured_image_prompt',
        'featured_image_options',
        'featured_image_generated_at',
        'featured_image_generation_error',
        'featured_image_reviewed_by',
        'featured_image_reviewed_at',
        'featured_image_review_notes',
        'author',
        'user_id',
        'comments_count',
        'view_count',
        'newsletter_sent_at',
        'status',
        'slug',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'newsletter_sent_at' => 'datetime',
        'featured_image_generation_enabled' => 'boolean',
        'featured_image_options' => 'array',
        'featured_image_generated_at' => 'datetime',
        'featured_image_reviewed_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function featuredImageReviewer()
    {
        return $this->belongsTo(User::class, 'featured_image_reviewed_by');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $post->slug = Str::slug($post->title);
        });
    }

    protected function setDetailsAttribute($value)
    {
        if (is_string($value) && !str_contains($value, '<')) {
            $value = preg_replace('/\*((?:[^*]|\\\*)+)\*/', '<strong>$1</strong>', $value);
        }

        $this->attributes['details'] = $value;
    }

    protected function getDetailsAttribute($value)
    {
        if (is_string($value) && !str_contains($value, '<')) {
            return preg_replace('/\*((?:[^*]|\\\*)+)\*/', '<strong>$1</strong>', $value);
        }

        return $value;
    }

    protected function getTitleAttribute($value)
    {
        return str_replace('*', '', $value);
    }
}
