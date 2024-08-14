//project annimantion
const images = document.querySelectorAll('.img-wrapper');
const imgArea = document.querySelector('.img-area');

imgArea.addEventListener('mousemove', (e) => {
    const rect = imgArea.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;

    images.forEach((img) => {
        const imgRect = img.getBoundingClientRect();
        const imgCenterX = imgRect.left + imgRect.width / 2 - rect.left;
        const imgCenterY = imgRect.top + imgRect.height / 2 - rect.top;

        const deltaX = mouseX - imgCenterX;
        const deltaY = mouseY - imgCenterY;
        const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
        const angle = Math.atan2(deltaY, deltaX);

        const moveDistance = Math.min(50, distance); // Giới hạn khoảng cách di chuyển
        const moveX = Math.cos(angle) * -moveDistance;
        const moveY = Math.sin(angle) * -moveDistance;

        img.style.transform = `translate(${moveX}px, ${moveY}px)`;
    });
});

imgArea.addEventListener('mouseleave', () => {
    images.forEach((img) => {
        img.style.transform = 'translate(0, 0)';
    });
});


// Flip annimation
const flipBoxes = [
    document.getElementsByClassName('left')[0],
    document.getElementsByClassName('context')[0],
    document.getElementsByClassName('img-rightbottom')[0]
];

let angles = [0, 0, 0];

function flip(box, index) {
    angles[index] += 180;
    box.querySelector('.flip-card-inner').style.transform = `rotateY(${angles[index]}deg)`;
}

function startFlipping() {
    let index = 0;
    setInterval(() => {
        flip(flipBoxes[index], index);
        index = (index + 1) % flipBoxes.length;
    }, 3000);
}

startFlipping();



// swiper project
// var swiper = new Swiper(".proj_mouth", {
//     navigation: {
//         nextEl: ".swiper-button-next",
//         prevEl: ".swiper-button-prev",
//     },
// });
// slide service

var swiperConfig = {
    slidesPerView: 1,
    spaceBetween: 10,
    speed: 2500,
    loop: true,
    autoplay: {
        delay: 0,
        disableOnInteraction: false,
    },
    pagination: {
        el: ".swiper-pagination",
        type: "progressbar",
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    breakpoints: {
        640: {
            slidesPerView: 2,
            spaceBetween: 15,
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        1024: {
            slidesPerView: 4,
            spaceBetween: 24,
        },
    },
    on: {
        slideChange: function () {
            document.querySelectorAll('.overlay_service').forEach(function (overlay) {
                overlay.style.opacity = '1';
            });
            // Đảm bảo swiper được khởi tạo trước khi sử dụng
            if (typeof swiper !== 'undefined') {
                var activeSlide = swiper.slides[swiper.activeIndex];
                var overlay = activeSlide.querySelector('.overlay_service');
                if (overlay) {
                    overlay.style.opacity = '0';
                }

                if (activeSlide.classList.contains('blank-slide')) {
                    swiper.slideToLoop(0, 0, false);
                    swiper.autoplay.stop();
                    setTimeout(function () {
                        swiper.autoplay.start();
                    }, 10000);
                }
            }
        },
    }
};

document.addEventListener('DOMContentLoaded', function() {
    var swiper = new Swiper(".swiper.services_list", swiperConfig);
});

// document.addEventListener('scroll', function() {
//     const container = document.querySelector('.container-project');
//     const middleImage = document.querySelector('.middle');

//     // Calculate scroll percentage
//     let scrollPosition = window.scrollY;
//     let windowHeight = window.innerHeight;
//     let bodyHeight = document.body.offsetHeight;

//     let scrollPercentage = (scrollPosition / (bodyHeight - windowHeight)) * 100;

//     if (scrollPercentage > 10) {
//         container.classList.add('scrolled');
//     } else {
//         container.classList.remove('scrolled');
//     }
// });

//banner annimation
// document.addEventListener('scroll', function() {
//     const banner = document.querySelector('.banner');
//     const zoomImages = document.querySelectorAll('#zoomImage');
//     const content = document.querySelectorAll('.content');
//     const nextButton = document.querySelector('.swiper-button-next');
//     const prevButton = document.querySelector('.swiper-button-prev');
    
//     const scrollPosition = window.scrollY;
//     const maxScroll = 700; // maximum scroll area

//     if (scrollPosition <= maxScroll) {
//         const scale = 0.3 + (scrollPosition / maxScroll) * 0.7;
//         zoomImages.forEach(img => img.style.transform = `scale(${scale})`);
        
//         if (scrollPosition >= 0.95*maxScroll) {
//             banner.classList.add('zoomed');
//             banner.classList.add('revealed');
//         } else {
//             banner.classList.remove('revealed');
//             if (scrollPosition < maxScroll * 0.7) {
//                 banner.classList.remove('zoomed');
//             }
//         }
//     }
// });

// window.addEventListener('scroll', () => {
//     const section_ = document.querySelector('.banner');
//     const imageContainers = document.querySelectorAll('.image-container');
//     const sectionRect = section_.getBoundingClientRect();
//     const windowHeight = window.innerHeight;
  
//     if (sectionRect.top <= windowHeight / 2 && sectionRect.bottom >= windowHeight / 2) {
//         const scrollPercentage = (windowHeight / 2 - sectionRect.top) / sectionRect.height;
    
//         // Expand the image container based on scroll percentage
//         const maxWidth = 1400;
//         const maxHeight = 860;
//         const initialWidth = 420;
//         const initialHeight = 280;
    
//         const newWidth = initialWidth + (maxWidth - initialWidth) * scrollPercentage;
//         const newHeight = initialHeight + (maxHeight - initialHeight) * scrollPercentage;
//         imageContainers.forEach((imageContainer) => {
//             imageContainer.style.position = 'fixed';
//             imageContainer.style.width = `${newWidth}px`;
//             imageContainer.style.height = `${newHeight}px`;
//             imageContainer.style.top = '50%'; // Always center the image vertically
//             imageContainer.style.left = '50%'; // Always center the image horizontally
//             imageContainer.style.transform = 'translate(-50%, -50%)';
//             // console.log("1");
//             console.log("Image container position:", imageContainer.style.top, imageContainer.style.left);
//         })
//     } else if (sectionRect.bottom < windowHeight) {
//         // Keep the image in place when reaching the end of section 2
//         imageContainers.forEach((imageContainer) => {
//             imageContainer.style.position = 'fixed';
//             imageContainer.style.top = '50%';
//             imageContainer.style.transform = 'translate(-50%, -50%)';
//             console.log("Image container position:", imageContainer.style.top, imageContainer.style.left);

//         })
//     } else {
//         // Before reaching section 2
//         imageContainers.forEach((imageContainer) => {
//             imageContainer.style.width = '420px';
//             imageContainer.style.height = '280px';
//             imageContainer.style.position = 'absolute';
//             imageContainer.style.top = '50%';
//             imageContainer.style.left = '50%';
//             imageContainer.style.transform = 'translate(-50%, -50%)';
//             console.log("Image container position:", imageContainer.style.top, imageContainer.style.left);

//         })
//     }
    
//   });
  
  



