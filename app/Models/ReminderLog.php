<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    protected $fillable = [
        'event_id',
        'recipients_count',
        'status',
        'notes'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
