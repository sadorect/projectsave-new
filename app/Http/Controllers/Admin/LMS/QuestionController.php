<?php

namespace App\Http\Controllers\Admin\LMS;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Exam;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function create(Exam $exam)
    {
        return view('admin.lms.questions.create', compact('exam'));
    }

    public function store(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1'
        ]);

        $exam->questions()->create([
            'question_text' => $validated['question_text'],
            'options' => json_encode($validated['options']),
            'correct_answer' => $validated['correct_answer'],
            'points' => $validated['points']
        ]);

        return redirect()->route('admin.exams.edit', $exam)
                        ->with('success', 'Question added successfully');
    }

    public function edit(Exam $exam, Question $question)
    {
        $question->options = json_decode($question->options);
        return view('admin.lms.questions.edit', compact('exam', 'question'));
    }
    

public function update(Request $request, Exam $exam, Question $question)
{
    $validated = $request->validate([
        'question_text' => 'required|string',
        'options' => 'required|array|min:2',
        'options.*' => 'required|string',
        'correct_answer' => 'required|string',
        'points' => 'required|integer|min:1'
    ]);

    $question->update([
        'question_text' => $validated['question_text'],
        'options' => json_encode($validated['options']),
        'correct_answer' => $validated['correct_answer'],
        'points' => $validated['points']
    ]);

    return redirect()->route('admin.exams.edit', $exam)
                    ->with('success', 'Question updated successfully');
}

public function destroy(Exam $exam, Question $question)
{
    $question->delete();
    return redirect()->route('admin.exams.edit', $exam)
                    ->with('success', 'Question deleted successfully');
}

}
