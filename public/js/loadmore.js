var nextPage = 2; // Trang tiếp theo (ban đầu là 2 vì trang đầu đã hiển thị)

function loadMoreProducts(categorySlug) {
    $.ajax({
        url: '/api/load-more-products',
        method: 'GET',
        data: {
            page: nextPage,
            category_slug: categorySlug // Gửi category_slug vào API
        },
        success: function(response) {
            if (response.products && response.products.length > 0) {
                var source = $("#product-template").html();
                var template = Handlebars.compile(source);

                response.products.forEach(function(product) {
                    // Thêm các trường cần thiết để khớp với template
                    product.image_url = product.image_url || '';

                    var html = template(product);
                    $('#product-list').append(html);
                });

                // Cập nhật trang tiếp theo
                if (response.next_page) {
                    nextPage = response.next_page;
                } else {
                    $('#btn-showmore').hide(); // Ẩn nút nếu không còn trang nào
                }
            }
        },
        error: function() {
            alert("An error occurred while loading more products.");
        }
    });
}

$('#btn-showmore').on('click', function() {
    var categorySlug = $(this).data('category');
    loadMoreProducts(categorySlug);
});
