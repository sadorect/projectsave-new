<?php

namespace App\Services;

use App\Models\UserFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManagerService
{
    private array $allowedMimeTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf', 'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain', 'text/csv'
    ];

    private int $maxFileSize = 10485760; // 10MB

    public function uploadFile(UploadedFile $file, int $userId, array $options = []): UserFile
    {
        $this->validateFile($file);

        $filename = $this->generateSecureFilename($file);
        $path = "user-files/{$userId}/" . date('Y/m/');
        
        // Store file with private visibility
        $storedPath = Storage::disk('private')->putFileAs(
            $path, 
            $file, 
            $filename
        );

        return UserFile::create([
            'user_id' => $userId,
            'original_name' => $file->getClientOriginalName(),
            'filename' => $filename,
            'path' => $storedPath,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'category' => $options['category'] ?? 'general',
            'is_private' => $options['is_private'] ?? true,
            'metadata' => $this->extractMetadata($file),
            'expires_at' => $options['expires_at'] ?? null,
        ]);
    }

    private function validateFile(UploadedFile $file): void
    {
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            throw new \InvalidArgumentException('File type not allowed');
        }

        if ($file->getSize() > $this->maxFileSize) {
            throw new \InvalidArgumentException('File size exceeds limit');
        }
    }

    private function generateSecureFilename(UploadedFile $file): string
    {
        return Str::random(40) . '.' . $file->getClientOriginalExtension();
    }

    private function extractMetadata(UploadedFile $file): array
    {
        $metadata = [];
        
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $imageInfo = getimagesize($file->getPathname());
            if ($imageInfo) {
                $metadata['dimensions'] = [
                    'width' => $imageInfo[0],
                    'height' => $imageInfo[1]
                ];
            }
        }

        return $metadata;
    }
}