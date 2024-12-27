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

    return redirect()->route('lms.exams.take', [$exam, $attempt]);
}

public function submit(Request $request, Exam $exam, ExamAttempt $attempt)
{
    // Calculate score
    $totalQuestions = $exam->questions->count();
    $correctAnswers = 0;

    foreach ($request->answers as $questionId => $answer) {
        $question = $exam->questions->find($questionId);
        if ($question && $answer === $question->correct_answer) {
            $correctAnswers++;
        }
    }

    $score = ($correctAnswers / $totalQuestions) * 100;
    $passed = $score >= $exam->passing_score;

    // Update attempt record
    $attempt->update([
        'completed_at' => now(),
        'score' => $score,
        'answers' => $request->answers
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
