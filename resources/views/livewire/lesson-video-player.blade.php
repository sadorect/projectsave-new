<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    @if($userCanAccess)
        <div class="video-container" 
             id="video-wrapper-{{ $lesson->id }}" 
             wire:key="video-{{ $lesson->id }}"
             oncontextmenu="return false;">
            
            @if(!$isWatching)
                <div class="video-placeholder d-flex align-items-center justify-content-center" 
                     style="background-image: url('{{ $thumbnailUrl ?? asset('images/video-placeholder.jpg') }}')">
                    <button wire:click="startWatching" class="play-button">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
            @else
                <div class="position-relative" style="width: 100%; height: 0; padding-bottom: 56.25%;">
                    <iframe 
                        id="lesson-video-{{ $lesson->id }}"
                        src="{{ $videoUrl }}" 
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                        frameborder="0" 
                        allowfullscreen
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        loading="lazy">
                    </iframe>
                </div>
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
