<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoReel;
use Illuminate\Http\Request;

class VideoReelController extends Controller
{
    public function index()
    {
        $videos = VideoReel::orderBy('display_order')->get();
        return view('admin.videos.index', compact('videos'));
    }

    public function create()
    {
        return view('admin.videos.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'youtube_id' => 'required',
            'display_order' => 'integer',
            'is_active' => 'sometimes'
        ]);

        // Set is_active to false if not checked
        $validated['is_active'] = $request->has('is_active');

        VideoReel::create($validated);
        return redirect()->route('videos.index')->with('success', 'Video added successfully');
    
    }

    public function edit(VideoReel $video)
    {
        return view('admin.videos.edit', compact('video'));
    }
    public function update(Request $request, VideoReel $video)
    {
        $validated = $request->validate([
            'title' => 'required',
            'youtube_id' => 'required',
            'display_order' => 'integer',
            'is_active' => 'sometimes'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $video->update($validated);
        return redirect()->route('videos.index')->with('success', 'Video modified successfully');
    
    }

    public function destroy(VideoReel $video)
    {
        $video->delete();
        return redirect()->route('videos.index')->with('success', 'Video deleted successfully');
    }
}
