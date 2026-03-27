<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiteVisitStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_date',
        'visits',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];
}
