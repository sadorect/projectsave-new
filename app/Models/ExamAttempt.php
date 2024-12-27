<?php

namespace App\Models;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamAttempt extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'exam_id',
        'started_at',
        'completed_at',
        'score',
        'answers'
    ];
    
    protected $casts = [
        'answers' => 'array',
        'score' => 'float',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function exam()
{
    return $this->belongsTo(Exam::class);
}

public function scopeForUserAndExam($query, $userId, $examId)
{
    return $query->where('user_id', $userId)
                 ->where('exam_id', $examId);
}
}






