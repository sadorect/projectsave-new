<div class="video-player-wrapper">
    @if($lesson->video_type === 'url')
        @if(str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
            <iframe 
                src="{{ str_replace('watch?v=', 'embed/', $lesson->video_url) }}"
                class="w-full aspect-video rounded-lg shadow-lg"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        @elseif(str_contains($lesson->video_url, 'vimeo.com'))
            <iframe 
                src="{{ str_replace('vimeo.com', 'player.vimeo.com/video', $lesson->video_url) }}"
                class="w-full aspect-video rounded-lg shadow-lg"
                allow="autoplay; fullscreen; picture-in-picture"
                allowfullscreen>
            </iframe>
        @endif
    @else
        <video 
            controls 
            class="w-full rounded-lg shadow-lg"
            controlsList="nodownload">
            <source src="{{ $lesson->video_url }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    @endif
</div>
