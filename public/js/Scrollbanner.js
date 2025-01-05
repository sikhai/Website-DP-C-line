const overlayImages = document.querySelectorAll('.overlay-image');
const mainImage = document.querySelector('.image-container');
const content = document.querySelector('.content');

// Dynamically calculate gap based on the main image size or viewport size
function calculateDynamicGap() {
  // const mainImageRect = mainImage.getBoundingClientRect();
  const viewportWidth = window.innerWidth;
  const viewportHeight = window.innerHeight;
  // console.log("Width: ", viewportWidth);
  // console.log("Height: ", viewportHeight);
  
  return [viewportWidth*0.1306, viewportHeight*0.26205]
}

// Initial gap calculation
let dynamicGap = calculateDynamicGap();

// Recalculate gap on window resize
window.addEventListener('resize', () => {
  dynamicGap = calculateDynamicGap();
});
// const gap = 250; // Fixed gap distance

// Directions for images to move outward
const directions = [
    { x: 0, y: -1 }, // top
    { x: -1, y: 0 }, // Center-left
    { x: 1, y: 0 },  // Center-right
    { x: -1, y: 1 },  // Bottom-left
    { x: 1, y: 1 }    // Bottom-right
];


document.addEventListener('DOMContentLoaded', function () {
    // Register the ScrollTrigger plugin
    gsap.registerPlugin(ScrollTrigger);
    
    // Ensure the title is initially hidden and off-screen
    gsap.set(".banner .content", {
        opacity: 0,
        y: 200
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
            x: (index) => directions[index % directions.length].x * (0 + dynamicGap[0]),
            y: (index) => directions[index % directions.length].y * (0 + dynamicGap[1]),
            duration: 1,
            ease: 'none',
        })
        
        // Synchronize overlay images with the main image expansion
        .to(overlayImages, {
            x: (index) => directions[index % directions.length].x * (0 + dynamicGap[0]*3),
            y: (index) => directions[index % directions.length].y * (0 + dynamicGap[1]*3),
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
            y: 0,
            ease: 'none',
          }, "-=3")
          
          ;
});
