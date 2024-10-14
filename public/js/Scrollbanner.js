document.addEventListener('DOMContentLoaded', function () {
    // Register the ScrollTrigger plugin
    gsap.registerPlugin(ScrollTrigger);
    
    // Ensure the title is initially hidden and off-screen
    const bannerContent = document.querySelector(".banner .content");
    
    if (bannerContent) {
        // Register the ScrollTrigger plugin
        gsap.registerPlugin(ScrollTrigger);
        
        // Ensure the title is initially hidden and off-screen
        gsap.set(bannerContent, {
            opacity: 0,
            y: 300
        });
    }

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

    // ------------------phase1
    // gsap.to(".banner .surround-img:nth-child(1)", {
    //     y: "-300px",
    //     scrollTrigger: {
    //         trigger: ".banner .image-container",
    //         start: "top center",
    //         end: "center center",
    //         scrub: true,
    //         markers: true
    //     }
    // });

    // gsap.to(".banner .surround-img:nth-child(2)", {
    //     x: "-200px",
    //     scrollTrigger: {
    //         trigger: ".banner .image-container",
    //         start: "top center",
    //         end: "center center",
    //         scrub: true,
    //         markers: true
    //     }
    // });

    // gsap.to(".banner .surround-img:nth-child(3)", {
    //     x: "200px",
    //     scrollTrigger: {
    //         trigger: ".banner .image-container",
    //         start: "top center",
    //         end: "center center",
    //         scrub: true,
    //         markers: true
    //     }
    // });

    // gsap.to(".banner .surround-img:nth-child(4)", {
    //     y: "200px",
    //     scrollTrigger: {
    //         trigger: ".banner .image-container",
    //         start: "top center",
    //         end: "center center",
    //         scrub: true,
    //         markers: true
    //     }
    // });

    // gsap.to(".banner .surround-img:nth-child(5)", {
    //     x: "100px",
    //     y: "-100px",
    //     scrollTrigger: {
    //         trigger: ".banner .image-container",
    //         start: "top center",
    //         end: "center center",
    //         scrub: true,
    //         markers: true
    //     }
    // });

    // ------------------phase2
    // gsap.to(".banner .surround-img:nth-child(1)", {
    //     y: "-1000px",
    //     scale:2,
    //     scrollTrigger: {
    //         trigger: ".banner .image-container",
    //         start: "center center",
    //         end: "bottom top",
    //         scrub: true,
    //         markers: true
    //     }
    // });

    // gsap.to(".banner .surround-img:nth-child(2)", {
    //     x: "-500px",
    //     scale:2,
    //     scrollTrigger: {
    //         trigger: ".banner .image-container",
    //         start: "center center",
    //         end: "bottom top",
    //         scrub: true,
    //         markers: true
    //     }
    // });

    // gsap.to(".banner .surround-img:nth-child(3)", {
    //     x: "500px",
    //     scale:2,
    //     scrollTrigger: {
    //         trigger: ".banner .image-container",
    //         start: "center center",
    //         end: "bottom top",
    //         scrub: true,
    //         markers: true
    //     }
    // });

    // gsap.to(".banner .surround-img:nth-child(4)", {
    //     y: "500px",
    //     scale:2,
    //     scrollTrigger: {
    //         trigger: ".banner .image-container",
    //         start: "center center",
    //         end: "bottom top",
    //         scrub: true,
    //         markers: true
    //     }
    // });

    // gsap.to(".banner .surround-img:nth-child(5)", {
    //     x: "300px",
    //     y: "-300px",
    //     scale: 2,
    //     scrollTrigger: {
    //         trigger: ".banner .image-container",
    //         start: "center center",
    //         end: "bottom top",
    //         scrub: true,
    //         markers: true
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
