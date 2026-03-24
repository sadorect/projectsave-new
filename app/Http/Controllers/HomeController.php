<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Event;
use App\Models\VideoReel;
use App\Models\NewsUpdate;

class HomeController extends Controller
{
public function index()
    {

    $newsUpdates = NewsUpdate::where('is_active', true)
        ->orderBy('date', 'desc')
        ->take(5)
        ->get();
        
    $videoReels = VideoReel::where('is_active', true)
        ->orderBy('display_order')
        ->get();

    $latestEvents = Event::upcoming()
        ->take(3)
        ->get();
         
    $posts = Post::with('categories')
        ->published()
        ->orderBy('published_at', 'desc')
        ->take(3)
        ->get();

  
    return view('home.index', compact('latestEvents','newsUpdates', 'videoReels', 'posts'));
    }

}
