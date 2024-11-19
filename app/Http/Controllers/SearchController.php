<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Event;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        
        $posts = Post::where('title', 'like', "%{$query}%")
            ->orWhere('details', 'like', "%{$query}%")
            ->get();
            
        $events = Event::where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();
            
        return view('search.index', compact('posts', 'events', 'query'));
    }
}
