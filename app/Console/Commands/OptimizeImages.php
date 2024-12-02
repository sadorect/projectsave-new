<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\Facades\Image;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize';
    protected $description = 'Optimize website images';

    public function handle()
    {
        $paths = [
            'frontend/img/carousel-1.jpg',
            'frontend/img/carousel-2.jpg',
            'frontend/img/carousel-3.jpg',
            'frontend/img/about.jpg',
            'frontend/img/donate.jpeg'
        ];

        foreach ($paths as $path) {
            $image = Image::make(public_path($path));
            
            // Resize large images while maintaining aspect ratio
            if ($image->width() > 1200) {
                $image->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Convert to WebP with 80% quality
            $webpPath = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $path);
            $image->save(public_path($webpPath), 80, 'webp');
        }

        $this->info('Images optimized successfully!');
    }
}
