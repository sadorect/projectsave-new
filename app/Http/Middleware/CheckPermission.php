<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        foreach ($permissions as $permission) {
            // Preserve the current admin override convention used in route middleware
            // strings like "permission:edit-content,admin".
            if ($permission === 'admin' && $user->isAdmin()) {
                return $next($request);
            }

            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403);
    }
}
