<?php

namespace App\Models;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    protected $table = 'lesson_progress';
    
    protected $fillable = ['user_id', 'lesson_id', 'completed', 'completed_at'];
    
    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
