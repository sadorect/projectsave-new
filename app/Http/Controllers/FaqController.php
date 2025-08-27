<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('details', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Sort options
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->oldest('created_at');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'status':
                $query->orderBy('status', 'asc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->latest('created_at');
        }
        
        $faqs = $query->paginate(10)->appends($request->query());
        
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

    /**
     * Bulk actions for FAQs
     */
    public function bulkAction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:delete,change_status',
                'faq_ids' => 'required|array|min:1',
                'faq_ids.*' => 'exists:faqs,id',
                'status' => 'nullable|in:draft,published'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->with('error', 'Invalid bulk action parameters.');
            }

            $faqIds = $request->faq_ids;
            $action = $request->action;
            $status = $request->status;

            DB::beginTransaction();

            switch ($action) {
                case 'delete':
                    Faq::whereIn('id', $faqIds)->delete();
                    $message = count($faqIds) . ' FAQs deleted successfully.';
                    break;

                case 'change_status':
                    if (!$status) {
                        return redirect()->back()->with('error', 'Status is required for this action.');
                    }
                    
                    Faq::whereIn('id', $faqIds)->update(['status' => $status]);
                    $statusLabel = ucfirst($status);
                    $message = count($faqIds) . " FAQs status changed to '{$statusLabel}'.";
                    break;

                default:
                    throw new \Exception('Invalid action');
            }

            DB::commit();
            return redirect()->route('admin.faqs.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('FAQ bulk action error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Bulk action failed. Please try again.');
        }
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
