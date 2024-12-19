<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $enrolledCourses = auth()->user()->courses()
            ->withPivot('status', 'completed_at')
            ->latest('enrollments.created_at')
            ->paginate(8);
            
        return view('lms.dashboard.index', compact('enrolledCourses'));
    }
}
