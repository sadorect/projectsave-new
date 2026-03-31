<div>
    @if($userCanAccess)
        <div class="video-container" id="video-wrapper-{{ $lesson->id }}" wire:key="video-{{ $lesson->id }}">
            @if(!$isWatching)
                <div class="video-placeholder d-flex align-items-center justify-content-center" style="background-image: url('{{ $thumbnailUrl ?? asset('images/video-placeholder.jpg') }}')">
                    <button wire:click="startWatching" class="play-button">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
            @else
                <div class="secure-video-wrapper">
                    <div class="video-block-overlay top-overlay" id="top-overlay-{{ $lesson->id }}"></div>
                    <div class="video-block-overlay bottom-overlay" id="bottom-overlay-{{ $lesson->id }}"></div>
                    <div class="video-middle-area" id="middle-area-{{ $lesson->id }}"></div>

                    <iframe
                        id="lesson-video-{{ $lesson->id }}"
                        src="{{ $videoUrl }}&enablejsapi=1"
                        class="lms-video-frame"
                        frameborder="0"
                        allowfullscreen
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        loading="lazy"
                    ></iframe>

                    <div class="video-watermark">
                        {{ auth()->user()->email }} • {{ now()->format('Y-m-d') }}
                    </div>

                    <div id="manual-play-{{ $lesson->id }}" class="manual-play-button">
                        <button class="btn btn-primary btn-lg">
                            <i class="fas fa-play me-2"></i>Click to Play
                        </button>
                    </div>
                </div>

                {{-- Controls sit OUTSIDE the secure-video-wrapper so the
                     privacy overlays can never block pointer events. --}}
                <div class="custom-video-controls" id="controls-{{ $lesson->id }}">
                    <button id="play-pause-{{ $lesson->id }}" class="control-button">
                        <i class="fas fa-pause"></i>
                    </button>
                    <div class="progress-container" id="seek-bar-{{ $lesson->id }}">
                        <div class="progress-bar" id="progress-{{ $lesson->id }}">
                            <span class="progress-thumb"></span>
                        </div>
                    </div>
                    <div class="video-time" id="video-time-{{ $lesson->id }}">0:00 / 0:00</div>
                    <div class="volume-container">
                        <button id="mute-{{ $lesson->id }}" class="control-button">
                            <i class="fas fa-volume-up"></i>
                        </button>
                    </div>
                </div>

                <div class="video-meta-bar" id="video-meta-{{ $lesson->id }}">
                    <span class="video-meta-item" id="video-duration-label-{{ $lesson->id }}">
                        <i class="fas fa-clock me-1"></i> <span id="video-duration-{{ $lesson->id }}">Loading duration…</span>
                    </span>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let tag = document.createElement('script');
                        tag.src = 'https://www.youtube.com/iframe_api';
                        let firstScriptTag = document.getElementsByTagName('script')[0];
                        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                        let player;
                        const container = document.getElementById('video-wrapper-{{ $lesson->id }}');
                        const middleArea = document.getElementById('middle-area-{{ $lesson->id }}');
                        const secureVideoWrapper = container.querySelector('.secure-video-wrapper');
                        const playPauseBtn = document.getElementById('play-pause-{{ $lesson->id }}');
                        const progressBar = document.getElementById('progress-{{ $lesson->id }}');
                        const seekBar     = document.getElementById('seek-bar-{{ $lesson->id }}');
                        const timeDisplay = document.getElementById('video-time-{{ $lesson->id }}');
                        const muteBtn = document.getElementById('mute-{{ $lesson->id }}');
                        const manualPlayBtn = document.getElementById('manual-play-{{ $lesson->id }}');
                        const durationLabel = document.getElementById('video-duration-{{ $lesson->id }}');

                        window.onYouTubeIframeAPIReady = function() {
                            try {
                                player = new YT.Player('lesson-video-{{ $lesson->id }}', {
                                    events: {
                                        onReady: onPlayerReady,
                                        onStateChange: onPlayerStateChange,
                                        onError: function(event) {
                                            console.error('YouTube player error:', event.data);
                                        }
                                    }
                                });
                            } catch (error) {
                                console.error('Error initializing YouTube player:', error);
                            }
                        };

                        container.addEventListener('contextmenu', e => e.preventDefault());
                        middleArea.addEventListener('contextmenu', e => e.preventDefault());

                        secureVideoWrapper.addEventListener('click', function(event) {
                            const rect = secureVideoWrapper.getBoundingClientRect();
                            const relativeY = (event.clientY - rect.top) / rect.height;

                            if (relativeY > 0.15 && relativeY < 0.85 && player && typeof player.getPlayerState === 'function') {
                                if (player.getPlayerState() === 1) {
                                    player.pauseVideo();
                                    playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                                } else {
                                    player.playVideo();
                                    playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                                }
                            }
                        });

                        playPauseBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            if (!player || typeof player.getPlayerState !== 'function') {
                                return;
                            }

                            if (player.getPlayerState() === 1) {
                                player.pauseVideo();
                                playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                            } else {
                                player.playVideo();
                                playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                            }
                        });

                        muteBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            if (!player || typeof player.isMuted !== 'function') {
                                return;
                            }

                            if (player.isMuted()) {
                                player.unMute();
                                muteBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
                            } else {
                                player.mute();
                                muteBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
                            }
                        });

                        manualPlayBtn.addEventListener('click', function() {
                            if (player && typeof player.playVideo === 'function') {
                                player.playVideo();
                                this.style.display = 'none';
                            }
                        });

                        // ── Seek bar ──────────────────────────────────────
                        function formatTime(s) {
                            s = Math.floor(s || 0);
                            const m = Math.floor(s / 60);
                            const sec = String(s % 60).padStart(2, '0');
                            return m + ':' + sec;
                        }

                        function seekToPosition(clientX) {
                            if (!player || typeof player.getDuration !== 'function') return;
                            const rect = seekBar.getBoundingClientRect();
                            const fraction = Math.min(1, Math.max(0, (clientX - rect.left) / rect.width));
                            const seekTime = fraction * player.getDuration();
                            player.seekTo(seekTime, true);
                            // Update the bar immediately so it feels responsive
                            const percent = fraction * 100;
                            progressBar.style.width = percent + '%';
                            timeDisplay.textContent = formatTime(seekTime) + ' / ' + formatTime(player.getDuration());
                        }

                        seekBar.addEventListener('click', function(e) {
                            e.stopPropagation();
                            seekToPosition(e.clientX);
                        });

                        let _seeking = false;
                        seekBar.addEventListener('mousedown', function(e) {
                            e.stopPropagation();
                            _seeking = true;
                            seekToPosition(e.clientX);
                        });
                        document.addEventListener('mousemove', function(e) {
                            if (_seeking) {
                                e.preventDefault();
                                seekToPosition(e.clientX);
                            }
                        });
                        document.addEventListener('mouseup', function() { _seeking = false; });

                        seekBar.addEventListener('touchstart', function(e) {
                            e.stopPropagation();
                            seekToPosition(e.touches[0].clientX);
                        }, { passive: false });
                        seekBar.addEventListener('touchmove', function(e) {
                            e.stopPropagation();
                            seekToPosition(e.touches[0].clientX);
                        }, { passive: false });

                        function onPlayerReady() {
                            setTimeout(function() {
                                try {
                                    player.playVideo();
                                } catch (error) {
                                    console.error('Error playing video:', error);
                                }
                            }, 1000);

                            setInterval(updateProgressBar, 1000);

                            // Populate the duration label below the player.
                            // YouTube may return 0 until the video metadata loads,
                            // so we poll briefly until it reports a real value.
                            let durationPoll = setInterval(function() {
                                if (player && typeof player.getDuration === 'function') {
                                    const dur = player.getDuration();
                                    if (dur > 0) {
                                        const mins = Math.floor(dur / 60);
                                        const secs = String(Math.floor(dur % 60)).padStart(2, '0');
                                        durationLabel.textContent = mins + ':' + secs + ' min';
                                        clearInterval(durationPoll);
                                    }
                                }
                            }, 500);

                            setTimeout(function() {
                                if (player && typeof player.getPlayerState === 'function' && player.getPlayerState() !== 1) {
                                    manualPlayBtn.style.display = 'block';
                                }
                            }, 3000);
                        }

                        function updateProgressBar() {
                            if (player && typeof player.getCurrentTime === 'function' && typeof player.getDuration === 'function') {
                                try {
                                    const currentTime = player.getCurrentTime();
                                    const duration = player.getDuration();
                                    const percent = duration > 0 ? (currentTime / duration) * 100 : 0;
                                    progressBar.style.width = percent + '%';
                                    timeDisplay.textContent = formatTime(currentTime) + ' / ' + formatTime(duration);
                                    @this.call('logWatchTime', currentTime);
                                } catch (error) {
                                    console.error('Error updating progress bar:', error);
                                }
                            }
                        }

                        function onPlayerStateChange(event) {
                            if (event.data === 0) {
                                @this.call('markVideoWatched');
                                playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                            } else if (event.data === 1) {
                                playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                                manualPlayBtn.style.display = 'none';
                            } else if (event.data === 2) {
                                playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                            }
                        }

                        document.addEventListener('keydown', function(event) {
                            const youtubeKeys = [32, 75, 37, 39, 70, 77];
                            if (youtubeKeys.includes(event.keyCode)) {
                                event.preventDefault();
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
</div>
