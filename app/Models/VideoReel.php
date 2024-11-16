<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VideoReel extends Model
{
    protected $fillable = ['title', 'youtube_id', 'display_order', 'is_active'];
}
