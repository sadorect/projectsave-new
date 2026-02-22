<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!auth()->check() || !$user->is_admin) {
            return redirect()->route('admin.login.form')
                ->with('error', 'You must be an administrator to access this area.');
        }

        return $next($request);
    }
}
