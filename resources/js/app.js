/**
 * PSB Dar Al Tauhid - Main JavaScript
 */

/**
 * Scroll Animation Observer
 * Adds 'visible' class to elements with 'animate-on-scroll' class when they enter viewport
 */
function initScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1
    });

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
}

/**
 * Initialize all JavaScript functionality when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function () {
    initScrollAnimations();
});

/**
 * Re-initialize after Livewire updates
 * This ensures animations work properly after dynamic content updates
 */
document.addEventListener('livewire:navigated', function () {
    initScrollAnimations();
});
