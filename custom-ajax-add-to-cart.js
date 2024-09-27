jQuery(function($) {
    $('form.cart').submit(function(e) {
        e.preventDefault();

        var form = $(this);
        var formData = form.serialize(); // Serialize form data
        var productId = form.find('input[name="add-to-cart"]').val(); // Get product ID

        // Check if the product is variable and a variation is selected
        var isVariableProduct = form.find('select[name="variation_id"]').length > 0;
        if (isVariableProduct) {
            var variationId = form.find('select[name="variation_id"]').val();
            if (!variationId) {
                alert("Please select a variation.");
                return;
            }
            formData += '&variation_id=' + variationId; // Add variation ID to form data
        }

        // Send AJAX request to add the item to the cart
        $.post(form.attr('action'), formData, function(response) {
            if (response.error) {
                alert(response.error);
            } else {
                // Update cart count and show message
                $(document.body).trigger('wc_fragment_refresh');

                // Show view cart message specific to the product
                var message = '<p><a href="' + wc_cart_params.cart_url + '">View Cart</a></p>';
                $('.cart-message-' + productId).html(message).fadeIn(10);
            }
        });
    });
});
