<?php

namespace App\Http\Controllers\Admin\LMS;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Course;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with('course')->paginate(10);
        return view('admin.lms.exams.index', compact('exams'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('admin.lms.exams.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:1',
            'allow_retakes' => 'boolean'
        ]);

        $exam = Exam::create($validated);
        return redirect()->route('admin.exams.edit', $exam);
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {   
        $exam->load(['course', 'questions']);
        $courses = Course::all();
        return view('admin.lms.exams.edit', compact('exam', 'courses'));
    }

    public function edit(Exam $exam)
    {
        $courses = Course::all();
        return view('admin.lms.exams.edit', compact('exam', 'courses'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:1',
            'allow_retakes' => 'boolean'
        ]);
       
        $exam->update($validated);
        return redirect()->route('admin.exams.show', $exam);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
{
    $exam->questions()->delete(); // Delete associated questions first
    $exam->delete();
    
    return redirect()->route('admin.exams.index')
                    ->with('success', 'Exam deleted successfully');
}



    public function preview(Exam $exam)
    {
        $exam->load('questions');
        return view('admin.lms.exams.preview', compact('exam'));
    }

    public function toggleActivation(Exam $exam)
{dd($exam);
    $exam->is_active = !$exam->is_active;
    $exam->save();
    
    return response()->json([
        'success' => true,
        'is_active' => $exam->is_active,
        'message' => $exam->is_active ? 'Exam activated successfully' : 'Exam deactivated successfully'
    ]);
}

    

}