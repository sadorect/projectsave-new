<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\Lesson;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            
            // Get the actual video URL (previously hidden from frontend)
            $videoUrl = $lesson->video_url;
            
            // For YouTube/Vimeo videos, redirect to embed URL
            if (stripos($videoUrl, 'youtube.com') !== false || stripos($videoUrl, 'vimeo.com') !== false) {
                if (!empty($lesson->embed_video_url) && filter_var($lesson->embed_video_url, FILTER_VALIDATE_URL)) {
                    return redirect()->to($lesson->embed_video_url . self::VIDEO_EMBED_PARAMS);
                } else {
                    abort(404, 'Video embed URL not available');
                }
            }
            
            // For self-hosted videos, stream the file
            return $this->streamVideo($videoUrl);
            
        } catch (\Exception $e) {
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
    
    private function streamVideo($path)
    {
        // Implementation for streaming self-hosted videos
        // This is a simplified example - production code would need more robust handling
        $response = new StreamedResponse(function() use ($path) {
            $stream = fopen($path, 'r');
            while (!feof($stream)) {
                echo fread($stream, 1024 * 8);
                flush();
            }
            fclose($stream);
        });
        
        $response->headers->set('Content-Type', 'video/mp4');
        return $response;
    }
}
