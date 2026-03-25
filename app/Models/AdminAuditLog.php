<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

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
        'user_id',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function adminUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_user_id');
    }

    protected static ?array $availableColumns = null;

    public static function record(array $attributes): ?self
    {
        $columns = static::availableColumns();

        if (isset($columns['user_id']) && ! array_key_exists('user_id', $attributes) && array_key_exists('admin_user_id', $attributes)) {
            $attributes['user_id'] = $attributes['admin_user_id'];
        }

        if (isset($columns['admin_user_id']) && ! array_key_exists('admin_user_id', $attributes) && array_key_exists('user_id', $attributes)) {
            $attributes['admin_user_id'] = $attributes['user_id'];
        }

        $filtered = array_intersect_key($attributes, $columns);

        if ($filtered === []) {
            return null;
        }

        return static::query()->create($filtered);
    }

    private static function availableColumns(): array
    {
        if (static::$availableColumns !== null) {
            return static::$availableColumns;
        }

        static::$availableColumns = array_flip(Schema::getColumnListing((new static())->getTable()));

        return static::$availableColumns;
    }
}
