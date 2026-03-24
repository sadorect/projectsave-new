<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'details',
        'status'
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
