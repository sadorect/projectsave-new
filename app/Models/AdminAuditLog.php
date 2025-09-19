<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAuditLog extends Model
{
    protected $table = 'admin_audit_logs';

    protected $fillable = [
        'admin_user_id',
        'action',
        'target_type',
        'target_id',
        'meta',
        'error_fingerprint',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function adminUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_user_id');
    }
}
