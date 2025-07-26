<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload a file to S3 storage
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $disk
     * @return string
     */
    public static function uploadFile(UploadedFile $file, string $directory, string $disk = null): string
    {
        $disk = $disk ?? config('filesystems.default');
        
        // Generate a unique filename
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        // Store the file in the specified directory
        $path = $file->storeAs($directory, $filename, $disk);
        
        return $path;
    }

    /**
     * Upload multiple files
     *
     * @param array $files
     * @param string $directory
     * @param string|null $disk
     * @return array
     */
    public static function uploadMultipleFiles(array $files, string $directory, string $disk = null): array
    {
        $uploadedPaths = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadedPaths[] = self::uploadFile($file, $directory, $disk);
            }
        }
        
        return $uploadedPaths;
    }

    /**
     * Delete a file from storage
     *
     * @param string $path
     * @param string|null $disk
     * @return bool
     */
    public static function deleteFile(string $path, string $disk = null): bool
    {
        $disk = $disk ?? config('filesystems.default');
        
        return Storage::disk($disk)->delete($path);
    }

    /**
     * Get the full URL for a stored file
     *
     * @param string $path
     * @param string|null $disk
     * @return string
     */
    public static function getFileUrl(string $path, string $disk = null): string
    {
        $disk = $disk ?? config('filesystems.default');
        
        return Storage::disk($disk)->url($path);
    }

    /**
     * Check if a file exists in storage
     *
     * @param string $path
     * @param string|null $disk
     * @return bool
     */
    public static function fileExists(string $path, string $disk = null): bool
    {
        $disk = $disk ?? config('filesystems.default');
        
        return Storage::disk($disk)->exists($path);
    }
}
