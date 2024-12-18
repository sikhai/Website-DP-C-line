window.addEventListener('scroll', function() {
    const scrollPercent = window.scrollY / (document.documentElement.scrollHeight - window.innerHeight);
    const maxWidth = 1400; // Image width
    const maxHeight = 860; // Image height
    const minWidth = 420; // Initial frame width
    const minHeight = 280; // Initial frame height
    
    const newWidth = minWidth + (maxWidth - minWidth) * scrollPercent;
    const newHeight = minHeight + (maxHeight - minHeight) * scrollPercent;
    
    const container = document.querySelector('.container');
    container.style.width = `${newWidth}px`;
    container.style.height = `${newHeight}px`;
});