var swiper = new Swiper(".proj_mouth", {
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});
// slide service
var swiper = new Swiper(".services_list", {
    slidesPerView: 1,
    spaceBetween: 10,
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
});

function openmenu(){
    let menu = document.querySelector('.menu-area');
    menu.style.display = "block";
}

function opensearch(){
    let search = document.querySelector('.search-area');
    search.style.display = "block";
}

function closemenu(){
    let menu = document.querySelector('.menu-area');
    let search_area = document.querySelector('.search-area');
    
    menu.style.display = "none";
    search_area.style.display = "none";
}

// document.getElementsById("search-item")[0].addEventListener('change', function (evt) {
//     var text_search = this.value;
//     let elemnet = document.querySelector('.text-search');
//     if (text_search == ""){
//         elemnet.style.display = "none";
//     }
//     else{
//         elemnet.style.display = "block";
//     }
// });
const input = document.querySelector('#search-item');
input.addEventListener('input', function (evt) {
    var text_search = this.value;
    let elemnet = document.querySelector('.text-search');
    if (text_search == ""){
        elemnet.style.display = "none";
    }
    else{
        elemnet.style.display = "block";
    }
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


document.querySelector('.banner').addEventListener('scroll', function() {
    const scrollSection = document.querySelector('.banner');
    const scrollPosition = scrollSection.scrollTop;
    const sectionHeight = scrollSection.clientHeight;
    const scrollHeight = scrollSection.scrollHeight;
    const scrollPercentage = scrollPosition / (scrollHeight - sectionHeight);

    // Calculate scales based on scrollPercentage
    const maxScale = 3; // Adjust the maximum scale as needed
    const minScale = 1;
    const scale = minScale + (maxScale - minScale) * scrollPercentage;
    
    // Calculate translations based on scrollPercentage
    const maxTranslation = 1000; // Adjust the maximum translation as needed
    const translateYTop = -maxTranslation * scrollPercentage;
    const translateYBottom = maxTranslation * scrollPercentage;
    const translateXSide = maxTranslation * scrollPercentage;
    
    // Apply transformations
    document.querySelector('.row:nth-child(1) .image').style.transform = `translateY(${translateYTop}px) `;
    
    document.querySelector('.row:nth-child(2) .image:nth-child(1)').style.transform = `translateX(-${translateXSide}px)`;
    document.querySelector('.row:nth-child(2) .image:nth-child(3)').style.transform = `translateX(${translateXSide}px)`;
    
    const maxContainerWidth = 1440; // Adjust the maximum width as needed
    const maxContainerHeight = 860; // Adjust the maximum height as needed
    const minContainerWidth = 400;  // Adjust the minimum width as needed
    const minContainerHeight = 225; // Adjust the minimum height as needed

    const containerWidth = minContainerWidth + (maxContainerWidth - minContainerWidth) * scrollPercentage;
    const containerHeight = minContainerHeight + (maxContainerHeight - minContainerHeight) * scrollPercentage;

    const middleContainer = document.querySelector('.middle-container');
    middleContainer.style.width = `${containerWidth}px`;
    middleContainer.style.height = `${containerHeight}px`;
    
    document.querySelector('.row:nth-child(3) .image:nth-child(1)').style.transform = `translateY(${translateYBottom}px)`;
    document.querySelector('.row:nth-child(3) .image:nth-child(2)').style.transform = `translateY(${translateYBottom}px)`;

    if (scrollPercentage >= 0.95) {
        middleContainer.classList.add('show');
    } else {
        middleContainer.classList.remove('show');
    }

});


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

// images.forEach((img, index) => {
//     img.addEventListener('mousemove', (e) => {
//         const rect = img.getBoundingClientRect();
//         const mouseX = e.clientX - rect.left;
//         const mouseY = e.clientY - rect.top;
//         const centerX = rect.width / 2;
//         const centerY = rect.height / 2;

//         const deltaX = mouseX - centerX;
//         const deltaY = mouseY - centerY;
        
//         const moveX = (deltaX / centerX) * 200; // Tăng biên độ dao động
//         const moveY = (deltaY / centerY) * 200; // Tăng biên độ dao động

//         img.style.transform = `translate(${moveX}px, ${moveY}px)`;

//         // Di chuyển các ảnh khác ra xa
//         images.forEach((otherImg, otherIndex) => {
//             if (otherIndex !== index) {
//                 otherImg.style.transform = `translate(${moveX * -0.5}px, ${moveY * -0.5}px)`;
//             }
//         });
//     });

//     // img.addEventListener('mouseleave', () => {
//     //     img.style.transform = 'translate(0, 0)';
//     //     images.forEach((otherImg) => {
//     //         otherImg.style.transform = 'translate(0, 0)';
//     //     });
//     // });
// });
