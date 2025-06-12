<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminFileController extends Controller
{
    public function index(Request $request)
    {
        $query = UserFile::with('user');

        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('mime_type')) {
            $query->where('mime_type', 'like', $request->mime_type . '%');
        }

        if ($request->filled('is_private')) {
            $query->where('is_private', $request->boolean('is_private'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('original_name', 'like', '%' . $request->search . '%');
        }

        $files = $query->latest()->paginate(20)->withQueryString();

        // Get filter options
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        $categories = UserFile::distinct()->pluck('category')->filter();
        $mimeTypes = UserFile::distinct()->pluck('mime_type')
            ->map(fn($type) => explode('/', $type)[0])
            ->unique()
            ->filter();

        // Statistics
        $stats = $this->getFileStatistics();

        return view('admin.files.index', compact('files', 'users', 'categories', 'mimeTypes', 'stats'));
    }

    public function show(UserFile $file)
    {
        $file->load('user');
        
        // Get file info
        $fileExists = Storage::disk('private')->exists($file->path);
        $fileSize = $fileExists ? Storage::disk('private')->size($file->path) : 0;
        
        return view('admin.files.show', compact('file', 'fileExists', 'fileSize'));
    }

    public function download(UserFile $file)
    {
        if (!Storage::disk('private')->exists($file->path)) {
            abort(404, 'File not found on disk');
        }

        // Log admin download
        activity()
            ->causedBy(auth()->user())
            ->performedOn($file)
            ->log('Admin downloaded file');

        return Storage::disk('private')->download($file->path, $file->original_name);
    }

    public function destroy(UserFile $file)
    {
      // Delete from storage
      if (Storage::disk('private')->exists($file->path)) {
        Storage::disk('private')->delete($file->path);
    }

    // Delete from database
    $file->delete();
        // Ensure the file is deleted
        if (Storage::disk('private')->exists($file->path)) {
            return back()->withErrors(['error' => 'Failed to delete file from storage']);
        }
        // Ensure the file is deleted from the database
        if (UserFile::find($file->id)) {
            return back()->withErrors(['error' => 'Failed to delete file from database']);
        }
        // Ensure the file is deleted from the activity log
       
        // Log deletion
        activity()
            ->causedBy(auth()->user())
            ->performedOn($file)
            ->withProperties([
                'file_name' => $file->original_name,
                'file_owner' => $file->user->name,
                'file_size' => $file->size,
            ])
            ->log('Admin deleted user file');

        

        return back()->with('success', 'File deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'exists:user_files,id'
        ]);

        $files = UserFile::whereIn('id', $request->file_ids)->get();
        $deletedCount = 0;

        foreach ($files as $file) {
            // Delete from storage
            if (Storage::disk('private')->exists($file->path)) {
                Storage::disk('private')->delete($file->path);
            }

            // Log deletion
            activity()
                ->causedBy(auth()->user())
                ->performedOn($file)
                ->withProperties([
                    'file_name' => $file->original_name,
                    'file_owner' => $file->user->name,
                ])
                ->log('Admin bulk deleted user file');

            $file->delete();
            $deletedCount++;
        }

        return back()->with('success', "{$deletedCount} files deleted successfully");
    }

    public function updatePrivacy(UserFile $file, Request $request)
    {
        $request->validate([
            'is_private' => 'required|boolean'
        ]);

        $oldStatus = $file->is_private ? 'private' : 'public';
        $newStatus = $request->boolean('is_private') ? 'private' : 'public';

        $file->update(['is_private' => $request->boolean('is_private')]);



        \Spatie\Activitylog\Facades\Activity::causedBy(auth()->user())
            ->performedOn($file)
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ])
            ->log('Admin changed file privacy');

        return back()->with('success', 'File privacy updated successfully');
    }

    public function cleanupExpired()
    {
        $expiredFiles = UserFile::where('expires_at', '<', now())->get();
        $deletedCount = 0;

        foreach ($expiredFiles as $file) {
            if (Storage::disk('private')->exists($file->path)) {
                Storage::disk('private')->delete($file->path);
            }
            $file->delete();
            $deletedCount++;
        }

        return back()->with('success', "{$deletedCount} expired files cleaned up");
    }

    public function storageAnalysis()
    {
        $analysis = [
            'total_files' => UserFile::count(),
            'total_size' => UserFile::sum('size'),
            'by_user' => UserFile::selectRaw('user_id, users.name, COUNT(*) as file_count, SUM(size) as total_size')
                ->join('users', 'users.id', '=', 'user_files.user_id')
                ->groupBy('user_id', 'users.name')
                ->orderByDesc('total_size')
                ->limit(10)
                ->get(),
            'by_category' => UserFile::selectRaw('category, COUNT(*) as file_count, SUM(size) as total_size')
                ->groupBy('category')
                ->orderByDesc('total_size')
                ->get(),
            'by_type' => UserFile::selectRaw('mime_type, COUNT(*) as file_count, SUM(size) as total_size')
                ->groupBy('mime_type')
                ->orderByDesc('file_count')
                ->get(),
            'monthly_uploads' => UserFile::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count, SUM(size) as size')
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ];

        return view('admin.files.analysis', compact('analysis'));
    }

    private function getFileStatistics(): array
    {
        return [
            'total_files' => UserFile::count(),
            'total_size' => $this->formatBytes(UserFile::sum('size')),
            'total_users_with_files' => UserFile::distinct('user_id')->count(),
            'files_today' => UserFile::whereDate('created_at', today())->count(),
            'files_this_week' => UserFile::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'files_this_month' => UserFile::whereMonth('created_at', now()->month)->count(),
            'private_files' => UserFile::where('is_private', true)->count(),
            'public_files' => UserFile::where('is_private', false)->count(),
            'expired_files' => UserFile::where('expires_at', '<', now())->count(),
        ];
    }

    private function formatBytes($size, $precision = 2): string
    {
        if ($size == 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = log($size, 1024);
        
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
    }

}