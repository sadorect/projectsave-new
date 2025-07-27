<div class="modal fade" id="completionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center py-5">
                <div class="completion-celebration mb-4">
                    <div class="trophy-icon">
                        <i class="fas fa-trophy text-warning display-1 mb-3"></i>
                    </div>
                    <div class="completion-confetti"></div>
                </div>
                
                <h3 class="mb-2 text-success fw-bold">Congratulations! 🎉</h3>
                <p class="lead mb-1">You've completed <strong>{{ $lesson->title }}</strong></p>
                <p class="text-muted mb-4">Great job on your learning journey!</p>
                
                <!-- Progress Update -->
                <div class="progress-update mb-4 p-3 bg-light rounded">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h5 mb-1 text-primary" id="updated-progress">{{ round($courseProgress) }}%</div>
                            <small class="text-muted">Course Progress</small>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-1 text-success">+1</div>
                            <small class="text-muted">Lesson Completed</small>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="d-grid gap-2 d-md-block">
                    @if($nextLesson)
                        <a href="{{ route('lms.lessons.show', [$course->slug, $nextLesson->slug]) }}" 
                           class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-arrow-right me-2"></i>Continue to Next Lesson
                        </a>
                    @else
                        <a href="{{ route('asom.welcome') }}" class="btn btn-success btn-lg px-4">
                            <i class="fas fa-graduation-cap me-2"></i>Return to ASOM Dashboard
                        </a>
                    @endif
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                </div>
                
                <!-- Achievement Check -->
                <div class="achievement-check mt-4 d-none" id="achievement-notification">
                    <div class="alert alert-warning d-flex align-items-center">
                        <i class="fas fa-medal me-3 fa-2x"></i>
                        <div>
                            <strong>Achievement Unlocked!</strong><br>
                            <span id="achievement-text"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .completion-celebration {
        position: relative;
        overflow: hidden;
    }
    
    .trophy-icon {
        animation: bounce 1s ease-in-out infinite alternate;
    }
    
    @keyframes bounce {
        from { transform: translateY(0px); }
        to { transform: translateY(-10px); }
    }
    
    .modal-content {
        border-radius: 20px;
    }
    
    .progress-update {
        border-radius: 15px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .btn-lg {
        border-radius: 25px;
        padding: 12px 30px;
        font-weight: 600;
    }
    
    #achievement-notification {
        animation: slideIn 0.5s ease-out;
    }
    
    @keyframes slideIn {
        from { 
            opacity: 0;
            transform: translateY(20px);
        }
        to { 
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
