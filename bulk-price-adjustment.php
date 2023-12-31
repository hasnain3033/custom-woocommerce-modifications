<?php

function add_manual_price_adjustment_field() {
    woocommerce_wp_text_input(
        array(
            'id' => '_manual_price_adjustment',
            'label' => 'Manual Price Adjustment (%)',
            'type' => 'number',
            'custom_attributes' => array(
                'step' => '0.01',
                'min' => '-100'
            ),
        )
        );
}
add_action('woocommerce_product_options_general_product_data', 'add_manual_price_adjustment_field');

function save_manual_price_adjustment_field($product_id) {
    $manual_adjustment = isset($_POST['_manual_price_adjustment']) ? wc_clean($_POST['_manual_price_adjustment']) : '';
    update_post_meta($product_id, '_manual_price_adjustment', $manual_adjustment);
}
add_action('woocommerce_process_product_meta', 'save_manual_price_adjustment_field');


function add_bulk_price_adjustment_interface() {
    ?>
    <div class="inline-edit-group">
        <label class="alignleft">
            <span class="title"><?php _e('Bulk Price Adjustment (%)', 'custom-woocommerce-modifications'); ?></span>
            <input type="number" name="_manual_price_adjustment" step="0.01" min="-100" value="0">
        </label>
    </div>
<?php 
}

// Hook to add a custom bulk price adjustment interface in the admin
add_action('woocommerce_product_bulk_edit_start', 'add_bulk_price_adjustment_interface');

function save_bulk_price_adjustment($product) {
    $product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;

    if(isset($_REQUEST['_manual_price_adjustment'])) {
        $bulk_adjustment = wc_clean($_REQUEST['_manual_price_adjustment']);
        update_post_meta($product_id, '_manual_price_adjustment', $bulk_adjustment);
    }
}

// // Save the bulk price adjustment when saving products
add_action('woocommerce_product_bulk_edit_save', 'save_bulk_price_adjustment');