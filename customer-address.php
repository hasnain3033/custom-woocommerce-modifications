<?php

function generate_user_code() {
    // Logic to generate a unique identifier (you can customize this based on your requirements)
    $unique_id = rand(10000, 99999); // Generate a random number between 10000 and 99999
    $user_code = 'GCM-' . $unique_id;

    return $user_code;
}

function generate_and_save_user_code($user_id) {
    // Check if the user has custom role

    $user = get_userdata($user_id);
    if(in_array('customer', $user->roles)) {
        // Generate the user code
        $user_code = generate_user_code();

        // Save the user code in a meta field
        update_user_meta($user_id, 'user_code', $user_code);

        // Log the user code
        error_log('User code for user ID ' . $user_id . ': ' . $user_code ); 
    }
}
add_action('user_register', 'generate_and_save_user_code');

// Display a notice on the My Account page for unverified emails
function add_user_code_to_dashboard() {
    $user_id = get_current_user_id();
    $customer_address = get_user_meta($user_id, 'user_code', true);

    if( $customer_address ) { ?>
        <div class="woocommerce-message">
        <p>This is your U.S. address: <strong>1 Aeropost Way, <?php echo $customer_address; ?>, Miami, FL 33206, USA</strong> </p>
        </div>
    <?php }
}
add_action('woocommerce_account_content', 'add_user_code_to_dashboard' );