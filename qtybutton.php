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

                <button type="submit" class="button alt" style="height:35px; margin-top: 10px;">
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
                <button type="submit" class="button alt" style="height:35px; margin-top: 10px;">
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

// Enqueue custom scripts and styles
function custom_enqueue_scripts() {
    // Enqueue main stylesheet
    wp_enqueue_style('main-style', get_stylesheet_uri());

    // Enqueue custom CSS
    wp_enqueue_style('custom-styles', get_template_directory_uri() . '/assets/css/custom-styles.css');

    // Register and enqueue the custom script
    wp_enqueue_script('custom-ajax-add-to-cart', get_template_directory_uri() . '/assets/js/custom-ajax-add-to-cart.js', array('jquery'), null, true);
    
    // Localize the script to pass PHP variables to JavaScript
    wp_localize_script('custom-ajax-add-to-cart', 'wc_cart_params', array(
        'cart_url' => wc_get_cart_url(),
    ));
}
add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');
