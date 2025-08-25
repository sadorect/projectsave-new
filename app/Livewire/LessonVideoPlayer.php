<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class LessonVideoPlayer extends Component
{
    public $lessonId;
    public $courseId;
    public $thumbnailUrl;
    public $videoUrl = null;
    public $isWatching = false;
    public $watchTimeLogged = 0;
    public $lastHeartbeat = null;
    public $encryptedToken = null;
    
    protected $listeners = [
        'heartbeat' => 'logWatchTime',
        'videoCompleted' => 'markVideoWatched'
    ];
    
    public function mount($lessonId, $courseId, $thumbnailUrl = null)
    {
        $this->lessonId = $lessonId;
        $this->courseId = $courseId;
        $this->thumbnailUrl = $thumbnailUrl;
        $this->lastHeartbeat = now()->timestamp;
        
        // Only create the video token if user has permission
        if ($this->userCanAccessVideo()) {
            $this->generateVideoToken();
        }
    }
    
    public function render()
    {
        return view('livewire.lesson-video-player', [
            'lesson' => Lesson::find($this->lessonId),
            'userCanAccess' => $this->userCanAccessVideo(),
        ]);
    }
    
    public function startWatching()
    {
        if (!$this->userCanAccessVideo()) {
            $this->emit('accessDenied');
            return;
        }
        
        try {
            $this->isWatching = true;
            $this->videoUrl = $this->getVideoUrl();
            
            // Find existing or create new "started" record
            $user = auth()->user();
            $interaction = $user->videoInteractions()
                ->where('lesson_id', $this->lessonId)
                ->where('action', 'started')
                ->first();
                
            if ($interaction) {
                // Update existing record
                $interaction->update([
                    'timestamp' => now()
                ]);
            } else {
                // Create new record
                $user->videoInteractions()->create([
                    'lesson_id' => $this->lessonId,
                    'action' => 'started',
                    'timestamp' => now()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error starting video: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'lesson_id' => $this->lessonId
            ]);
        }
    }
    
    public function logWatchTime($currentTime)
    {
        $now = now()->timestamp;
        
        // Only log progress every 10 seconds
        if ($now - $this->lastHeartbeat >= 10) {
            try {
                $user = auth()->user();
                
                // Find existing or create new progress record
                $interaction = $user->videoInteractions()
                    ->where('lesson_id', $this->lessonId)
                    ->where('action', 'progress')
                    ->first();
                    
                if ($interaction) {
                    // Update existing record
                    $interaction->update([
                        'position' => $currentTime,
                        'timestamp' => now()
                    ]);
                } else {
                    // Create new record
                    $user->videoInteractions()->create([
                        'lesson_id' => $this->lessonId,
                        'action' => 'progress',
                        'position' => $currentTime,
                        'timestamp' => now()
                    ]);
                }
                
                $this->watchTimeLogged += ($now - $this->lastHeartbeat);
                $this->lastHeartbeat = $now;
                
                // Auto-mark as completed if watched 80% of the video
                $lesson = Lesson::find($this->lessonId);
                if ($lesson->video_duration && $this->watchTimeLogged > ($lesson->video_duration * 0.8)) {
                    $this->markVideoWatched();
                }
            } catch (\Exception $e) {
                Log::error('Error logging watch time: ' . $e->getMessage());
            }
        }
    }

    public function markVideoWatched()
    {
        try {
            $lesson = Lesson::find($this->lessonId);
            $user = auth()->user();
            
            if (!$lesson->isCompleted($user)) {
                // Complete the lesson
                $user->completedLessons()->attach($lesson->id, [
                    'completed_at' => now()
                ]);
                
                $this->emit('lessonCompleted');
            }
        } catch (\Exception $e) {
            Log::error('Error marking video as watched: ' . $e->getMessage());
        }
    }
    
    private function userCanAccessVideo()
    {
        $user = auth()->user();
        $course = Course::find($this->courseId);
        
        // Check user is enrolled in the course
        return $user && $course && $user->courses()->where('course_id', $course->id)->exists();
    }
    
    private function generateVideoToken()
    {
        // Create a signed token that expires in 30 minutes
        $token = [
            'lesson_id' => $this->lessonId,
            'user_id' => auth()->id(),
            'expires' => now()->addMinutes(30)->timestamp
        ];
        
        $this->encryptedToken = Crypt::encrypt(json_encode($token));
    }
    
    private function getVideoUrl()
    {
        // Return the route that requires the token
        return route('video.stream', ['token' => $this->encryptedToken]);
    }
}
