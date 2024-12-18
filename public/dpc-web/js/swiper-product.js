const swiper = new Swiper('.swiper-container', {
    slidesPerView: 5, // Show 5 items at a time
    spaceBetween: 30, // Space between slides
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    // pagination: {
    //     el: '.swiper-pagination',
    //     clickable: true,
    // },
    loop: true, // Loop through the slides
});