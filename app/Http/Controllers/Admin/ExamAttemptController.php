<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Exam;

class ExamAttemptController extends Controller
{
    public function index()
    {   
        $exam = Exam::all();
        $attempts = ExamAttempt::with(['user', 'exam'])->latest()->paginate(20);
        
        return view('admin.lms.exams.show-attempts', compact('attempts','exam'));
    }

    public function show(Exam $exam)
    {
        $attempts = $exam->attempts()->with('user')->get();
        return view('admin.attempts.show', compact('exam', 'attempts'));
    }

    public function destroy(ExamAttempt $attempt)
    {
        $attempt->delete();
        return redirect()->back()->with('success', 'Attempt record deleted successfully');
    }
}
