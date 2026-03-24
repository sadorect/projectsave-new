<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = ['name', 'slug', 'description', 'category', 'guard_name'];

    protected static function booted(): void
    {
        static::creating(function (self $permission) {
            $permission->guard_name ??= config('auth.defaults.guard', 'web');
            $permission->slug ??= Str::slug($permission->name);
        });

        static::saving(function (self $permission) {
            $permission->guard_name ??= config('auth.defaults.guard', 'web');
            $permission->slug ??= Str::slug($permission->name);
        });
    }

    public static function findByName(string $name, ?string $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $permission = static::getPermission(['name' => $name, 'guard_name' => $guardName]);

        if (! $permission) {
            $permission = static::getPermission(['slug' => $name, 'guard_name' => $guardName]);
        }

        if (! $permission) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }

        return $permission;
    }
}
