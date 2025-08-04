<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

     protected $fillable = ['title', 'description', 'fields', 'require_login', 'notify_emails'];
    protected $casts = [
        'fields' => 'array',
        'notify_emails' => 'array',
    ];

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }
}
