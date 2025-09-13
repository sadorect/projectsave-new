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


    public function show(ExamAttempt $attempt)
    {
        $attempt->load(['user', 'exam.questions']);
        return view('admin.lms.exam-attempts.show', compact('attempt'));
    }

    public function destroy(ExamAttempt $attempt)
    {
        $attempt->delete();
        return redirect()->back()->with('success', 'Attempt record deleted successfully');
    }

    /**
     * Show form to manually pass a student for an exam
     */
    public function manualPass(Exam $exam)
    {
        $users = User::orderBy('name')->get();
        return view('admin.lms.exams.manual-pass', compact('exam', 'users'));
    }

    /**
     * Manually mark a student as passed for an exam
     */
    public function storeManualPass(Request $request, Exam $exam)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'score' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = User::findOrFail($request->user_id);
        
        // Check if user already has a passing attempt
        $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->where('score', '>=', $exam->passing_score)
            ->first();
            
        if ($existingAttempt) {
            return back()->with('warning', "{$user->name} already has a passing score for this exam.");
        }

        // Create manual passing attempt
        ExamAttempt::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now(),
            'completed_at' => now(),
            'score' => $request->score,
            'answers' => [
                'manual_pass' => true,
                'admin_notes' => $request->notes,
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name
            ]
        ]);

        return redirect()->route('admin.exam-attempts.index')
            ->with('success', "Successfully marked {$user->name} as passed for {$exam->title} with score of {$request->score}%");
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
