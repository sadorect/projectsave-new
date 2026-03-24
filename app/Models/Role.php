<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'slug', 'description', 'guard_name'];

    protected static function booted(): void
    {
        static::creating(function (self $role) {
            $role->guard_name ??= config('auth.defaults.guard', 'web');
            $role->slug ??= Str::slug($role->name);
        });

        static::saving(function (self $role) {
            $role->guard_name ??= config('auth.defaults.guard', 'web');
            $role->slug ??= Str::slug($role->name);
        });
    }

    public static function findByName(string $name, ?string $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (! $role) {
            $role = static::findByParam(['slug' => $name, 'guard_name' => $guardName]);
        }

        if (! $role && ctype_digit($name)) {
            $role = static::findByParam(['id' => (int) $name, 'guard_name' => $guardName]);
        }

        if (! $role) {
            throw RoleDoesNotExist::named($name, $guardName);
        }

        return $role;
    }
}
