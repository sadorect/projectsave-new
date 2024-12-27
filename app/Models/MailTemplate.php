<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailTemplate extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'body',
        'variables'
    ];

    protected $casts = [
        'variables' => 'array'
    ];
}
