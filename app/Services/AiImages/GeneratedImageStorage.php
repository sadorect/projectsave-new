<?php

namespace App\Services\AiImages;

use App\Services\FileUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use RuntimeException;

class GeneratedImageStorage
{
    /**
     * @return array{path:string, mime_type:string, width:int, height:int}
     */
    public function store(string $content, string $mimeType, string $directoryPrefix = 'post'): array
    {
        $imageInfo = @getimagesizefromstring($content);

        if ($imageInfo === false) {
            throw new RuntimeException('Generated image payload is not a valid image.');
        }

        if (!in_array($mimeType, FileUploadService::IMAGE_MIME_TYPES, true) && !str_starts_with($mimeType, 'image/')) {
            throw new RuntimeException('Generated image MIME type is not allowed.');
        }

        $targetFormat = (string) config('ai-images.target_format', 'webp');
        $quality = (int) config('ai-images.target_quality', 85);
        $maxWidth = (int) config('ai-images.max_width', 1536);
        $maxHeight = (int) config('ai-images.max_height', 1024);
        $disk = (string) config('ai-images.storage_disk', config('filesystems.default'));
        $basePath = trim((string) config('ai-images.storage_path', 'posts/generated'), '/');

        $image = Image::make($content)->orientate();
        $image->resize($maxWidth, $maxHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $encoded = (string) $image->encode($targetFormat, $quality);
        $extension = $targetFormat === 'jpeg' ? 'jpg' : $targetFormat;
        $path = $basePath . '/' . trim($directoryPrefix, '/') . '-' . Str::uuid() . '.' . $extension;

        Storage::disk($disk)->put($path, $encoded, 'public');

        return [
            'path' => $path,
            'mime_type' => 'image/' . $targetFormat,
            'width' => $image->width(),
            'height' => $image->height(),
        ];
    }
}