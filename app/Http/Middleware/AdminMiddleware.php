<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!auth()->check() || !$user->hasBackofficeAccess()) {
            return redirect()->route('admin.login.form')
                ->with('error', 'You must have back-office access to enter this area.');
        }

        return $next($request);
    }
}
