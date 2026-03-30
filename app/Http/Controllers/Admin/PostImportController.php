<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessDevotionalImportSession;
use App\Models\DevotionalImportSession;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create,' . Post::class);
        $this->middleware('can:manageTaxonomy,' . Post::class);
    }

    public function create(Request $request): View
    {
        $selectedSession = null;

        if ($request->filled('session')) {
            $selectedSession = DevotionalImportSession::query()
                ->with('user')
                ->find($request->integer('session'));
        }

        $recentSessions = DevotionalImportSession::query()
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        if (! $selectedSession) {
            $selectedSession = $recentSessions->first();
        } elseif (! $recentSessions->contains(fn (DevotionalImportSession $session) => $session->is($selectedSession))) {
            $recentSessions->prepend($selectedSession);
        }

        $hasActiveSessions = $recentSessions->contains(
            fn (DevotionalImportSession $session) => $session->isActive()
        );

        return view('admin.posts.import', [
            'recentSessions' => $recentSessions,
            'selectedSession' => $selectedSession,
            'hasActiveSessions' => $hasActiveSessions,
            'queueConnection' => (string) config('queue.default', 'sync'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_file' => 'required|file|mimes:txt|max:20480',
            'duplicate_strategy' => 'required|in:update,skip',
        ]);

        $uploadedFile = $request->file('source_file');
        $storedFilename = Str::uuid() . '.txt';
        $storedPath = $uploadedFile->storeAs('devotional-imports', $storedFilename, 'local');

        if (! $storedPath) {
            return back()
                ->withInput()
                ->with('error', 'The uploaded devotional file could not be stored. Please try again.');
        }

        $session = DevotionalImportSession::query()->create([
            'user_id' => $request->user()->getKey(),
            'source_disk' => 'local',
            'source_path' => $storedPath,
            'source_filename' => $uploadedFile->getClientOriginalName(),
            'source_checksum' => hash_file('sha256', Storage::disk('local')->path($storedPath)) ?: null,
            'duplicate_strategy' => $validated['duplicate_strategy'],
            'status' => DevotionalImportSession::STATUS_QUEUED,
            'created_categories' => [],
            'category_counts' => [],
            'failures' => [],
            'queued_at' => now(),
            'last_activity_at' => now(),
        ]);

        ProcessDevotionalImportSession::dispatch($session->getKey());

        return redirect()
            ->route('admin.posts.import.create', ['session' => $session->getKey()])
            ->with('success', 'Devotional import queued successfully. Progress will update here as the worker processes the archive.');
    }

    public function resume(DevotionalImportSession $session): RedirectResponse
    {
        if ($session->status === DevotionalImportSession::STATUS_COMPLETED) {
            return redirect()
                ->route('admin.posts.import.create', ['session' => $session->getKey()])
                ->with('info', 'This devotional import session has already completed.');
        }

        if ($session->status === DevotionalImportSession::STATUS_PROCESSING && ! $session->isStale()) {
            return redirect()
                ->route('admin.posts.import.create', ['session' => $session->getKey()])
                ->with('info', 'This devotional import is already being processed.');
        }

        if (! Storage::disk($session->source_disk)->exists($session->source_path)) {
            return redirect()
                ->route('admin.posts.import.create', ['session' => $session->getKey()])
                ->with('error', 'The uploaded devotional source file is missing, so this session cannot be resumed.');
        }

        $session->forceFill([
            'status' => DevotionalImportSession::STATUS_QUEUED,
            'queued_at' => now(),
            'finished_at' => null,
            'last_error' => null,
            'last_activity_at' => now(),
        ])->save();

        ProcessDevotionalImportSession::dispatch($session->getKey());

        return redirect()
            ->route('admin.posts.import.create', ['session' => $session->getKey()])
            ->with('success', 'Devotional import resumed from the last saved checkpoint.');
    }
}
