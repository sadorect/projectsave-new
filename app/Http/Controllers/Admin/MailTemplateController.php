<?php

namespace App\Http\Controllers\Admin;

use App\Models\MailTemplate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MailTemplateController extends Controller
{
    public function index()
    {
        $templates = MailTemplate::latest()->paginate(10);
        return view('admin.mail.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.mail.templates.create');
    }

    public function store(Request $request)
    { 
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|string'
        ]);
        if ($request->variables) {
          $validated['variables'] = array_map('trim', explode(',', $request->variables));
        }
      //dd($validated);
        MailTemplate::create($validated);
       
        return redirect()->route('admin.mail-templates.index')->with('success', 'Template created successfully');
    }

    public function edit(MailTemplate $mailTemplate)
    {
        return view('admin.mail.templates.edit', compact('mailTemplate'));
    }

    public function update(Request $request, MailTemplate $mailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|string'
        ]);
        if ($request->variables) {
          $validated['variables'] = array_map('trim', explode(',', $request->variables));
        }
        $mailTemplate->update($validated);
        return redirect()->route('admin.mail-templates.index')->with('success', 'Template updated successfully');
    }
}
