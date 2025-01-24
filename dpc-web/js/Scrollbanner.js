const overlayImages = document.querySelectorAll('.overlay-image');
const mainImage = document.querySelector('.image-container');
const content = document.querySelector('.content');

// // Dynamically calculate gap based on the main image size or viewport size
// function calculateDynamicGap() {
//   // const mainImageRect = mainImage.getBoundingClientRect();
//   const viewportWidth = window.innerWidth;
//   const viewportHeight = window.innerHeight;
//   const deviceScale = window.devicePixelRatio || 1;
//   // console.log("Width: ", viewportWidth);
//   // console.log("Height: ", viewportHeight);
  
//   return [viewportWidth*0.1306*deviceScale, viewportHeight*0.26205*deviceScale, deviceScale]
// }

// // Initial gap calculation
// let dynamicGap = calculateDynamicGap();

// // Recalculate gap on window resize
// window.addEventListener('resize', () => {
//   dynamicGap = calculateDynamicGap();
// });
const gap = 250; // Fixed gap distance

// Directions for images to move outward
const directions_1 = [
    { x: 0, y: -1 }, // top
    { x: -1, y: 0.2 }, // Center-left
    { x: 1, y: -0.2 },  // Center-right
    { x: -0.3, y: 1 },  // Bottom-left
    { x: 0.3, y: 1 }    // Bottom-right
];

const isMobile = window.innerWidth <= 768;

if (isMobile === false) {
  document.addEventListener('DOMContentLoaded', function () {
    // Register the ScrollTrigger plugin
    gsap.registerPlugin(ScrollTrigger);
    
    // Ensure the title is initially hidden and off-screen
    gsap.set(".banner .content", {
        opacity: 0,
        y: 800
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
            x: (index) => directions_1[index % directions_1.length].x * (0 + gap),
            y: (index) => directions_1[index % directions_1.length].y * (0 + gap*2/3),
            duration: 1,
            ease: 'none',
        })
        
        // Synchronize overlay images with the main image expansion
        .to(overlayImages, {
            x: (index) => directions_1[index % directions_1.length].x * (0 + gap*3),
            y: (index) => directions_1[index % directions_1.length].y * (0 + gap*2),
            scale: 1.5,
            opacity: 0, // Fade out images as they move out
            duration: 3,
            ease: 'none',
        }, "-=1")

        // Animate section 2 when scrolling up
        .to(mainImage, {
            width: "1400px",
            height: "860px",
            duration: 2,
            ease: 'none',
          }, "-=2")
        // Animate section 2 when scrolling up
        .to(content, {
            opacity: 1,
            duration: 3,
            y: 0,
            ease: 'none',
          }, "-=3")
          
          ;
});

    
}

