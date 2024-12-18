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

function hide_filterbar(){
    let filterbar = document.getElementsByClassName('filter-bar-section');
    let opacity_layer = document.getElementsByClassName('opacity-layer');
    opacity_layer[0].style.display = 'none';
    filterbar[0].style.display = "none";
    filterbar[0].style.transition = "width 2s";
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
}


const checkboxes = document.querySelectorAll('.form-check-input');
        
checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const label = document.querySelector(`label[for='${this.id}']`).innerText;
        const selected_item = document.querySelector("#row-items-selected");
        let id_checkbox = "check_"+label.replaceAll(" ", "").toLowerCase();
        
        if (this.checked) {
            console.log(`Checked: ${label}`); 
            selected_item.innerHTML +=`<div class="item-selected d-flex align-items-center" id="`+id_checkbox+`">
                                            `+label+` <img id="x-close-items" src="images/x-close-8x8.svg"  alt="" style="cursor: pointer;"  onclick="deleteItem()">
                                        </div>`;
        }
        else {
            console.log(`Unchecked: ${label}`); // In ra nhãn của checkbox khi bỏ chọn
            
            let selected_item = document.querySelector("#"+id_checkbox);
            selected_item.remove();
        }
    });
});

function clear_sellected_item(){
    const selected_item = document.querySelector("#row-items-selected");

    selected_item.innerHTML = "";

    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

function deleteItem(){
    const closeButtons = document.querySelectorAll('#x-close-items');

        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Tìm phần tử cha có class item-selected và xóa class đó
                const parent = this.closest('.item-selected');
                if (parent) {
                    // parent.classList.remove('item-selected');
                    let id = parent.id;
                    checkboxes.forEach(checkbox => {
                        if (checkbox.id == id){
                            checkbox.checked = false;
                        }
                        
                    });
                    parent.remove();
                }
            });
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

