<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Event;
use App\Models\Faq;
use App\Models\MinistryReport;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        if ($query === '') {
            return view('search.index', [
                'posts' => collect(),
                'events' => collect(),
                'faqs' => collect(),
                'reports' => collect(),
                'query' => $query,
            ]);
        }

        $posts = Post::published()
            ->where(function ($builder) use ($query) {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhere('details', 'like', "%{$query}%");
            })
            ->get();
            
        $events = Event::query()
            ->where(function ($builder) use ($query) {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->get();

        $faqs = Faq::published()
            ->where(function ($builder) use ($query) {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhere('details', 'like', "%{$query}%");
            })
            ->get();

        $reports = MinistryReport::published()
            ->where(function ($builder) use ($query) {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhere('details', 'like', "%{$query}%")
                    ->orWhere('location', 'like', "%{$query}%");
            })
            ->get();
            
        return view('search.index', compact('posts', 'events', 'query', 'faqs', 'reports'));
    }
}
