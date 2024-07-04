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