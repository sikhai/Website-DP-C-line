var nextPage = 2; // Trang tiếp theo (ban đầu là 2 vì trang đầu đã hiển thị)

function loadMoreProducts(categorySlug) {
    // Hiển thị spinner
    $('#btn-loading').removeClass('d-none');
    $('#btn-showmore').prop('disabled', true);
    $('#btn-showmore').addClass('active');
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
                    product.image_url = product.image_url || '';

                    var html = template(product);
                    $('#product-list').append(html);
                });

                if (response.next_page) {
                    nextPage = response.next_page;
                } else {
                    $('#btn-showmore').hide();
                }
            }
        },
        error: function() {
            alert("An error occurred while loading more products.");
        },
        complete: function() {
            $('#btn-loading').addClass('d-none');
            $('#btn-showmore').prop('disabled', false);
            $('#btn-showmore').removeClass('active');
        }
    });
}

$('#btn-showmore').on('click', function() {
    var categorySlug = $(this).data('category');
    loadMoreProducts(categorySlug);
});

function loadMoreFilterProducts(encryptedIds) {
    // Hiển thị spinner
    $('#btn-loading').removeClass('d-none');
    $('#btn-showmore-filter').prop('disabled', true);
    $('#btn-showmore-filter').addClass('active');
    $.ajax({
        url: '/api/load-more-filter-products',
        method: 'GET',
        data: {
            page: nextPage,
            encrypted_ids: encryptedIds,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.products && response.products.length > 0) {
                var source = $("#product-template").html();
                var template = Handlebars.compile(source);

                response.products.forEach(function(product) {
                    product.image_url = product.image_url || '';

                    var html = template(product);
                    $('#product-list').append(html);
                });

                if (response.next_page) {
                    nextPage = response.next_page;
                } else {
                    $('#btn-showmore-filter').hide();
                }
            }
        },
        error: function() {
            alert("An error occurred while loading more products.");
        },
        complete: function() {
            $('#btn-loading').addClass('d-none');
            $('#btn-showmore-filter').prop('disabled', false);
            $('#btn-showmore-filter').removeClass('active');
        }
    });
}

$('#btn-showmore-filter').on('click', function() {
    var encryptedIds = $(this).data('list_ids');
    loadMoreFilterProducts(encryptedIds);
});
