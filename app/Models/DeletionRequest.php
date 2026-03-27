<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeletionRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'requester_name',
        'requester_email',
        'reason',
        'status',
        'processed_by',
        'processed_notes',
        'processed_at',
    ];

    protected $casts = ['processed_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
