function openmenu(){
    let menu = document.querySelector('.menu-area');
    menu.classList.add('show');
}

function opensearch(){
    let search = document.querySelector('.search-area');
    search.classList.add('show');
}

function closemenu(){
    let menu = document.querySelector('.menu-area');
    let search_area = document.querySelector('.search-area');
    
    menu.classList.remove('show');
    search_area.classList.remove('show');
    
}

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