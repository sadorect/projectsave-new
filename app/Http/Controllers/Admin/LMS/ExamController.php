<?php

namespace App\Http\Controllers\Admin\LMS;

use App\Models\Exam;
use App\Models\Course;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function questions(Exam $exam)
    {
        
        return view('admin.lms.exams.import_questions', compact('exam'));
    }

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
        return redirect()->route('admin.exams.import-questions', $exam->id)->with('success', 'Exam created successfully. You can now import questions for ' . $exam->title);
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

    public function showImportForm(Exam $exam)
{
    return view('admin.lms.exams.import_questions', compact('exam'));
}

/*
public function importPreview(Request $request, Exam $exam)
{
    $request->validate([
        'docx_file' => 'required|file|mimes:docx',
    ]);

    $path = $request->file('docx_file')->store('temp', 'public');
    $fullPath = storage_path("app/{$path}");
    if (!file_exists($fullPath)) {
        return back()->withErrors(['docx_file' => 'The uploaded file does not exist.']);
    }
    $data = $this->extractQuestionsFromInlineOptions($fullPath);
    $questions = $data['questions'];
    $skipped = $data['skipped'];

    $exam->load('questions');
        return view('admin.lms.exams.preview', compact('exam'));

    Exam::create([
        'course_id' => $exam->course_id,
        'title' => $exam->title,
        'description' => $exam->description,
        'duration_minutes' => $exam->duration_minutes,
        'passing_score' => $exam->passing_score,
        'max_attempts' => $exam->max_attempts,
        'allow_retakes' => $exam->allow_retakes,
        'is_active' => $exam->is_active,
    ]);

    $created = 0;
    foreach ($questions as $q) {
        if (!$q['answer']) continue;
        $exam->questions()->create([
            'exam_id' => $exam->id,
            'question_text' => $q['question'],
            'options' => json_encode($q['options']),
            'correct_answer' => $q['answer'],
            'points' => 1,
        ]);
        $created++;
    }
    Storage::disk('local')->delete($path);

    $skippedCount = count($skipped);
    $message = "{$created} question(s) imported.";
    if ($skippedCount > 0) {
        $message .= " {$skippedCount} question(s) were skipped.";
        // Optionally, pass skipped details to the view
        return back()->with([
            'success' => $message,
            'skipped' => $skipped
        ]);
    }

    return back()->with('success', $message);
}
*/

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
    {
    //dd($exam);
    $exam->is_active = !$exam->is_active;
    $exam->save();
    
    return response()->json([
        'success' => true,
        'is_active' => $exam->is_active,
        'message' => $exam->is_active ? 'Exam activated successfully' : 'Exam deactivated successfully'
    ]);
    }

    private function extractQuestionsFromInlineOptions($filePath)
{
    if (!file_exists($filePath)) {
        throw new \Exception("DOCX file not found at: {$filePath}");
    }

    $phpWord = IOFactory::load($filePath);
    $questions = [];
    $skippedQuestions = []; // To store skipped ones

    foreach ($phpWord->getSections() as $section) {
        foreach ($section->getElements() as $element) {
            if (!($element instanceof \PhpOffice\PhpWord\Element\TextRun)) continue;

            $fullText = '';
            $boldMap = [];

            foreach ($element->getElements() as $subElement) {
                if (!($subElement instanceof \PhpOffice\PhpWord\Element\Text)) continue;

                $text = $subElement->getText();
                $fullText .= $text;

                $style = $subElement->getFontStyle();
                if ($style && method_exists($style, 'isBold') && $style->isBold()) {
                    $boldMap[] = trim($text);
                }
            }

            // Match questions with 4 options inline: (a)...(b)...(c)...(d)...
            if (preg_match('/^\d+\s+(.*?)\s*\(a\)(.*?)\(b\)(.*?)\(c\)(.*?)\(d\)(.*?)$/i', $fullText, $matches)) {
                $questionText = trim($matches[1]);
                $options = [
                    'A' => $this->cleanOption($matches[2]),
                    'B' => $this->cleanOption($matches[3]),
                    'C' => $this->cleanOption($matches[4]),
                    'D' => $this->cleanOption($matches[5]),
                ];

                // Try to find which option was bolded
                $correctOption = null;
                foreach ($options as $key => $value) {
                    foreach ($boldMap as $boldText) {
                        if (stripos($value, $boldText) !== false || stripos($boldText, $value) !== false) {
                            $correctOption = $key;
                            break 2;
                        }
                    }
                }

                if ($correctOption) {
                    $questions[] = [
                        'question' => $questionText,
                        'options' => $options,
                        'answer' => $correctOption,
                    ];
                } else {
                    $skippedQuestions[] = [
                        'question' => $questionText,
                        'reason' => 'No bolded correct option found',
                        'options' => $options,
                        'boldCandidates' => $boldMap,
                    ];
                }
            }
        }
    }

    // Optionally, log or return skipped questions
    if (!empty($skippedQuestions)) {
        Log::warning('Skipped questions during DOCX import', [
            'count' => count($skippedQuestions),
            'items' => $skippedQuestions
        ]);
    }

    return ['questions' => $questions, 'skipped' => $skippedQuestions];
}

private function cleanOption($text)
{
    return trim(preg_replace('/^[A-Da-d]\.\s*/', '', trim($text)));
}

public function importConfirm(Request $request, Exam $exam)
{
    $questions = Cache::pull("import_exam_{$exam->id}_questions", []);
    $created = 0;

    foreach ($questions as $q) {
        $exam->questions()->create([
            'question_text' => $q['question'],
            'options' => json_encode($q['options']),
            'correct_answer' => $q['answer'],
            'points' => 1,
        ]);
        $created++;
    }

    return redirect()->route('admin.exams.edit', $exam)->with('success', "$created questions imported successfully.");
}


public function importPreview(Request $request, Exam $exam)
{
    $request->validate([
        'docx_file' => 'required|file|mimes:docx'
    ]);

    // Save the file locally
    $path = $request->file('docx_file')->store('temp', 'public');
$fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
    Log::error("DOCX file not found at: {$fullPath}");
    return back()->withErrors(['docx_file' => 'Error processing the uploaded file. Please try again.']);
}
    

    $result = $this->extractQuestionsFromInlineOptions($fullPath);

    // Cache the parsed data for next step (confirm)
    Cache::put("import_exam_{$exam->id}_questions", $result['questions'], now()->addMinutes(15));
    Cache::put("import_exam_{$exam->id}_skipped", $result['skipped'], now()->addMinutes(15));

    return view('admin.lms.exams.import_preview', [
        'exam' => $exam,
        'questions' => $result['questions'],
        'skipped' => $result['skipped']
    ]);
}

}