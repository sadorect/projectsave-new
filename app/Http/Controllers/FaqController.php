<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::latest()->paginate(10);
        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'details' => 'required',
            'status' => 'required|in:draft,published'
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        Faq::create($validated);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ created successfully');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'details' => 'required',
            'status' => 'required|in:draft,published'
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        $faq->update($validated);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated successfully');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted successfully');
    }

    public function show($slug)
    {
    $faq = Faq::where('slug', $slug)
              ->where('status', 'published')
              ->firstOrFail();
              
    $previous = Faq::where('status', 'published')
                   ->where('created_at', '<', $faq->created_at)
                   ->orderBy('created_at', 'desc')
                   ->first();
                   
    $next = Faq::where('status', 'published')
               ->where('created_at', '>', $faq->created_at)
               ->orderBy('created_at', 'asc')
               ->first();
    
    return view('pages.faqs.show', compact('faq', 'previous', 'next'));
    }


    public function list()
    {
        $faqs = Faq::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(6);
        
        return view('pages.faqs.list', compact('faqs'));
    }
  }
