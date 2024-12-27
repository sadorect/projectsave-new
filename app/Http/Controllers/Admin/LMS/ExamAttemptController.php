<?php

namespace App\Http\Controllers\Admin\LMS;

use App\Models\Exam;
use App\Models\User;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExamAttemptController extends Controller
{
    public function index()
{
    $attempts = ExamAttempt::with(['user', 'exam'])->latest()->paginate(20);
    return view('admin.lms.exams.show-attempts', compact('attempts'));
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




        public function resetAttempts(Exam $exam, User $user = null)
        {
            if ($user) {
                // Reset for specific student
                ExamAttempt::where('exam_id', $exam->id)
                        ->where('user_id', $user->id)
                        ->delete();
                $message = "Attempts reset for student: {$user->name}";
            } else {
                // Reset for all students
                ExamAttempt::where('exam_id', $exam->id)->delete();
                $message = "Attempts reset for all students";
            }

            return back()->with('success', $message);
        }

}
