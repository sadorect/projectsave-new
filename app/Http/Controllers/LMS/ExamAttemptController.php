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
    // Check remaining attempts
    $attemptCount = ExamAttempt::where('user_id', auth()->id())
                               ->where('exam_id', $exam->id)
                               ->count();

    if ($attemptCount >= $exam->max_attempts) {
        return redirect()->back()
            ->with('error', 'Maximum attempts reached for this exam');
    }

    // Create new attempt if allowed
    $attempt = ExamAttempt::create([
        'user_id' => auth()->id(),
        'exam_id' => $exam->id,
        'started_at' => now()
    ]);

    return view('lms.exams.take', compact('exam', 'attempt'));
    //return redirect()->route('lms.exams.take', [$exam, $attempt]);
}

public function submit(Request $request, Exam $exam, ExamAttempt $attempt)
{
    $score = $this->calculateScore($exam, $request->answers);
    $passed = $score >= $exam->passing_score;

    $attempt->update([
        'completed_at' => now(),
        'score' => $score,
        'answers' => $request->answers,
        'passed' => $passed
    ]);

    $attempt = ExamAttempt::where('user_id', auth()->id())
                             ->where('exam_id', $exam->id)
                             ->latest()
                             ->firstOrFail();
                             return view('lms.exams.results', [
                                'exam' => $exam,
                                'attempt' => $attempt,
                                'score' => $score,
                                'passed' => $passed
                            ]);
    //return redirect()->route('lms.exams.results', [$exam, 'score' => $score]);
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
