<?php
// Add quantity input and "Add to Cart" button to the product listing page
add_action('woocommerce_after_shop_loop_item', 'custom_add_quantity_field_on_loop', 20);

function custom_add_quantity_field_on_loop() {
    global $product;

    if ($product->is_in_stock()) {
        ?>
        <form class="cart" action="<?php echo esc_url($product->add_to_cart_url()); ?>" method="post" enctype="multipart/form-data">
            <?php
            // Check if the product is variable
            if ($product->is_type('variable')) {
                // Get available variations
                $available_variations = $product->get_available_variations();
                ?>
                <select class="variations-select" name="variation_id" required>
                    <option value=""><?php esc_html_e('Size', 'woocommerce'); ?></option>
                    <?php foreach ($available_variations as $variation) : ?>
                        <option value="<?php echo esc_attr($variation['variation_id']); ?>">
                            <?php
                            // Display variation attributes
                            $variation_attributes = '';
                            foreach ($variation['attributes'] as $attribute_name => $attribute_value) {
                                $variation_attributes .= esc_html($attribute_value) . ' ';
                            }
                            echo esc_html(trim($variation_attributes));
                            ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Quantity input field for variable products -->
                <?php
                woocommerce_quantity_input(array(
                    'input_value' => 1, // Default quantity
                    'min_value'   => 1, // Minimum quantity
                    'max_value'   => $product->get_max_purchase_quantity(), // Maximum quantity
                ));
                ?>

                <button type="submit" class="button alt" style="height:35px;">
                    <?php esc_html_e('Add to Cart', 'woocommerce'); ?>
                </button>
                <?php
            } else {
                // For simple products, display just the Add to Cart button
                // Display quantity input field
                woocommerce_quantity_input(array(
                    'input_value' => 1, // Default quantity
                    'min_value'   => 1, // Minimum quantity
                    'max_value'   => $product->get_max_purchase_quantity(), // Maximum quantity
                ));
                ?>
                <button type="submit" class="button alt" style="height:35px;">
                    <?php echo esc_html($product->add_to_cart_text()); ?>
                </button>
                <?php
            }
            ?>
            <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" />
            <div class="cart-message-<?php echo esc_attr($product->get_id()); ?>" style="display: none;"></div>
           
        </form>
        <?php
    }
}

add_action('wp_ajax_add_to_cart', 'custom_add_to_cart');
add_action('wp_ajax_nopriv_add_to_cart', 'custom_add_to_cart');

function custom_add_to_cart() {
    if (!isset($_POST['product_id'])) {
        wp_send_json_error(array('error' => 'Product ID is missing.'));
    }

    $product_id = intval($_POST['product_id']);
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;

    // Add the product to cart
    $cart = WC()->cart;
    $cart->add_to_cart($product_id, 1, $variation_id);

    // Check for errors
    if ($cart->get_cart_contents_count() === 0) {
        wp_send_json_error(array('error' => 'Unable to add the product to the cart.'));
    }

    // Send the response
    wp_send_json_success();
}

add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');
function enqueue_custom_scripts() {
    // Enqueue jQuery
    wp_enqueue_script('jquery');
    
    // Enqueue your custom AJAX script
    wp_enqueue_script('custom-ajax-add-to-cart', get_template_directory_uri() . '/assets/js/custom-ajax-add-to-cart.js', array('jquery'), null, true);
    
    // Localize script for AJAX
    wp_localize_script('custom-ajax-add-to-cart', 'wc_cart_params', array(
        'cart_url' => wc_get_cart_url(),
    ));
    
    // Enqueue custom CSS file
    wp_enqueue_style('custom-style', get_template_directory_uri() . '/assets/css/custom-styles.css');
}