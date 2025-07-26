<?php

namespace App\Console\Commands;

use App\Models\Course;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateCourseImagesToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:migrate-images-to-s3 {--dry-run : Show what would be migrated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing course images to S3 with proper LMS folder structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $courses = Course::whereNotNull('featured_image')
                        ->where('featured_image', '!=', '')
                        ->get();

        if ($courses->isEmpty()) {
            $this->info('No courses with images found.');
            return;
        }

        $this->info("Found {$courses->count()} courses with images");

        foreach ($courses as $course) {
            $this->processCourseImage($course, $dryRun);
        }

        if ($dryRun) {
            $this->info('DRY RUN COMPLETED - Run without --dry-run to execute changes');
        } else {
            $this->info('Migration completed!');
        }
    }

    private function processCourseImage(Course $course, bool $dryRun)
    {
        $currentPath = $course->featured_image;
        
        // Skip if already an S3 URL
        if (str_contains($currentPath, 's3.amazonaws.com') || str_contains($currentPath, config('filesystems.disks.s3.bucket'))) {
            $this->line("Course '{$course->title}' already has S3 image: {$currentPath}");
            return;
        }

        // Handle local storage paths
        if (str_starts_with($currentPath, '/storage/')) {
            $localPath = str_replace('/storage/', 'public/', $currentPath);
            
            if ($dryRun) {
                $this->line("Would migrate: {$course->title} - {$currentPath}");
                return;
            }

            if (Storage::disk('local')->exists($localPath)) {
                // Copy to S3
                $fileContent = Storage::disk('local')->get($localPath);
                $fileName = basename($currentPath);
                $s3Path = "lms/courses/images/{$fileName}";
                
                Storage::disk('s3')->put($s3Path, $fileContent);
                $newUrl = Storage::disk('s3')->url($s3Path);
                
                // Update course
                $course->update(['featured_image' => $newUrl]);
                
                $this->info("Migrated: {$course->title} -> {$newUrl}");
                
                // Optionally delete local file
                // Storage::disk('local')->delete($localPath);
            } else {
                $this->warn("Local file not found for course '{$course->title}': {$localPath}");
            }
        } else {
            $this->line("Course '{$course->title}' has custom path: {$currentPath}");
        }
    }
}
