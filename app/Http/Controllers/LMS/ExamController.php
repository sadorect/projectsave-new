<?php

namespace App\Http\Controllers\LMS;

use App\Models\Exam;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with('course')->paginate(10);
        return view('lms.exams.index', compact('exams'));
    }

    public function create()
{
    $courses = Course::all();
    return view('lms.exams.create', compact('courses'));
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100'
        ]);

        $exam = Exam::create($validated);
        return redirect()->route('lms.exams.edit', $exam)->with('success', 'Exam created successfully');
    }

    public function show(Exam $exam)
    {
        $exam->load(['questions', 'course']);
        return view('lms.exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
{
    $courses = Course::all();
    return view('lms.exams.edit', compact('exam', 'courses'));
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
{
    $validated = $request->validate([
        'course_id' => 'required|exists:courses,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'duration_minutes' => 'required|integer|min:1',
        'passing_score' => 'required|integer|min:0|max:100'
    ]);

    $exam->update($validated);
    
    return redirect()->route('lms.exams.show', $exam)->with('success', 'Exam updated successfully');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
