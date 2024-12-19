<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $filename = Str::random(40) . '.' . $video->getClientOriginalExtension();
        $path = "videos/courses/{$courseId}/lessons/{$lessonId}";
        
        $video->storeAs($path, $filename, 'public');
        
        return [
            'video_url' => Storage::url($path . '/' . $filename),
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
