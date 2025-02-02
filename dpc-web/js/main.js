//our-project annimantion
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
    slidesPerView: 5,
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
        320: {
            slidesPerView: 2,
            spaceBetween: 10,
        },
        480: {
            slidesPerView: 2,
            spaceBetween: 10,
        },
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

  



