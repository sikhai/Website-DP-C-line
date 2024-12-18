const overlayImages = document.querySelectorAll('.overlay-image');
const mainImage = document.querySelector('.image-container');
const content = document.querySelector('.content');
const gap = 150; // Fixed gap distance

// Directions for images to move outward
const directions = [
    { x: -1, y: -1 }, // Top-left
    { x: 1, y: -1 },  // Top-right
    { x: -1, y: 1 },  // Bottom-left
    { x: 1, y: 1 }    // Bottom-right
];


document.addEventListener('DOMContentLoaded', function () {
    // Register the ScrollTrigger plugin
    gsap.registerPlugin(ScrollTrigger);
    
    // Ensure the title is initially hidden and off-screen
    gsap.set(".banner .content", {
        opacity: 0,
        y: 0
    });

    // Create timeline
    gsap.timeline({
        scrollTrigger: {
          trigger: '.banner',
          start: 'center center',
          end: 'bottom top',
          scrub: true,
          pin: true,
        }
      })
        // Move overlay images outward to maintain gap
        .to(overlayImages, {
            x: (index) => directions[index % directions.length].x * (0 + gap),
            y: (index) => directions[index % directions.length].y * (0 + gap),
            duration: 1,
            ease: 'none',
        })
        // Synchronize overlay images with the main image expansion
        .to(overlayImages, {
            x: (index) => directions[index % directions.length].x * (0 + gap * 6),
            y: (index) => directions[index % directions.length].y * (0 + gap * 6),
            scale: 2,
            opacity: 0, // Fade out images as they move out
            duration: 3,
            ease: 'none',
        }, "-=1")

        // Animate section 2 when scrolling up
        .to(mainImage, {
            width: "1400px",
            height: "860px",
            duration: 3,
            ease: 'none',
          }, "-=2")
        // Animate section 2 when scrolling up
        .to(content, {
            opacity: 1,
            duration: 1,
            ease: 'none',
          }, "-=3")
          
          ;
});
