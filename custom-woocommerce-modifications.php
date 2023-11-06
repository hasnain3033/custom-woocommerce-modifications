<?php
/*
Plugin Name: Custom WooCommerce Modifications
Description: Calculator for Air Frieght, Insurance, etc and also Custom cart and checkout tempplates. Including a custom product title widget for elementor with character limit option
Version: 5.0
Author: Hasnain Qureshi
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: custom-woocommerce-modifications
*/

// Security: Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


// Check if WooCommerce is active or installed
function custom_check_woocommerce()
{
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'custom_woocommerce_missing_notice');
    }
}
add_action('admin_init', 'custom_check_woocommerce');

// Display notice if WooCommerce is missing
function custom_woocommerce_missing_notice()
{
    echo '<div class="error"><p>';
    echo __('Woocommerce Modifications is enabled but not effective. It requires WooCommerce in order to work.', 'custom-woocommerce-modifications');
    echo '</p></div>';
}


// Register the widget
function register_custom_elementor_widgets() {
    require_once(plugin_dir_path(__FILE__) . 'elementor-widget.php');
    
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Custom_Product_Title_Widget());
}
add_action('elementor/widgets/register', 'register_custom_elementor_widgets');


// Add your custom code below this line

function custom_enqueue_scripts()
{

    wp_enqueue_script('custom-modifications-woocommerce', plugin_dir_url(__FILE__) . 'js/custom-modifications-woocommerce.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');

function enqueue_custom_tooltip_styles()
{
    wp_enqueue_style('custom-tooltip', plugin_dir_url(__FILE__) . 'css/custom-tooltips.css', array(), '1.0.0', 'all');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_tooltip_styles');


// handling fees logic
function get_custom_handling_fee()
{

    $cart = WC()->cart;

    $handling_fee_first_product = 7; // Handling fee for the first product
    $handling_fee_additional_product = 3.5; // Additional handling fee for each product

    $cart_items = $cart->get_cart();
    $product_count = 0;

    foreach ($cart_items as $cart_item_key => $cart_item) {
        $product_count += $cart_item['quantity'];
    }

    if ($product_count > 0) {
        $handling_fee = $handling_fee_first_product + ($handling_fee_additional_product * ($product_count - 1));
        return $handling_fee;
    }
    return 0;
}


function calculate_final_product_price($regular_price, $product_id)
{

    $manual_adjustment = get_post_meta($product_id, '_manual_price_adjustment', true);
    $price_updated = $regular_price + ($regular_price * ($manual_adjustment / 100));

    // Step 1: Calculate admin fee (10% of regular price)
    $admin_fee = 0.1 * $price_updated;

    // Step 8: Calculate final product price (sum of all components)
    $final_cif_price = $price_updated + $admin_fee;

	$product_price_terms_id = null; // Initialize it to a default value
    $product_price_term = get_the_terms($product_id, 'product_cat');

	if ($product_price_term && !is_wp_error($product_price_term)) {
		$top_level_category = null;

		foreach ($product_price_term as $term) {
			if ($term->parent == 0) {
				// This is a top-level category
				$top_level_category = $term;
				break;
			}
		}

		if ($top_level_category) {
			// Found a top-level category
			$product_price_terms_id = $top_level_category;
		} else {
			// No top-level category found, use any category as the top-level
			$product_price_terms_id = reset($product_price_terms);
		}
	}

    $duties_percentage_value = get_field('duties_percentage_value', $product_price_terms_id);


    $duties_totalt_val = $final_cif_price * ($duties_percentage_value / 100);

    $pkg_fees = 7.32;
    $final_product_price = $duties_totalt_val + $price_updated + $admin_fee + $pkg_fees;

    return $final_product_price;
}

// Hook the custom price calculation into WooCommerce
add_filter('woocommerce_product_get_price', 'custom_product_price_calculation', 10, 2);
function custom_product_price_calculation($price, $product)
{
    $product_id = $product->get_id();
    // $regular_price = get_post_meta($product_id, '_regular_price', true);
    // $sale_price = get_post_meta($product_id, '_sale_price', true);

    // // Set the initial product price to regular price
    // $product_price = $regular_price;

    // // Check if the product is on sale and the sale price is lower than the regular price
    // if ($sale_price && $sale_price < $regular_price) {
    //     $product_price = $sale_price;
    // }

    // Calculate the custom product price
    $custom_price = calculate_final_product_price($price, $product_id);

    return $custom_price;
}

add_action('woocommerce_cart_calculate_fees', 'display_custom_shipping_expense');

function display_custom_shipping_expense()
{
    global $woocommerce;

    // Get the cart instance
    $cart = WC()->cart;

    // Get the total weight of the cart items in kg
    $total_weight_lbs = $cart->get_cart_contents_weight();

    if ($total_weight_lbs > 0.5) {
        $total_weight_lbs = round($total_weight_lbs);
    }

    $combinedArray = [
        0.5 => 7.25,
        1 => 8.95,
        2 => 13.50,
        3 => 18.30,
        4 => 23.25,
        5 => 28.20,
        6 => 33.15,
        7 => 38.10,
        8 => 43.05,
        9 => 48.00,
        10 => 52.95,
        11 => 57.90,
        12 => 62.85,
        13 => 67.80,
        14 => 72.75,
        15 => 77.70,
        16 => 82.65,
        17 => 87.60,
        18 => 92.55,
        19 => 97.50,
        20 => 102.45,
        21 => 107.40,
        22 => 112.35,
        23 => 117.30,
        24 => 122.25,
        25 => 127.20,
        26 => 132.15,
        27 => 137.10,
        28 => 142.05,
        29 => 147.00,
        30 => 151.95,
        31 => 156.90,
        32 => 161.85,
        33 => 166.80,
        34 => 171.75,
        35 => 176.70,
        36 => 181.65,
        37 => 186.60,
        38 => 191.55,
        39 => 196.50,
        40 => 201.45,
        41 => 206.40,
        42 => 211.35,
        43 => 216.30,
        44 => 221.25,
        45 => 226.20,
        46 => 231.15,
        47 => 236.10,
        48 => 241.05,
        49 => 246.00,
        50 => 250.95,
        51 => 255.90,
        52 => 260.85,
        53 => 265.80,
        54 => 270.75,
        55 => 275.70,
        56 => 280.65,
        57 => 285.60,
        58 => 290.55,
        59 => 295.50,
        60 => 300.45,
        61 => 305.40,
        62 => 310.35,
        63 => 315.30,
        64 => 320.25,
        65 => 325.20,
        66 => 330.15,
        67 => 335.10,
        68 => 340.05,
        69 => 345.00,
        70 => 349.95,
        71 => 354.90,
        72 => 359.85,
        73 => 364.80,
        74 => 369.75,
        75 => 374.70,
        76 => 379.65,
        77 => 384.60,
        78 => 389.55,
        79 => 394.50,
        80 => 399.45,
        81 => 404.40,
        82 => 409.35,
        83 => 414.30,
        84 => 419.25,
        85 => 424.20,
        86 => 429.15,
        87 => 434.10,
        88 => 439.05,
        89 => 444.00,
        90 => 448.95,
        91 => 453.90,
        92 => 458.85,
        93 => 463.80,
        94 => 468.75,
        95 => 473.70,
        96 => 478.65,
        97 => 483.60,
        98 => 488.55,
        99 => 493.50,
        100 => 498.451
    ];


    // Calculate the total base price of products in the cart
    $total_base_price = 0;
    foreach ($cart->get_cart() as $cart_item) {
        $product_id = $cart_item['product_id']; // Get the product ID
        $regular_price = get_post_meta($product_id, '_regular_price', true);
        $sale_price = get_post_meta($product_id, '_sale_price', true);
        // Set the initial product price to regular price
        $product_price = $regular_price;
        // Check if the product is on sale and the sale price is lower than the regular price
        if ($sale_price && $sale_price < $regular_price) {
            $product_price = $sale_price;
        }
        $total_base_price += $product_price * $cart_item['quantity'];

    }

    // Calculate the insurance fee based on the total base price
    $insurance_fee = floor(($total_base_price + 99.99) / 100) * 2;


    // Add the insurance fee as a cart fee
    if ($insurance_fee > 0) {
        $cart->add_fee(__('Insurance', 'woocommerce'), $insurance_fee);
    }



    // Check if the converted weight exists in the combined array
    if (isset($combinedArray[$total_weight_lbs])) {
        // Get the air freight value from the combined array
        $air_freight = $combinedArray[$total_weight_lbs];

    } else {
        // Calculate shipping cost for extra pounds
        $base_weight = max(array_keys($combinedArray)); // Get the highest weight in the array
        $extra_weight = $total_weight_lbs - $base_weight;
        $extra_shipping_cost_per_pound = 4.95;
        $air_freight = $combinedArray[$base_weight] + ($extra_weight * $extra_shipping_cost_per_pound);
    }

    $handing_fees = get_custom_handling_fee();

    $admin__shipping_fee = 0.1 * ($air_freight + $handing_fees + $insurance_fee);
    // $shipping_cost_total = $shipping_cost_total_without_admin + $admin__shipping_fee;

    // Add air freight value to the total value
    $cart->add_fee(__('Air Freight', 'woocommerce'), $air_freight, false);
    $cart->add_fee(__('Service Fee', 'woocommerce'), $admin__shipping_fee, false);
    $cart->add_fee(__('Handling Fee', 'woocommerce'), $handing_fees);

}

add_action('woocommerce_review_order_before_order_total', 'display_total_fees');
add_action('woocommerce_cart_totals_before_order_total', 'display_total_fees');

function display_total_fees()
{
    // Get the cart
    $cart = WC()->cart;

    // Get the total amount of fees
    $total_fees = 0;
    foreach ($cart->get_fees() as $fee) {
        $total_fees += floatval($fee->amount);
    }

    // Display the total fees after the "Order Total" on the checkout page
    echo '<tr class="total-fees"><th>Total Shipping Cost</th><td data-title="Total Shipping Cost">' . wc_price($total_fees) . '</td></tr>';
}

// Modify the product subtotal display on the checkout page
function custom_checkout_product_total($subtotal, $cart_item, $cart_item_key)
{
    if (is_checkout()) {
        // Get the product, price, and quantity
        $product_id = $cart_item['product_id']; // Get the product ID
        $regular_price = get_post_meta($product_id, '_regular_price', true);
        $sale_price = get_post_meta($product_id, '_sale_price', true);
        
        // Set the initial product price to regular price
        $product_price = $regular_price;
        // Check if the product is on sale and the sale price is lower than the regular price
        if ($sale_price && $sale_price < $regular_price) {
            $product_price = $sale_price;
        }

        $quantity = $cart_item['quantity'];

        // Calculate the modified subtotal
        $modified_subtotal = $product_price * $quantity;

        // Format and return the modified subtotal display
        return '<span class="custom-product-total">' . wc_price($modified_subtotal) . '</span>';
    }

    return $subtotal; // Return original subtotal for other pages
}

add_filter('woocommerce_cart_item_subtotal', 'custom_checkout_product_total', 10, 3);


function custom_modify_review_order_template_location($template, $template_name, $template_path)
{
    if ($template_name === 'checkout/review-order.php') {
        $template = plugin_dir_path(__FILE__) . 'templates/checkout/review-order.php';
    }
    if ($template_name === 'cart/cart.php') {
        $template = plugin_dir_path(__FILE__) . 'templates/cart/cart.php';
    }
    if ($template_name === 'cart/cart-totals.php') {
        $template = plugin_dir_path(__FILE__) . 'templates/cart/cart-totals.php';
    }
    if ($template_name === 'cart/proceed-to-checkout-button.php') {
        $template = plugin_dir_path(__FILE__) . 'templates/cart/proceed-to-checkout-button.php';
    }
    if ($template_name === 'checkout/form-checkout.php') {
        $template = plugin_dir_path(__FILE__) . 'templates/checkout/form-checkout.php';
    }
    if ($template_name === 'emails/customer-processing-order.php') {
        $template = plugin_dir_path(__FILE__) . 'templates/emails/customer-processing-order.php';
    }
    return $template;
}
add_filter('woocommerce_locate_template', 'custom_modify_review_order_template_location', 10, 3);

function calculate_cart_regular_price()
{
    // Get the cart contents
    $cart_items = WC()->cart->get_cart();

    // Initialize variables to store product IDs and total regular price
    // $product_ids = array();
    $total_regular_price = 0;

    // Loop through each cart item
    foreach ($cart_items as $cart_item_key => $cart_item) {
        // Get the product ID and quantity for the current cart item
        $product_id = $cart_item['product_id'];
        $quantity = $cart_item['quantity'];

        // Get the regular price of the current product
        $regular_price = get_post_meta($product_id, '_regular_price', true);
        $sale_price = get_post_meta($product_id, '_sale_price', true);
        $manual_adjustment = get_post_meta($product_id, '_manual_price_adjustment', true);

        // Set the initial product price to regular price
        $product_price = $regular_price;

        // Check if the product is on sale and the sale price is lower than the regular price
        if ($sale_price && $sale_price < $regular_price) {
            $product_price = $sale_price;
        }

        if($manual_adjustment !== '') {
            // Calculate the new price considering both default and manual adjustments
            $manual_adjustment_value = ($product_price * ($manual_adjustment / 100));
            $product_price += $manual_adjustment_value;
            $total_regular_price += ($product_price * $quantity);
            
        } else {            
            // Multiply the regular price by the quantity and add to the total
            $total_regular_price += ($product_price * $quantity);
        }

    }

    // If there is only one product in the cart, return its regular price
    return $total_regular_price;

}

function calculate_cart_duties_values()
{
    // Get the cart contents
    $cart_items = WC()->cart->get_cart();

    // Initialize variables to store product IDs and total regular price
    $total_duties_price = 0;

    // Loop through each cart item
    foreach ($cart_items as $cart_item_key => $cart_item) {
        // Get the product ID and quantity for the current cart item
        $product_id = $cart_item['product_id'];
        $quantity = $cart_item['quantity'];

        $regular_price = get_post_meta($product_id, '_regular_price', true);
        $sale_price = get_post_meta($product_id, '_sale_price', true);
        $manual_adjustment = get_post_meta($product_id, '_manual_price_adjustment', true);

        // Set the initial product price to regular price
        $product_price = $regular_price;        
        // Check if the product is on sale and the sale price is lower than the regular price
        if ($sale_price && $sale_price < $regular_price) {
            $product_price = $sale_price;
        }
        if($manual_adjustment !== '') {
            // Calculate the new price considering both default and manual adjustments
            $manual_adjustment_value = ($product_price * ($manual_adjustment / 100));
            $product_price += $manual_adjustment_value;
        }

		$product_price_terms_id = null; // Initialize it to a default value
        $product_price_term = get_the_terms($product_id, 'product_cat');

		if ($product_price_term && !is_wp_error($product_price_term)) {
		$top_level_category = null;

		foreach ($product_price_term as $term) {
			if ($term->parent == 0) {
				// This is a top-level category
				$top_level_category = $term;
				break;
			}
		}

		if ($top_level_category) {
			// Found a top-level category
			$product_price_terms_id = $top_level_category;
		} else {
			// No top-level category found, use any category as the top-level
			$product_price_terms_id = reset($product_price_terms);
		}
	}
		
        $duties_percentage_value = get_field('duties_percentage_value', $product_price_terms_id);
        $admin_fee = 0.1 * $product_price;
        $final_cif_price = $product_price + $admin_fee;
        $duties_totalt_val = $final_cif_price * ($duties_percentage_value / 100);

        // Multiply the regular price by the quantity and add to the total
        $total_duties_price += ($duties_totalt_val * $quantity);
    }

    return $total_duties_price;

}
function calculate_cart_admin_values()
{
    // Get the cart contents
    $cart_items = WC()->cart->get_cart();

    // Initialize variables to store product IDs and total regular price
    $total_admin_fee = 0;

    // Loop through each cart item
    foreach ($cart_items as $cart_item_key => $cart_item) {
        // Get the product ID and quantity for the current cart item
        $product_id = $cart_item['product_id'];
        $quantity = $cart_item['quantity'];

        $regular_price = get_post_meta($product_id, '_regular_price', true);
        $sale_price = get_post_meta($product_id, '_sale_price', true);
        $manual_adjustment = get_post_meta($product_id, '_manual_price_adjustment', true);
        
        // Set the initial product price to regular price
        $product_price = $regular_price;
        
        // Check if the product is on sale and the sale price is lower than the regular price
        if ($sale_price && $sale_price < $regular_price) {
            $product_price = $sale_price;
        }
        if($manual_adjustment !== '') {
            // Calculate the new price considering both default and manual adjustments
            $manual_adjustment_value = ($product_price * ($manual_adjustment / 100));
            $product_price += $manual_adjustment_value;
        }

        $admin_fee = 0.1 * $product_price;


        // Multiply the regular price by the quantity and add to the total
        $total_admin_fee += ($admin_fee * $quantity);
    }

    return $total_admin_fee;

}
function calculate_cart_pkg_values()
{
    // Get the cart contents
    $cart_items = WC()->cart->get_cart();

    // Initialize variables to store product IDs and total regular price
    $total_pkg_fees = 0;

    // Loop through each cart item
    foreach ($cart_items as $cart_item_key => $cart_item) {
        // Get the product quantity for the current cart item
        $quantity = $cart_item['quantity'];
        $pkg_fees = 7.32;
        // Multiply the regular price by the quantity and add to the total
        $total_pkg_fees += ($pkg_fees * $quantity);
    }

    return $total_pkg_fees;

}

function replace_state_with_island_name($fields)
{
    $island_names = array(
        'Grand Cayman',
        'Cayman Brac',
        'Little Cayman',
    );

    $fields['billing']['billing_island_name'] = array(
        'type' => 'select',
        'class' => array('form-row-wide'),
        'label' => __('Island Name', 'woocommerce'),
        'required' => true,
        'clear' => true,
        'options' => array('' => __('Choose Island', 'woocommerce')) + array_combine($island_names, $island_names),
    );

    $fields['shipping']['shipping_island_name'] = array(
        'type' => 'select',
        'class' => array('form-row-wide'),
        'label' => __('Island Name', 'woocommerce'),
        'required' => true,
        'clear' => true,
        'options' => array('' => __('Choose Island', 'woocommerce')) + array_combine($island_names, $island_names),
    );

    // Move the Island Name field after the Country / Region field
    $fields['billing']['billing_island_name']['priority'] = 40;
    $fields['shipping']['shipping_island_name']['priority'] = 40;

    return $fields;
}
add_filter('woocommerce_checkout_fields', 'replace_state_with_island_name');


add_filter('woocommerce_billing_fields', 'remove_state_field', 10, 1);
function remove_state_field($fields)
{
    unset($fields['billing_state']);
    return $fields;
}
add_filter('woocommerce_shipping_fields', 'remove_state_field_shipping', 10, 1);
function remove_state_field_shipping($fields)
{
    unset($fields['shipping_state']);
    return $fields;
}
function replace_town_city_with_district($fields)
{
    // Remove the "Town / City" field
    unset($fields['billing']['billing_city']);
    unset($fields['shipping']['shipping_city']);

    // Add the "District" text field
    $fields['billing']['billing_district'] = array(
        'type' => 'text',
        'class' => array('form-row-wide'),
        'label' => __('District', 'woocommerce'),
        'required' => true,
    );

    $fields['shipping']['shipping_district'] = array(
        'type' => 'text',
        'class' => array('form-row-wide'),
        'label' => __('District', 'woocommerce'),
        'required' => true,
    );

    // Move the "District" text field after the "Country / Region" field
    $fields['billing']['billing_district']['priority'] = 45;
    $fields['shipping']['shipping_district']['priority'] = 45;

    return $fields;
}
add_filter('woocommerce_checkout_fields', 'replace_town_city_with_district');


function get_cart_total_value()
{
    // Get the cart
    $cart = WC()->cart;

    $cart_total = $cart->get_cart_contents_total();
    $total_fees = 0;
    foreach ($cart->get_fees() as $fee) {
        $total_fees += floatval($fee->amount);
    }
    $exchange_rate_usd_to_kyd = 0.8334;
    $new_total = $cart_total + $total_fees * $exchange_rate_usd_to_kyd;
    return $new_total;
}

// Include the bulk price adjustment features 
include_once(plugin_dir_path(__FILE__) . 'bulk-price-adjustment.php');

// Include the email verification features 
include_once(plugin_dir_path(__FILE__) . 'email-verification.php');


// Include the customer unique address generate feature 
include_once(plugin_dir_path(__FILE__) . 'customer-address.php');

// Include the shortcodes 
include_once(plugin_dir_path(__FILE__) . 'templates/sections/header-notice.php');

// Include the credit-auth-form 
include_once(plugin_dir_path(__FILE__) . 'credit-auth-form.php');

// Include the credit-auth-form 
include_once(plugin_dir_path(__FILE__) . 'custom-author-selection.php');




function updated_get_product_subtotal($product, $quantity) {
    $product_id = $product->get_id();
    $regular_price = get_post_meta($product_id, '_regular_price', true);
    $sale_price = get_post_meta($product_id, '_sale_price', true);

    // Set the initial product price to regular price
     $price = $regular_price;

    if ($sale_price && $sale_price < $regular_price) {
        $price = $sale_price;
    }

    // Retrieve the manual price adjustment from the product's custom field
    $manual_price_adjustment = get_post_meta($product_id, '_manual_price_adjustment', true);

    if ($manual_price_adjustment !== '') {
        $manual_price_adjustment = floatval($manual_price_adjustment);
        $price += ($price * $manual_price_adjustment / 100);
    }

    if ($product->is_taxable()) {
        if (wc_tax_enabled()) {
            $display_prices_including_tax = wc_prices_include_tax();
            $tax_display_mode = get_option('woocommerce_tax_display_cart');

            if ($display_prices_including_tax) {
                $row_price = wc_get_price_including_tax($product, array('qty' => $quantity));
            } else {
                $row_price = wc_get_price_excluding_tax($product, array('qty' => $quantity));
            }

            if (($tax_display_mode === 'excl' && $display_prices_including_tax) || ($tax_display_mode === 'incl' && !$display_prices_including_tax)) {
                $product_subtotal = wc_price($row_price) . ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
            } else {
                $product_subtotal = wc_price($row_price);
            }
        } else {
            $product_subtotal = wc_price($price * $quantity);
        }
    } else {
        $product_subtotal_updated = wc_price($price * $quantity);
    }

    return $product_subtotal_updated;
}


function custom_wc_register_custom_order_statuses() {
    // Define an array of custom order statuses
    $custom_statuses = array(
        'wc-arrived-at-station' => 'Arrived At Station',
        'wc-assigned-to-awb' => 'Assigned to AWB',
        'wc-awaiting-invoice' => 'Awaiting invoice or payment',
        'wc-customs-clearance' => 'Customs Clearance in Process',
        'wc-gateway-processing' => 'Gateway Inventory - Processing Request',
        'wc-gateway-release' => 'Gateway Inventory - Release Requested',
        'wc-in-transit-country' => 'In Transit to Destination Country',
        'wc-package-abandoned' => 'Package Abandoned',
        'wc-preparing-shipment' => 'Preparing for shipment',
        'wc-processing-request' => 'Processing request',
        'wc-special-handling' => 'Special Handling',
        'wc-package-dangerous' => 'Package On-Hold – Dangerous Goods',
        'wc-package-hold-stored' => 'Package On Hold – Stored',
        'wc-proof-of-delivery' => 'Proof of Delivery',
    );

    foreach ($custom_statuses as $status => $label) {
        register_post_status($status, array(
            'label' => _x($label, 'Order status', 'woocommerce'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop($label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>', 'woocommerce'),
        ));
    }
}
add_action('init', 'custom_wc_register_custom_order_statuses');


function custom_wc_add_custom_order_statuses_to_dropdown($order_statuses) {
    
    $custom_statuses = array(
        'wc-arrived-at-station' => 'Arrived At Station',
        'wc-assigned-to-awb' => 'Assigned to AWB',
        'wc-awaiting-invoice' => 'Awaiting invoice or payment',
        'wc-customs-clearance' => 'Customs Clearance in Process',
        'wc-gateway-processing' => 'Gateway Inventory - Processing Request',
        'wc-gateway-release' => 'Gateway Inventory - Release Requested',
        'wc-in-transit-country' => 'In Transit to Destination Country',
        'wc-package-abandoned' => 'Package Abandoned',
        'wc-preparing-shipment' => 'Preparing for shipment',
        'wc-processing-request' => 'Processing request',
        'wc-special-handling' => 'Special Handling',
        'wc-package-dangerous' => 'Package On-Hold – Dangerous Goods',
        'wc-package-hold-stored' => 'Package On Hold – Stored',
        'wc-proof-of-delivery' => 'Proof of Delivery',
    );

    foreach ($custom_statuses as $status => $label) {
        $order_statuses[$status] = _x($label, 'Order status', 'woocommerce');
    }

    return $order_statuses;
}
add_filter('wc_order_statuses', 'custom_wc_add_custom_order_statuses_to_dropdown');


function custom_update_order_status($order_id) {
    $target_product_id = 26150; // Replace with the actual product ID of "Shipping with EasyShopUSA"
    $new_status = 'wc-awaiting-invoice'; // The desired new order status

    // Get the order object
    $order = wc_get_order($order_id);

    // Check if the order contains the specified product
    $contains_target_product = false;
    foreach ($order->get_items() as $item) {
        if ($item->get_product_id() === $target_product_id) {
            $contains_target_product = true;
            break;
        }
    }

    // If the order contains the specified product, update the status
    if ($contains_target_product) {
        $order->update_status($new_status);
    }
}
add_action('woocommerce_thankyou', 'custom_update_order_status');


// Add "Authorization Forms" Tab to My Account
function add_authorization_forms_endpoints() {
    add_rewrite_endpoint('authorization-forms', EP_PAGES);
    add_rewrite_endpoint('upload-authorization-form', EP_PAGES);
}

add_action('init', 'add_authorization_forms_endpoints');

// Add "Authorization Forms" Tab to My Account
function add_authorization_forms_tab($menu_items) {
    $menu_items['authorization-forms'] = 'Authorization Forms';
    return $menu_items;
}

add_filter('woocommerce_account_menu_items', 'add_authorization_forms_tab');


// Display content on "Authorization Forms" tab
function authorization_forms_content() {
    // Load the template part
    if ( file_exists( plugin_dir_path( __FILE__ ) . 'templates/my-account-tab/authorization-forms.php' ) ) {
        include( plugin_dir_path( __FILE__ ) . 'templates/my-account-tab/authorization-forms.php' );
    }
}

add_action('woocommerce_account_authorization-forms_endpoint', 'authorization_forms_content');

// Display content on "Authorization Forms" tab
function authorization_upload_content() {
    // Load the template part
    if ( file_exists( plugin_dir_path( __FILE__ ) . 'templates/my-account-tab/auth-upload-form.php' ) ) {
        include( plugin_dir_path( __FILE__ ) . 'templates/my-account-tab/auth-upload-form.php' );
    }
}

add_action('woocommerce_account_upload-authorization-form_endpoint', 'authorization_upload_content');