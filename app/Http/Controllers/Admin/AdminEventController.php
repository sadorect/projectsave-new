<?php

namespace App\Http\Controllers\Admin;

use App\Services\FileUploadService;
use App\Services\HtmlSanitizer;
use App\Models\Event;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:viewAny,' . Event::class)->only('index');
        $this->middleware('can:create,' . Event::class)->only(['create', 'store']);
        $this->middleware('can:update,event')->only(['edit', 'update']);
        $this->middleware('can:delete,event')->only('destroy');
    }

    public function index()
    {
        $events = Event::latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'details' => 'required',
            'location' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable|after_or_equal:start_time'
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();
        $validated['description'] = HtmlSanitizer::clean($validated['description']);
        $validated['details'] = HtmlSanitizer::clean($validated['details']);

        if ($request->hasFile('image')) {
            $validated['image'] = FileUploadService::uploadImage(
                $request->file('image'),
                'events',
                'public',
                ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'image'
            );
        }

        Event::create($validated);
        return redirect()->route('admin.events.index')->with('success', 'Event created successfully');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'details' => 'required',
            'location' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable|after_or_equal:start_time'
        ]);

        $validated['description'] = HtmlSanitizer::clean($validated['description']);
        $validated['details'] = HtmlSanitizer::clean($validated['details']);

        if ($request->hasFile('image')) {
            $validated['image'] = FileUploadService::uploadImage(
                $request->file('image'),
                'events',
                'public',
                ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'image'
            );
        }

        $event->update($validated);
        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully');
    }
}
