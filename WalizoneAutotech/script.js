document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('nav a');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            window.scrollTo({
                top: targetSection.offsetTop,
                behavior: 'smooth'
            });
        });
    });
    
    // Form submission handling
    const contactForm = document.querySelector('.contact form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            // Form validation can be added here if needed
            console.log('Form submitted');
            // The actual submission is handled by the action attribute (contact.php)
        });
    }
    
    // Mobile navigation toggle (to be implemented with a hamburger menu)
    // This is a placeholder for future implementation
    const createMobileNav = () => {
        // Code for mobile navigation will go here
    };
    
    // Call functions based on screen size
    const checkScreenSize = () => {
        if (window.innerWidth <= 768) {
            // Mobile view adjustments
        } else {
            // Desktop view adjustments
        }
    };
    
    // Initial check
    checkScreenSize();
    
    // Check on resize
    window.addEventListener('resize', checkScreenSize);
});