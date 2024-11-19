<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FirstLoginMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->first_login) {
            return redirect()->route('password.change')
                ->with('warning', 'Please change your password to continue.');
        }

        return $next($request);
    }
}
