<div>
    @if($userCanAccess)
        <div class="video-container" 
             id="video-wrapper-{{ $lesson->id }}" 
             wire:key="video-{{ $lesson->id }}">
            
            @if(!$isWatching)
                <div class="video-placeholder d-flex align-items-center justify-content-center" 
                     style="background-image: url('{{ $thumbnailUrl ?? asset('images/video-placeholder.jpg') }}')">
                    <button wire:click="startWatching" class="play-button">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
            @else
                <div class="secure-video-wrapper position-relative" style="width: 100%; height: 0; padding-bottom: 56.25%;">
                    {{-- Top overlay to block "Watch Later", "Share", channel info --}}
                    <div class="video-block-overlay top-overlay" id="top-overlay-{{ $lesson->id }}"></div>
                    
                    {{-- Bottom overlay to block YouTube logo and subscriptions --}}
                    <div class="video-block-overlay bottom-overlay" id="bottom-overlay-{{ $lesson->id }}"></div>
                    
                    {{-- Middle area security overlay (no right-click but allows clicks to pass through) --}}
                    <div class="video-middle-area" id="middle-area-{{ $lesson->id }}"></div>
                    
                    <iframe 
                        id="lesson-video-{{ $lesson->id }}"
                        src="{{ $videoUrl }}&enablejsapi=1" 
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                        frameborder="0" 
                        allowfullscreen
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        loading="lazy">
                    </iframe>
                    
                    {{-- User watermark --}}
                    <div class="video-watermark">
                        {{ auth()->user()->email }} • {{ now()->format('Y-m-d') }}
                    </div>
                    
                    {{-- Custom controls overlay --}}
                    <div class="custom-video-controls">
                        <button id="play-pause-{{ $lesson->id }}" class="control-button">
                            <i class="fas fa-pause"></i>
                        </button>
                        <div class="progress-container">
                            <div class="progress-bar" id="progress-{{ $lesson->id }}"></div>
                        </div>
                        <div class="volume-container">
                            <button id="mute-{{ $lesson->id }}" class="control-button">
                                <i class="fas fa-volume-up"></i>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Manual play button (displays if autoplay fails) --}}
                    <div id="manual-play-{{ $lesson->id }}" class="manual-play-button">
                        <button class="btn btn-primary btn-lg">
                            <i class="fas fa-play me-2"></i> Click to Play
                        </button>
                    </div>
                </div>
                
                <script>
                    // Add this script to initialize the YouTube player and security features
                    document.addEventListener('DOMContentLoaded', function() {
                        console.log('DOM loaded, initializing YouTube API for lesson {{ $lesson->id }}');
                        
                        let tag = document.createElement('script');
                        tag.src = "https://www.youtube.com/iframe_api";
                        let firstScriptTag = document.getElementsByTagName('script')[0];
                        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                        
                        let player;
                        let videoId = '{{ $lesson->id }}';
                        
                        window.onYouTubeIframeAPIReady = function() {
                            try {
                                console.log('YouTube API Ready, initializing player...');
                                player = new YT.Player('lesson-video-{{ $lesson->id }}', {
                                    events: {
                                        'onReady': onPlayerReady,
                                        'onStateChange': onPlayerStateChange,
                                        'onError': function(event) {
                                            console.error('YouTube player error:', event.data);
                                        }
                                    }
                                });
                            } catch (e) {
                                console.error('Error initializing YouTube player:', e);
                            }
                        };
                        
                        // Block right-clicks on the entire container
                        const container = document.getElementById('video-wrapper-{{ $lesson->id }}');
                        container.addEventListener('contextmenu', e => e.preventDefault());
                        
                        // Block right-clicks on the middle area specifically
                        const middleArea = document.getElementById('middle-area-{{ $lesson->id }}');
                        middleArea.addEventListener('contextmenu', e => e.preventDefault());
                        
                        // Handle clicks on the secure video wrapper
                        const secureVideoWrapper = document.querySelector('.secure-video-wrapper');
                        secureVideoWrapper.addEventListener('click', function(e) {
                            // Only process clicks in the middle area (not on controls or overlays)
                            const rect = secureVideoWrapper.getBoundingClientRect();
                            const clickY = e.clientY - rect.top;
                            const relativeY = clickY / rect.height;
                            
                            // If click is in the middle area (between 15% and 85%)
                            if (relativeY > 0.15 && relativeY < 0.85) {
                                console.log('Middle area clicked, toggling video playback');
                                if (player && typeof player.getPlayerState === 'function') {
                                    if (player.getPlayerState() === 1) {
                                        player.pauseVideo();
                                        playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                                    } else {
                                        player.playVideo();
                                        playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                                    }
                                }
                            }
                        });
                        
                        // Custom controls
                        const playPauseBtn = document.getElementById('play-pause-{{ $lesson->id }}');
                        const progressBar = document.getElementById('progress-{{ $lesson->id }}');
                        const muteBtn = document.getElementById('mute-{{ $lesson->id }}');
                        
                        playPauseBtn.addEventListener('click', function() {
                            console.log('Play/pause button clicked');
                            if (player && typeof player.getPlayerState === 'function') {
                                if (player.getPlayerState() === 1) {
                                    player.pauseVideo();
                                    playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                                } else {
                                    player.playVideo();
                                    playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                                }
                            }
                        });
                        
                        muteBtn.addEventListener('click', function() {
                            if (player && typeof player.isMuted === 'function') {
                                if (player.isMuted()) {
                                    player.unMute();
                                    muteBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
                                } else {
                                    player.mute();
                                    muteBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
                                }
                            }
                        });
                        
                        // Manual play button
                        const manualPlayBtn = document.getElementById('manual-play-{{ $lesson->id }}');
                        manualPlayBtn.addEventListener('click', function() {
                            console.log('Manual play button clicked');
                            if (player && typeof player.playVideo === 'function') {
                                player.playVideo();
                                this.style.display = 'none';
                            }
                        });
                        
                        function onPlayerReady(event) {
                            console.log('Player ready, attempting to play video...');
                            
                            // Force play with a slight delay to ensure API is fully loaded
                            setTimeout(function() {
                                try {
                                    player.playVideo();
                                    console.log('Play command sent to video');
                                } catch (e) {
                                    console.error('Error playing video:', e);
                                }
                            }, 1000);
                            
                            // Update progress bar
                            setInterval(updateProgressBar, 1000);
                            
                            // Show manual play button if video doesn't start playing within 3 seconds
                            setTimeout(function() {
                                if (player && typeof player.getPlayerState === 'function' && player.getPlayerState() !== 1) {
                                    console.log('Video not playing automatically, showing manual play button');
                                    manualPlayBtn.style.display = 'block';
                                }
                            }, 3000);
                        }
                        
                        function updateProgressBar() {
                            if (player && typeof player.getCurrentTime === 'function' && typeof player.getDuration === 'function') {
                                try {
                                    const currentTime = player.getCurrentTime();
                                    const duration = player.getDuration();
                                    const percent = (currentTime / duration) * 100;
                                    progressBar.style.width = percent + '%';
                                    
                                    // Send current time to Livewire component
                                    @this.call('logWatchTime', currentTime);
                                } catch (e) {
                                    console.error('Error updating progress bar:', e);
                                }
                            }
                        }
                        
                        function onPlayerStateChange(event) {
                            console.log('Player state changed to:', event.data);
                            
                            // When video ends (state = 0)
                            if (event.data === 0) {
                                @this.call('markVideoWatched');
                                playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                            } else if (event.data === 1) { // Playing
                                playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                                manualPlayBtn.style.display = 'none'; // Hide manual play button when playing
                            } else if (event.data === 2) { // Paused
                                playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                            }
                        }
                        
                        // Prevent keyboard shortcuts for YouTube
                        document.addEventListener('keydown', function(e) {
                            // List of keys that YouTube uses for shortcuts
                            const youtubeKeys = [32, 75, 37, 39, 70, 77];
                            if (youtubeKeys.includes(e.keyCode)) {
                                e.preventDefault();
                            }
                        });
                    });
                </script>
            @endif
        </div>
    @else
        <div class="video-access-denied">
            <div class="alert alert-warning">
                <i class="fas fa-lock me-2"></i>
                You need to be enrolled in this course to access this video.
                <a href="{{ route('lms.courses.show', $lesson->course->slug) }}" class="btn btn-sm btn-primary mt-2">
                    Enroll Now
                </a>
            </div>
        </div>
    @endif

    <style>
        .video-placeholder {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%;
            background-size: cover;
            background-position: center;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
        }
        
        .play-button {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(0,0,0,0.7);
            border: none;
            color: white;
            font-size: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .play-button:hover {
            transform: scale(1.1);
            background: rgba(0,0,0,0.8);
        }
        
        .secure-video-wrapper {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .video-security-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 5;
            pointer-events: none;
            background: transparent;
        }
        
        .video-watermark {
            position: absolute;
            bottom: 40px;
            right: 10px;
            background: rgba(0,0,0,0.5);
            color: rgba(255,255,255,0.7);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 20;
            pointer-events: none;
        }
        
        .custom-video-controls {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40px;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            padding: 0 10px;
            z-index: 25;
            pointer-events: auto;
        }
        
        .control-button {
            background: transparent;
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            cursor: pointer;
        }
        
        .progress-container {
            flex-grow: 1;
            height: 5px;
            background: rgba(255,255,255,0.2);
            margin: 0 10px;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background: #ff0000;
            width: 0%;
        }
        
        .volume-container {
            margin-left: 10px;
        }
        
        .video-block-overlay {
            position: absolute;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 15;
            pointer-events: auto; /* Block interactions on overlays */
        }
        
        .top-overlay {
            top: 0;
            height: 15%;
        }
        
        .bottom-overlay {
            bottom: 0;
            height: 15%;
        }
        
        .video-middle-area {
            position: absolute;
            top: 15%;
            left: 0;
            width: 100%;
            height: 70%;
            z-index: 10;
            background: transparent;
            pointer-events: none; /* CHANGED: Allow clicks to pass through to video */
        }
        
        .manual-play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 30;
            display: none; /* Hidden by default */
        }
    </style>
</div>