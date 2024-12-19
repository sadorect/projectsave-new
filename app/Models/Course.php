<?php
namespace App\Models;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'slug',
        'instructor_id',
        'featured_image',
        'status'
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function getFeaturedImageAttribute($value)
{
    if ($value) {
        return Storage::url($value);
    }
    return null;
}

public function users()
{
    return $this->belongsToMany(User::class, 'course_user')
                ->withTimestamps()
                ->withPivot('status', 'enrolled_at', 'completed_at');
}



}




