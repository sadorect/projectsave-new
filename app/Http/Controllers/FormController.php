<?php
namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\FormSubmissionNotification;

class FormController extends Controller
{
    // Admin: List forms
    public function index()
    {
        $forms = Form::latest()->paginate(10);
        return view('admin.forms.index', compact('forms'));
    }

    // Admin method for admin routes
    public function adminIndex()
    {
        $forms = Form::latest()->paginate(10);
        return view('admin.forms.index', compact('forms'));
    }

    // Admin: Create form
    public function create()
    {
        return view('admin.forms.create');
    }

    // Admin: Store form
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,textarea,select,checkbox,radio,email,number,date',
            'fields.*.required' => 'boolean',
            'fields.*.options' => 'nullable|array',
            'notify_emails' => 'nullable|array',
            'notify_emails.*' => 'email',
            'require_login' => 'boolean',
        ]);

        // Add names to fields if not provided
        $fields = $validated['fields'];
        foreach ($fields as $index => &$field) {
            if (!isset($field['name']) || empty($field['name'])) {
                $field['name'] = 'field_' . $index . '_' . time();
            }
        }

        try {
            Form::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'fields' => $fields,
                'require_login' => $validated['require_login'] ?? false,
                'notify_emails' => $validated['notify_emails'] ?? [],
            ]);
            return redirect()->route('admin.forms.index')->with('success', 'Form created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create form: ' . $e->getMessage());
        }
    }
    // Admin: Edit form
    public function edit(Form $form)
    {
        return view('admin.forms.edit', compact('form'));
    }       

    // Admin: Update form
    public function update(Request $request, Form $form)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,textarea,select,checkbox,radio,email,number,date',
            'fields.*.required' => 'boolean',
            'fields.*.options' => 'nullable|array',
            'notify_emails' => 'nullable|array',
            'notify_emails.*' => 'email',
            'require_login' => 'boolean',
        ]); 

        // Add names to fields if not provided
        $fields = $validated['fields'];
        foreach ($fields as $index => &$field) {
            if (!isset($field['name']) || empty($field['name'])) {
                $field['name'] = 'field_' . $index . '_' . time();
            }
        }

        try {
            $form->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'fields' => $fields,
                'require_login' => $validated['require_login'] ?? false,
                'notify_emails' => $validated['notify_emails'] ?? [],
            ]);
            return redirect()->route('admin.forms.index')->with('success', 'Form updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update form: ' . $e->getMessage());
        }
    }

    // Admin: Delete form
    public function destroy(Form $form)
    {
        try {
            // Delete associated submissions first
            $form->submissions()->delete();
            $form->delete();
            return redirect()->route('admin.forms.index')->with('success', 'Form and all submissions deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete form: ' . $e->getMessage());
        }
    }


    // Public: Show form
    public function show(Form $form)
    {
        if ($form->require_login && !Auth::check()) {
            return redirect()->route('login')->with('warning', 'Please login to access this form.');
        }
        return view('admin.forms.public.show', compact('form'));
    }

    // Public: Submit form
    public function submit(Request $request, Form $form)
    {
        if ($form->require_login && !Auth::check()) {
            return redirect()->route('login')->with('warning', 'Please login to submit this form.');
        }

        // Always validate math captcha on public form submissions
        $request->validate([
            'math_captcha' => ['required', new \App\Rules\MathCaptchaRule],
        ]);

        // Dynamic validation
        $rules = [];
        $fields = $form->fields ?? [];
        foreach ($fields as $field) {
            if (!isset($field['name']) || empty($field['name'])) {
                continue; // Skip fields without names
            }
            
            $rule = [];
            if (isset($field['required']) && $field['required']) {
                $rule[] = 'required';
            }
            
            switch ($field['type'] ?? 'text') {
                case 'email': 
                    $rule[] = 'email'; 
                    break;
                case 'number': 
                    $rule[] = 'numeric'; 
                    break;
                case 'date': 
                    $rule[] = 'date'; 
                    break;
            }
            
            if (!empty($rule)) {
                $rules[$field['name']] = $rule;
            }
        }
        
        $validated = $request->validate($rules);

        try {
            $submission = FormSubmission::create([
                'form_id' => $form->id,
                'user_id' => Auth::id(),
                'data' => $validated,
            ]);

            // Notify managers
            if (!empty($form->notify_emails)) {
                foreach ($form->notify_emails as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::to($email)->send(new FormSubmissionNotification($form, $submission));
                        } catch (\Exception $e) {
                            // Log email error but don't fail the submission
                            Log::error('Failed to send form notification email to ' . $email . ': ' . $e->getMessage());
                        }
                    }
                }
            }

            return redirect()->back()->with('success', 'Form submitted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to submit form: ' . $e->getMessage());
        }
    }

    // Admin: List all submissions (index view)
    public function submissionsIndex()
    {
        $submissions = FormSubmission::with('form')->latest()->paginate(20);
        return view('admin.forms.submissions_index', compact('submissions'));
    }

    // Admin: View submissions for a specific form
    public function submissions(Form $form)
    {
        if (!$form) {
            abort(404, 'Form not found.');
        }
        $submissions = $form->submissions()->latest()->paginate(20);
        return view('admin.forms.submissions', compact('form', 'submissions'));
    }


    // Admin: Download submissions as CSV
    public function downloadSubmissions(Form $form)
    {
        $submissions = $form->submissions()->with('user')->get();

        if ($submissions->isEmpty()) {
            return redirect()->back()->with('error', 'No submissions found for this form.');
        }

        $csvFileName = 'form_submissions_' . $form->id . '_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($submissions, $form) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            $csvHeaders = ['ID', 'Submitted At', 'User Name', 'User Email'];
            
            // Add form field headers
            if (!empty($form->fields)) {
                foreach ($form->fields as $field) {
                    $csvHeaders[] = $field['label'] ?? 'Unknown Field';
                }
            }
            
            fputcsv($file, $csvHeaders);

            // CSV Data
            foreach ($submissions as $submission) {
                $row = [
                    $submission->id,
                    $submission->created_at->format('Y-m-d H:i:s'),
                    $submission->user->name ?? 'Guest',
                    $submission->user->email ?? 'N/A'
                ];
                
                // Add form field data
                if (!empty($form->fields)) {
                    foreach ($form->fields as $field) {
                        $fieldName = $field['name'] ?? '';
                        $value = $submission->data[$fieldName] ?? '';
                        
                        // Handle array values (for checkboxes, multi-select)
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        
                        $row[] = $value;
                    }
                }
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}