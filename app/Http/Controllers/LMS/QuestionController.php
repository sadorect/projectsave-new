<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Exam;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function create(Exam $exam)
    {
        return view('lms.questions.create', compact('exam'));
    }

    public function store(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1'
        ]);

        $validated['options'] = json_encode($validated['options']);
        $exam->questions()->create($validated);

        return redirect()->route('lms.exams.edit', $exam)
                        ->with('success', 'Question added successfully');
    }
}
