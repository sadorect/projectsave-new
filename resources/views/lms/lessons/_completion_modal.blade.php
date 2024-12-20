<div class="modal fade" id="completionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bi bi-trophy-fill text-warning display-1 mb-3"></i>
                <h3>Congratulations!</h3>
                <p class="lead mb-4">You've completed this lesson successfully!</p>
                @if($nextLesson)
                    <a href="{{ route('lms.lessons.show', [$course->slug, $nextLesson->slug]) }}" class="btn btn-primary">
                        Continue to Next Lesson
                    </a>
                @else
                    <a href="{{ route('lms.dashboard') }}" class="btn btn-success">
                        Return to Dashboard
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
