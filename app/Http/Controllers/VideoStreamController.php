<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Models\Lesson;
//use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoStreamController extends Controller
{
    private const VIDEO_EMBED_PARAMS = '?rel=0&controls=0&showinfo=0&modestbranding=0';

    public function stream(Request $request)
    {
        try {
            // Decrypt the token
            $tokenData = json_decode(Crypt::decrypt($request->token), true);
            
            // Validate token
            if (!$this->validateToken($tokenData)) {
                abort(403, 'Invalid or expired token');
            }
            
            $lesson = Lesson::findOrFail($tokenData['lesson_id']);
            $videoUrl = $lesson->video_url;
            
            if (empty($videoUrl)) {
                abort(404, 'Video URL not found');
            }
            
            // Convert YouTube URLs to embed format
            if ($this->isYouTubeUrl($videoUrl)) {
                $embedUrl = $this->getYouTubeEmbedUrl($videoUrl);
                return view('video.youtube-embed', [
                    'embedUrl' => $embedUrl,
                    'lessonId' => $lesson->id
                ]);
            }
            
            // If not YouTube, display error
            abort(400, 'Unsupported video type');
            
        } catch (\Exception $e) {
            Log::error('Video streaming error: ' . $e->getMessage());
            abort(403, 'Access denied');
        }
    }
    
    private function validateToken($tokenData)
    {
        // Check if token has expired
        if (!isset($tokenData['expires']) || $tokenData['expires'] < now()->timestamp) {
            return false;
        }
        
        // Check if the user ID matches
        if (!isset($tokenData['user_id']) || $tokenData['user_id'] != auth()->id()) {
            return false;
        }
        
        return true;
    }
    
    private function isYouTubeUrl($url)
    {
        return (
            strpos($url, 'youtube.com') !== false || 
            strpos($url, 'youtu.be') !== false
        );
    }
    
    private function getYouTubeEmbedUrl($url)
    {
        // Extract video ID from URL
        $videoId = null;
        
        // Handle youtube.com/watch?v=VIDEO_ID
        if (strpos($url, 'youtube.com/watch') !== false) {
            parse_str(parse_url($url, PHP_URL_QUERY), $params);
            $videoId = $params['v'] ?? null;
        } 
        // Handle youtu.be/VIDEO_ID
        elseif (strpos($url, 'youtu.be/') !== false) {
            $videoId = substr(parse_url($url, PHP_URL_PATH), 1);
        }
        
        if (!$videoId) {
            return null;
        }
        
        // Return embed URL with secure parameters
        return "https://www.youtube.com/embed/{$videoId}" . self::VIDEO_EMBED_PARAMS;
    }
}
