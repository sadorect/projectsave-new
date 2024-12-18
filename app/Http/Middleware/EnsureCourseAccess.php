<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCourseAccess
{
    public function handle(Request $request, Closure $next)
    {
        $course = $request->route('course');
        
        if (!$request->user()->isEnrolledIn($course)) {
            return redirect()->route('courses.show', $course)
                           ->with('error', 'Please enroll in this course to access its content.');
        }

        return $next($request);
    }
}
