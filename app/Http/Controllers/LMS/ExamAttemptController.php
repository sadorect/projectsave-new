<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class ExamAttemptController extends Controller
{
    public function start(Exam $exam)
    {
        $attempt = ExamAttempt::create([
            'user_id' => auth()->id(),
            'exam_id' => $exam->id,
            'started_at' => now(),
            'answers' => [],
            'score' => 0,
            'passed' => false
        ]);

        return view('lms.exams.attempt', compact('exam', 'attempt'));
    }

    public function submit(Request $request, Exam $exam)
    {
        $attempt = ExamAttempt::where([
            'user_id' => auth()->id(),
            'exam_id' => $exam->id,
        ])->latest()->firstOrFail();

        $score = $this->calculateScore($exam, $request->answers);
        
        $attempt->update([
            'answers' => $request->answers,
            'score' => $score,
            'passed' => $score >= $exam->passing_score,
            'completed_at' => now()
        ]);

        return redirect()->route('lms.exams.results', $exam);
    }

    private function calculateScore(Exam $exam, array $answers)
    {
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($exam->questions as $question) {
            $totalPoints += $question->points;
            if (isset($answers[$question->id]) && $answers[$question->id] === $question->correct_answer) {
                $earnedPoints += $question->points;
            }
        }

        return ($earnedPoints / $totalPoints) * 100;
    }

    public function results(Exam $exam)
    {
        $attempt = ExamAttempt::where('user_id', auth()->id())
                             ->where('exam_id', $exam->id)
                             ->latest()
                             ->firstOrFail();
                              
        return view('lms.exams.results', compact('exam', 'attempt'));
    }
    

}
