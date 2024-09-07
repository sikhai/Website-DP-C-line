document.addEventListener('DOMContentLoaded', function () {
    // Register the ScrollTrigger plugin
    gsap.registerPlugin(ScrollTrigger);
    
    // Ensure the title is initially hidden and off-screen
    gsap.set(".banner .content", {
        opacity: 0,
        y: 300
    });

    // Animate section 2 when scrolling up
    gsap.to(".image-container", {
    width: "1400px",
    height: "860px",
    scrollTrigger: {
        trigger: ".banner",
        start: "center center", // Animation starts as soon as the section enters the viewport
        end: "bottom 70%", // Animation continues until the section is fully out of the viewport
        pin: ".banner",
        pinSpacing: false,
        scrub: true, // Links the animation progress to the scroll position
        markers: false // Optional: Show markers for debugging
        }
    });

    // Animate section 3 with a different effect
    // gsap.to('#section3', {
    //     rotation: 360,
    //     scrollTrigger: {
    //         trigger: '#section3',
    //         start: 'top 50%',
    //         end: 'bottom 50%',
    //         scrub: true,
    //         markers: false // Optional, for debugging
    //     }
    // });

    // Animation for the title appearing at 95% of the content2 expansion
    // gsap.to("#section2 .title", {
    //     opacity: 1,
    //     y: 0,
    //     duration: 1,
    //     scrollTrigger: {
    //         trigger: "#section2 .content2",
    //         start: "top 95%",  // Trigger the animation when content2 has reached 95% of its target
    //         toggleActions: "play none none none", // Only play the animation once
    //         markers: true,     // Optional: Show markers for debugging
    //         onEnter: () => gsap.to("#section2 .title", { opacity: 1, y: 0 }),  // Animate title into view
    //         onLeaveBack: () => gsap.set("#section2 .title", { opacity: 0, y: 100 }), // Reset title when scrolling back
    //     }
    // });
    // Animation for the title appearing at 95% of the content2 expansion
    ScrollTrigger.create({
        trigger: ".banner .image-container",
        start: "top top",  // Trigger the animation when content2 has reached 95% of its target
        onEnter: () => gsap.to(".banner .content", {
            opacity: 1,
            y: 0,
            duration: 1
        }),  // Animate title into view
        onLeaveBack: () => gsap.set(".banner .content", {
            opacity: 0,
            y: 300
        }), // Reset title when scrolling back
        markers: false           // Optional: Show markers for debugging
    });
});
