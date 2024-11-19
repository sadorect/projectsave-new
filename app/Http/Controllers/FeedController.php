<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Response;

class FeedController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->take(20)->get();
        
        $content = view('feed.index', compact('posts'));
        
        return Response::make($content, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
}
