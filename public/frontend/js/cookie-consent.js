document.addEventListener('DOMContentLoaded', function() {
    const cookieConsent = document.getElementById('cookie-consent');
    const acceptButton = document.getElementById('accept-cookies');
    const isHomePage = window.location.pathname === '/';
    
    if (!localStorage.getItem('cookieConsent') && isHomePage) {
        cookieConsent.style.display = 'block';
    }
    
    acceptButton.addEventListener('click', function() {
        localStorage.setItem('cookieConsent', 'true');
        cookieConsent.style.display = 'none';
    });
});
