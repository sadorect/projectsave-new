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
        'author',
        'user_id',
        'comments_count',
        'slug',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime'
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

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    protected static function boot()
{
    parent::boot();
    
    static::creating(function ($post) {
        $post->slug = Str::slug($post->title);
    });
}

// In the Post model:
protected function setDetailsAttribute($value) {
    // Match and convert all asterisk-wrapped text while preserving paragraphs
    $pattern = '/\*((?:[^*]|\\\*)+)\*/';
    $value = preg_replace($pattern, '<strong>$1</strong>', $value);
    $this->attributes['details'] = $value;
}

// In the Post model
protected function getDetailsAttribute($value) {
    return preg_replace('/\*((?:[^*]|\\\*)+)\*/', '<strong>$1</strong>', $value);
}


}





