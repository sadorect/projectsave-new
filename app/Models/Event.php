<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['user_id',
        'title', 'slug', 'description', 'details',
        'location', 'image', 'start_date', 'end_date',
        'start_time', 'end_time', 'status', 'published_at'
    ];
    protected $dates = ['date'];

    protected $casts = [
        'date' => 'datetime'
    ];

    protected static function boot()
{
    parent::boot();
    
    static::creating(function ($event) {
        $event->slug = Str::slug($event->title);
    });
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now())
                    ->where('status', 'published')
                    ->orderBy('start_date');
    }


public function reminderLogs()
{
    return $this->hasMany(ReminderLog::class);
}

}