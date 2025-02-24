// function openmenu(){
//     let main_view = document.getElementsByClassName('container');
//     let menu = document.getElementsByClassName('menu-container');

//     main_view[0].style.display  = 'none';
//     menu[0].style.display  = 'block';
// }

// function closemenu(){
//     let main_view = document.getElementsByClassName('container');
//     let menu = document.getElementsByClassName('menu-container');

//     main_view[0].style.display  = 'block';
//     menu[0].style.display  = 'none';
// }
const isMobile = window.innerWidth <= 1024;
function hide_filterbar(){
    let filterbar = document.getElementsByClassName('filter-bar-section');
    let opacity_layer = document.getElementsByClassName('opacity-layer');
    opacity_layer[0].style.display = 'none';
    filterbar[0].style.display = "none";
    filterbar[0].style.transition = "width 2s";
    if (isMobile === true) {
        let btn_apply = document.getElementsByClassName('btn-apply-filter');
        let btn_filter = document.getElementById('btn-filter');
        btn_apply[0].style.position = 'none';
        btn_filter.style.position = 'fixed';
        btn_filter.style.display = 'flex';
        
    }
}

function open_filterbar(){
    let filterbar = document.getElementsByClassName('filter-bar-section');
    filterbar[0].style.display = "block";
    let opacity_layer = document.getElementsByClassName('opacity-layer');
    opacity_layer[0].style.display = 'block';
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
    if (isMobile === true) {
        let btn_apply = document.getElementsByClassName('btn-apply-filter');
        let btn_filter = document.getElementById('btn-filter');
        // btn_apply[0].style.position = 'fixed';
        btn_filter.style.display = 'none';
        
    }
}
const checkboxes = document.querySelectorAll('.form-check-input');
let totalProducts = 0;

checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const label = document.querySelector(`label[for='${this.id}']`).innerText;
        const selectedItemContainer = document.querySelector("#row-items-selected");
        const btnFilterAction = document.querySelector(".btn-filter-action");
        let idCheckbox = "check_" + label.replaceAll(" ", "").toLowerCase();
        let listProductsCheckbox = parseInt(this.getAttribute('data-products'));
        
        if (this.checked) {
            totalProducts += listProductsCheckbox;
            selectedItemContainer.innerHTML += `<div class="item-selected d-flex align-items-center" id="${idCheckbox}">
                                                    ${label} 
                                                    <img src="images/x-close-8x8.svg" alt="" style="cursor: pointer;" onclick="deleteItem('${idCheckbox}', ${listProductsCheckbox})">
                                                </div>`;
        } else {
            totalProducts -= listProductsCheckbox;
            let selectedItem = document.querySelector("#" + idCheckbox);
            if (selectedItem) {
                selectedItem.remove();
            }
        }

        btnFilterAction.innerHTML = "APPLY " + "(" + totalProducts + ")";
    });
});

function deleteItem(idCheckbox, productCount) {
    const itemToRemove = document.getElementById(idCheckbox);
    if (itemToRemove) {
        itemToRemove.remove();
        totalProducts -= productCount;

        // Uncheck the associated checkbox
        checkboxes.forEach(checkbox => {
            const label = document.querySelector(`label[for='${checkbox.id}']`).innerText;
            if ("check_" + label.replaceAll(" ", "").toLowerCase() === idCheckbox) {
                checkbox.checked = false;
            }
        });

        document.querySelector(".btn-filter-action").innerHTML = "APPLY " + "(" + totalProducts + ")";
    }
}

function show_hide_characteristic(element) {
    const targetSelector = element.getAttribute('data-target');
    const targetElements = document.querySelectorAll(targetSelector);
    
    targetElements.forEach(function(targetElement) {
        if (targetElement.style.display !== 'none') {
            targetElement.style.display = 'none';
            element.style.transform = 'rotate(0deg)';

            // Count checked checkboxes
            const checkboxes = targetElement.querySelectorAll('input[type="checkbox"]');
            let checkedCount = 0;
            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    checkedCount++;
                }
            });
            console.log(checkedCount);
            const countElement = targetElement.previousElementSibling.querySelector('#count');
            if (checkedCount == 0) {
                countElement.style.display = 'none';
            } else {
                countElement.style.display = 'block';
                countElement.textContent = `(${checkedCount})`;
            }
            
        } else {
            targetElement.style.display = 'block';
            element.style.transform = 'rotate(180deg)';
            const countElement = targetElement.previousElementSibling.querySelector('#count');
            countElement.style.display = 'none';
        }

        
    
    });

    
}

function clear_sellected_item(){
    const selected_item = document.querySelector("#row-items-selected");

    selected_item.innerHTML = "";

    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

// detail items 
// switch image
function switchImage(imgElement) {
    // Đổi hình ảnh trong main-view
    var mainImage = document.getElementById('main-image');
    mainImage.src = imgElement.src;

    // Thay đổi class
    var currentSelected = document.querySelector('.current-selected');
    currentSelected.classList.remove('current-selected');
    currentSelected.classList.add('next-selected');

    var parentDiv = imgElement.parentElement;
    parentDiv.classList.remove('next-selected');
    parentDiv.classList.add('current-selected');
}

