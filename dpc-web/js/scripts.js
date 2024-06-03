function openmenu(){
    let main_view = document.getElementsByClassName('container');
    let menu = document.getElementsByClassName('menu-container');

    main_view[0].style.display  = 'none';
    menu[0].style.display  = 'block';
}

function closemenu(){
    let main_view = document.getElementsByClassName('container');
    let menu = document.getElementsByClassName('menu-container');

    main_view[0].style.display  = 'block';
    menu[0].style.display  = 'none';
}