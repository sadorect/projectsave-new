<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsUpdate;
use Illuminate\Http\Request;

class NewsUpdateController extends Controller
{
    public function index()
    {
        $updates = NewsUpdate::orderBy('date', 'desc')->get();
        return view('admin.news.index', compact('updates'));
    }

    public function create()
    {
        return view('admin.news.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'date' => 'required|date',
            'is_active' => 'sometimes'
        ]);

        $validated['is_active'] = $request->has('is_active');

        NewsUpdate::create($validated);
        return redirect()->route('news.index')->with('success', 'News update created successfully');
    }

    public function edit(NewsUpdate $news)
    {
        return view('admin.news.edit', compact('news'));
    }
    public function update(Request $request, NewsUpdate $news)
    {
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'date' => 'required|date',
            'is_active' => 'sometimes'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $news->update($validated);
        return redirect()->route('news.index')->with('success', 'News update modified successfully');
    
    }

    public function destroy(NewsUpdate $news)
    {
        $news->delete();
        return redirect()->route('news.index')->with('success', 'News update deleted successfully');
    }
}
