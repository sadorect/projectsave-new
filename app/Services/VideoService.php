<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoService
{
    public function handleVideo($video, $courseId, $lessonId)
    {
        if ($video instanceof UploadedFile) {
            return $this->storeVideo($video, $courseId, $lessonId);
        }
        
        return $this->validateExternalUrl($video);
    }

    private function storeVideo(UploadedFile $video, $courseId, $lessonId)
    {
        $path = "videos/courses/{$courseId}/lessons/{$lessonId}";
        $storedPath = FileUploadService::uploadVideo($video, $path, 'public', ['mp4', 'mov', 'ogg', 'webm'], 'video');
        
        return [
            'video_url' => Storage::url($storedPath),
            'video_type' => 'file'
        ];
    }

    private function validateExternalUrl($url)
    {
        // Validate YouTube URL
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return [
                'video_url' => $url,
                'video_type' => 'url'
            ];
        }

        // Validate Vimeo URL
        if (str_contains($url, 'vimeo.com')) {
            return [
                'video_url' => $url,
                'video_type' => 'url'
            ];
        }

        throw new \Exception('Invalid video URL format');
    }
}
