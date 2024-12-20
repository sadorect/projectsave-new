document.addEventListener('DOMContentLoaded', function() {
    const markCompleteBtn = document.querySelector('.mark-complete');
    
    if (markCompleteBtn) {
        markCompleteBtn.addEventListener('click', function() {
            const lessonId = this.dataset.lesson;
            const courseId = this.dataset.course;
            
            fetch(`/learn/courses/${courseId}/lessons/${lessonId}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Trigger confetti effect
                    confetti({
                        particleCount: 100,
                        spread: 70,
                        origin: { y: 0.6 }
                    });
                    
                    // Show completion modal
                    document.getElementById('completionModal').style.display = 'block';
                    
                    // Update UI elements
                    markCompleteBtn.textContent = 'Completed';
                    markCompleteBtn.classList.add('disabled');
                    
                    // Update progress bar
                    const progressBar = document.querySelector('.progress-bar');
                    if (progressBar) {
                        const newProgress = parseInt(progressBar.getAttribute('aria-valuenow')) + 
                            (100 / document.querySelectorAll('.list-group-item').length);
                        progressBar.style.width = `${newProgress}%`;
                        progressBar.textContent = `${Math.round(newProgress)}%`;
                    }
                }
            });
        });
    }
});