{{-- filepath: resources/views/lms/lessons/youtube-embed.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Video Player</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        .video-wrapper {
            position: relative;
            width: 100%;
            height: 100vh;
        }
        iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
</head>
<body>
    <div class="video-wrapper">
        <iframe 
            id="youtube-player-{{ $lessonId }}"
            src="{{ $embedUrl }}"
            frameborder="0" 
            allowfullscreen
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        ></iframe>
    </div>
    
    <script>
        // Heartbeat implementation to send to parent Livewire component
        let player;
        let videoEnded = false;
        
        // Listen for messages from parent window
        window.addEventListener('message', function(event) {
            if (event.data && event.data.action === 'getCurrentTime' && !videoEnded) {
                // Send current time to parent
                if (player && typeof player.getCurrentTime === 'function') {
                    try {
                        const currentTime = player.getCurrentTime();
                        window.parent.postMessage({currentTime: currentTime}, '*');
                    } catch (e) {
                        console.error('Error getting video time', e);
                    }
                }
            }
        });
        
        // YouTube API integration
        let tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        let firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('youtube-player-{{ $lessonId }}', {
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }
        
        function onPlayerReady(event) {
            // Tell parent window the player is ready
            window.parent.postMessage({event: 'playerReady'}, '*');
        }
        
        function onPlayerStateChange(event) {
            // When video ends (state = 0)
            if (event.data === 0 && !videoEnded) {
                videoEnded = true;
                window.parent.postMessage({event: 'videoEnded'}, '*');
            }
        }
    </script>
</body>
</html>