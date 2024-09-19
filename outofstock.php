<?php
// Change the "Read More" text to "Out of Stock" for out-of-stock products on product listings
add_filter('woocommerce_loop_add_to_cart_link', 'custom_out_of_stock_text', 10, 2);
function custom_out_of_stock_text($link, $product) {
    // Check if the product is out of stock
    if (!$product->is_in_stock()) {
        // Change the "Read More" text to "Out of Stock"
        $link = '<span class="button out-of-stock">Out of Stock</span>';
    }
    return $link;
}
?>