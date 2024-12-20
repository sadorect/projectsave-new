document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.querySelector('.progress-bar');
    
    if (progressBar) {
        // Add animation class
        progressBar.style.transition = 'width 1s ease-in-out';
        
        // Animate progress bar on completion
        const animateProgress = (newProgress) => {
            progressBar.style.width = `${newProgress}%`;
            progressBar.textContent = `${Math.round(newProgress)}%`;
            
            if (newProgress === 100) {
                progressBar.classList.add('bg-success');
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 }
                });
            }
        };
        
        // Expose animation function globally
        window.animateProgress = animateProgress;
    }
});
