
window.openmenu = function openmenu() {
    let menu = document.querySelector('.menu-area');
    menu.classList.add('show');
}

window.opensearch = function opensearch() {
    let search = document.querySelector('.search-area');
    search.classList.add('show');
}

window.closemenu = function closemenu() {
    let menu = document.querySelector('.menu-area');
    let search_area = document.querySelector('.search-area');
    
    menu.classList.remove('show');
    search_area.classList.remove('show');
}

// Xử lý sự kiện input của thanh tìm kiếm
document.addEventListener('DOMContentLoaded', () => {
    const input = document.querySelector('#search-item');
    const textSearchElement = document.querySelector('.text-search');

    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');

    if (searchParam) {
        input.value = searchParam; 
    }

    // Cập nhật hiển thị thông báo
    const updateTextSearch = (message = '') => {
        textSearchElement.textContent = message;
        textSearchElement.style.display = message ? 'block' : 'none';
    };

    // Xử lý sự kiện nhập liệu
    input.addEventListener('input', () => {
        if (input.value.trim() === '') {
            updateTextSearch('');
        }else{
            updateTextSearch('Press Enter to search');
        }
    });

    // Xử lý sự kiện nhấn Enter
    input.addEventListener('keydown', (evt) => {
        if (evt.key === 'Enter') {
            const query = input.value.trim();
            if (query) {
                window.location.href = `/products?search=${encodeURIComponent(query)}`;
            } else {
                updateTextSearch('Vui lòng nhập từ khóa tìm kiếm!');
            }
        }
    });
});

