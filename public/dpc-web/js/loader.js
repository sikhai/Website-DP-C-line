//loader
document.addEventListener("DOMContentLoaded", function() {
    setTimeout(() => {
        document.querySelector('.loader').style.display = 'none';
        const layout1 = document.querySelector('.layout');
        layout1.classList.remove('hidden');
        layout1.style.opacity = '1';
    }, 500); // 2 seconds delay
});
