document.addEventListener('DOMContentLoaded', function() {
    const completionForm = document.querySelector('.completion-form');
    const markCompleteBtn = document.querySelector('.mark-complete-btn');
    
    if (completionForm && markCompleteBtn) {
        completionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const btnText = markCompleteBtn.querySelector('.btn-text');
            const spinner = markCompleteBtn.querySelector('.loading-spinner');
            
            btnText.classList.add('d-none');
            spinner.classList.remove('d-none');
            markCompleteBtn.classList.add('completing');
            markCompleteBtn.disabled = true;
            
            // Get form data
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Hide loading state
                    spinner.classList.add('d-none');
                    
                    // Update button to completed state
                    markCompleteBtn.classList.remove('completing');
                    markCompleteBtn.classList.add('completed', 'success-animation');
                    btnText.innerHTML = '<i class="fas fa-check-circle me-2"></i>Completed ✓';
                    btnText.classList.remove('d-none');
                    
                    // Trigger confetti effect
                    if (typeof confetti !== 'undefined') {
                        confetti({
                            particleCount: 100,
                            spread: 70,
                            origin: { y: 0.6 },
                            colors: ['#28a745', '#20c997', '#17a2b8']
                        });
                    }
                    
                    // Update progress bars
                    updateProgressBars(data.newProgress || 0);
                    
                    // Update lesson navigation
                    updateLessonNavigation();
                    
                    // Check for achievements
                    if (data.achievement) {
                        showAchievementNotification(data.achievement);
                    }
                    
                    // Show completion modal with delay for animation
                    setTimeout(() => {
                        const modal = new bootstrap.Modal(document.getElementById('completionModal'));
                        modal.show();
                    }, 1000);
                    
                } else {
                    throw new Error(data.message || 'Failed to mark lesson as complete');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Reset button state
                spinner.classList.add('d-none');
                btnText.classList.remove('d-none');
                markCompleteBtn.classList.remove('completing');
                markCompleteBtn.disabled = false;
                
                // Show error message
                showErrorNotification('Failed to mark lesson as complete. Please try again.');
            });
        });
    }
    
    // Function to update progress bars throughout the page
    function updateProgressBars(newProgress) {
        const progressBars = document.querySelectorAll('.progress-bar');
        const progressTexts = document.querySelectorAll('.progress-text');
        
        progressBars.forEach(bar => {
            bar.style.width = `${newProgress}%`;
            bar.setAttribute('aria-valuenow', newProgress);
        });
        
        progressTexts.forEach(text => {
            text.textContent = `${Math.round(newProgress)}%`;
        });
        
        // Update modal progress
        const modalProgress = document.getElementById('updated-progress');
        if (modalProgress) {
            modalProgress.textContent = `${Math.round(newProgress)}%`;
        }
    }
    
    // Function to update lesson navigation
    function updateLessonNavigation() {
        const currentLessonNav = document.querySelector('.lesson-nav-item.current');
        if (currentLessonNav) {
            currentLessonNav.classList.add('completed');
            
            // Update the lesson number to show completion
            const lessonNumber = currentLessonNav.querySelector('.lesson-counter');
            if (lessonNumber) {
                lessonNumber.outerHTML = `
                    <div class="completion-check">
                        <i class="fas fa-check"></i>
                    </div>
                `;
            }
        }
    }
    
    // Function to show achievement notification
    function showAchievementNotification(achievement) {
        const achievementDiv = document.getElementById('achievement-notification');
        const achievementText = document.getElementById('achievement-text');
        
        if (achievementDiv && achievementText) {
            achievementText.textContent = achievement;
            achievementDiv.classList.remove('d-none');
        }
    }
    
    // Function to show error notification
    function showErrorNotification(message) {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-danger border-0 position-fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-exclamation-circle me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        const toastBootstrap = new bootstrap.Toast(toast);
        toastBootstrap.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            document.body.removeChild(toast);
        });
    }
    
    // Auto-scroll to current lesson in navigation
    const currentLesson = document.querySelector('.lesson-nav-item.current');
    if (currentLesson) {
        currentLesson.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
        });
    }
    
    // Add smooth scrolling for video containers
    const videoContainer = document.querySelector('.video-container');
    if (videoContainer) {
        const iframe = videoContainer.querySelector('iframe');
        if (iframe) {
            iframe.addEventListener('load', function() {
                // Video loaded successfully
                console.log('Video loaded');
            });
        }
    }
});