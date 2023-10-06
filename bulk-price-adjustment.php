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

function calculate_final_product_price($price, $product) {

    // Get manual adjustment from the product
    $manual_adjustment = get_post_meta($product->get_id(), '_manual_price_adjustment', true);

    if($manual_adjustment !== '') {
        // Calculate the new price considering both default and manual adjustments
        $price = $price + ($price * ($manual_adjustment / 100));
    }

    return $price;

}
add_filter('woocommerce_product_get_price', 'calculate_final_product_price', 10, 2);
add_filter('woocommerce_product_get_regular_price', 'calculate_final_product_price', 10, 2);

function add_bulk_price_adjustment_interface() {
    ?>
    <div class="inline-edit-group">
        <label class="alignleft">
            <span class="title"><?php _e('Bulk Price Adjustment (%)', 'custom-woocommerce-modifications'); ?></span>
            <input type="number" name="_bulk_price_adjustment" step="0.01" min="-100" value="0">
        </label>
        <button class="button button-primary alignright" onclick="applyBulkPriceAdjustment()">Apply</button>
    </div>
    <script>
        function applyBulkPriceAdjustment() {
            const bulkPriceAdjustment = parseFloat(document.querySelector(['name="_bulk_price_adjustment"']).value);
            const products = document.querySelectorAll('.check-column input[type="checkbox"]:checked');

            products.forEach(product => {
                const productId = product.value;
                const currentPrice = parseFloat(document.querySelector(`#edit-${productId} input[name="_manual_price_adjustment"]`).value);
                const newPrice = currentPrice + bulkPriceAdjustment;
                document.querySelector(`#edit-${productId} input[name="_manual_price_adjustment"]`).value = newPrice.toFixed(2);
            });
            jQuery('.cancel').trigger('click');
        }
    </script>
<?php 
}
// Hook to add a custom bulk price adjustment interface in the admin
add_action('woocommerce_product_bulk_edit_end', 'add_bulk_price_adjustment_interface');

function save_bulk_price_adjustment($product_ids) {
    if(isset($_REQUEST['_bulk_price_adjustment'])) {
        $bulk_adjustment = wc_clean($_REQUEST['_bulk_price_adjustment']);
        foreach($product_ids as $product_id) {
            update_post_meta($product_id, '_manual_price_adjustment', $bulk_adjustment);
        }
    }
}