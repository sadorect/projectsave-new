<?php

namespace App\Services;

use App\Contracts\ScansUploadedFiles;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    public const IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
    ];

    public const DOCUMENT_MIME_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];

    public const VIDEO_MIME_TYPES = [
        'video/mp4',
        'video/quicktime',
        'video/ogg',
        'application/ogg',
        'video/webm',
    ];

    /**
     * Upload a validated file to the configured storage disk.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $disk
     * @param array<string> $allowedMimeTypes
     * @param array<string> $allowedExtensions
     * @param string $attribute
     * @return string
     */
    public static function uploadFile(
        UploadedFile $file,
        string $directory,
        string $disk = null,
        array $allowedMimeTypes = [],
        array $allowedExtensions = [],
        string $attribute = 'file'
    ): string
    {
        $disk = $disk ?? config('filesystems.default');

        self::validateUploadedFile($file, $allowedMimeTypes, $allowedExtensions, $attribute);
        app(ScansUploadedFiles::class)->scan($file, $attribute);

        // Generate a unique filename
        $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension());
        $filename = Str::uuid() . '.' . $extension;
        
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

    public static function uploadImage(
        UploadedFile $file,
        string $directory,
        string $disk = null,
        array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        string $attribute = 'file'
    ): string {
        return self::uploadFile($file, $directory, $disk, self::IMAGE_MIME_TYPES, $allowedExtensions, $attribute);
    }

    public static function uploadDocument(
        UploadedFile $file,
        string $directory,
        string $disk = null,
        array $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
        string $attribute = 'file'
    ): string {
        return self::uploadFile($file, $directory, $disk, self::DOCUMENT_MIME_TYPES, $allowedExtensions, $attribute);
    }

    public static function uploadVideo(
        UploadedFile $file,
        string $directory,
        string $disk = null,
        array $allowedExtensions = ['mp4', 'mov', 'ogg', 'webm'],
        string $attribute = 'file'
    ): string {
        return self::uploadFile($file, $directory, $disk, self::VIDEO_MIME_TYPES, $allowedExtensions, $attribute);
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

    /**
     * Validate MIME type and extension using the uploaded file metadata.
     *
     * @param array<string> $allowedMimeTypes
     * @param array<string> $allowedExtensions
     */
    public static function validateUploadedFile(
        UploadedFile $file,
        array $allowedMimeTypes = [],
        array $allowedExtensions = [],
        string $attribute = 'file'
    ): void {
        if (!$file->isValid()) {
            throw ValidationException::withMessages([
                $attribute => 'The uploaded file is invalid.',
            ]);
        }

        $serverMime = $file->getMimeType();
        $clientMime = $file->getClientMimeType();
        $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension());

        if (
            $allowedMimeTypes !== []
            && !in_array($serverMime, $allowedMimeTypes, true)
            && !in_array($clientMime, $allowedMimeTypes, true)
        ) {
            throw ValidationException::withMessages([
                $attribute => 'The uploaded file type is not allowed.',
            ]);
        }

        if ($allowedExtensions !== [] && !in_array($extension, $allowedExtensions, true)) {
            throw ValidationException::withMessages([
                $attribute => 'The uploaded file extension is not allowed.',
            ]);
        }
    }
}
