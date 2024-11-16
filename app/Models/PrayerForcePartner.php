<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PrayerForcePartner extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'dob',
        'profession',
        'phone',
        'email',
        'born_again',
        'salvation_date',
        'salvation_place',
        'water_baptized',
        'baptism_type',
        'holy_ghost_baptism',
        'holy_ghost_baptism_reason',
        'leadership_experience',
        'leadership_details',
        'calling',
        'prayer_commitment',
        'status'
    ];

    protected $casts = [
        'dob' => 'date',
        'salvation_date' => 'date',
        'leadership_details' => 'array'
    ];
}
