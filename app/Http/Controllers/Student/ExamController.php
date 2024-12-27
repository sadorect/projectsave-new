<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::where('is_active', true)
                     ->with(['course','questions'])
                     ->paginate(10);
        return view('lms.exams.index', compact('exams'));
    }

    public function show(Exam $exam)
    {
        if (!$exam->is_active) {
            return redirect()->route('lms.exams.index')
                           ->with('error', 'This exam is not available.');
        }
        return view('lms.exams.show', compact('exam'));
    }

    public function start(Exam $exam)
    {
        $remainingAttempts = ExamAttempt::getRemainingAttempts(auth()->id(), $exam->id);
    
    if ($remainingAttempts <= 0) {
        return redirect()->back()
            ->with('error', 'You have reached the maximum number of attempts for this exam.');
    }
        $attempt = ExamAttempt::create([
            'user_id' => auth()->id(),
            'exam_id' => $exam->id,
            'status' => 'in_progress',
            'started_at' => now()
        ]);

        return view('lms.exams.take', compact('exam', 'attempt'));
    }

    public function submit(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        $answers = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string'
        ]);

        $score = $this->calculateScore($exam, $answers['answers']);
        
        $attempt->update([
            'completed_at' => now(),
            'score' => $score,
            'answers' => json_encode($answers['answers'])
        ]);

        return redirect()->route('lms.exams.results', $attempt);
    }

    private function calculateScore(Exam $exam, array $answers)
    {
        $totalPoints = $exam->questions->sum('points');
        $earnedPoints = 0;

        foreach ($exam->questions as $question) {
            if (isset($answers[$question->id]) && $answers[$question->id] === $question->correct_answer) {
                $earnedPoints += $question->points;
            }
        }

        return ($earnedPoints / $totalPoints) * 100;
    }

    public function results(ExamAttempt $attempt)
    {
        $attempt->load(['exam.questions', 'user']);
        
        $userAnswers = $attempt->answers;
        $questions = $attempt->exam->questions;
        $totalQuestions = $questions->count();
        $correctAnswers = 0;
        
        foreach ($questions as $question) {
            if (isset($userAnswers[$question->id]) && $userAnswers[$question->id] === $question->correct_answer) {
                $correctAnswers++;
            }
        }
        
        return view('lms.exams.results', compact('attempt', 'correctAnswers', 'totalQuestions'));
    }
    
}
