<?php

namespace App\Http\Controllers;

use App\Models\UserFile;
use App\Services\FileManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileManagerController extends Controller
{
    public function __construct(
        private FileManagerService $fileManager
    ) {}

    public function index(Request $request)
    {
        $files = auth()->user()->files()
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->when($request->search, fn($q) => $q->where('original_name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20);

        return view('user.files.index', compact('files'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // 10MB
            'category' => 'nullable|string|max:50',
            'is_private' => 'boolean',
        ]);

        $uploadedFiles = [];
        
        foreach ($request->file('files') as $file) {
            try {
                $uploadedFiles[] = $this->fileManager->uploadFile(
                    $file,
                    auth()->id(),
                    [
                        'category' => $request->category,
                        'is_private' => $request->boolean('is_private', true),
                    ]
                );
            } catch (\Exception $e) {
                return back()->withErrors(['upload' => "Failed to upload {$file->getClientOriginalName()}: {$e->getMessage()}"]);
            }
        }

        return back()->with('success', count($uploadedFiles) . ' file(s) uploaded successfully');
    }

    public function download(UserFile $file)
    {
        // Security check
        if (!$this->canAccessFile($file)) {
            abort(403);
        }

        if ($file->isExpired()) {
            abort(410, 'File has expired');
        }

        return Storage::disk('private')->download(
            $file->path,
            $file->original_name
        );
    }

    public function destroy(UserFile $file)
    {
        if ($file->user_id !== auth()->id()) {
            abort(403);
        }

        Storage::disk('private')->delete($file->path);
        $file->delete();

        return back()->with('success', 'File deleted successfully');
    }

    private function canAccessFile(UserFile $file): bool
    {
        // Owner can always access
        if ($file->user_id === auth()->id()) {
            return true;
        }

        // Admin can access all files
        if (auth()->user()->hasRole('admin')) {
            return true;
        }

        // Public files can be accessed by authenticated users
        if (!$file->is_private && auth()->check()) {
            return true;
        }

        return false;
    }
}