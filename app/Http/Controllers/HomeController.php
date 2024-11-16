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

    $latestEvents = Event::latest()->take(2)->get();
         
    $posts = Post::latest()->take(3)->get();

  
    return view('home.index', compact('latestEvents','newsUpdates', 'videoReels', 'posts'));
    }

}