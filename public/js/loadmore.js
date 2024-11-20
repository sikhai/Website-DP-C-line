var nextPage = 1; // Bạn có thể bắt đầu từ trang 1 hoặc trang tiếp theo

function loadMoreProducts() {
    $.ajax({
        url: '/load-more-products',
        method: 'GET',
        data: { page: nextPage },
        success: function(response) {
            console.log(response);
            
            // var source = $("#product-template").html();
            // var template = Handlebars.compile(source);
            // response.products.data.forEach(function(product) {
            //     var html = template(product);
            //     $('#product-list').append(html);
            // });

            // Cập nhật trang tiếp theo
            nextPage++;
        }
    });
}

$('#load-more').on('click', function() {
    loadMoreProducts();
})