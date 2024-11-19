<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnniversaryWishLog extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'sent_by',
        'years',
        'message'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
