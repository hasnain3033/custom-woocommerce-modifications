<?php

// Generate a verification token and save it to user meta
function generate_verification_token( $user_id ) {
    $token = bin2hex( random_bytes( 20 ) );
    update_user_meta($user_id, 'verification_token', $token);
}
add_action('woocommerce_created_customer', 'generate_verification_token');


// Send the verification email
function send_verification_email( $user_id ) {
    $user = get_user_by('ID', $user_id);
    $email = $user->user_email;
    $verification_token = get_user_meta($user_id, 'verification_token', true);

    $subject = 'Verify your email address';
    $message = "Please click the link below to verify your email address:\n\n";
    $message .= esc_url(add_query_arg('token', $verification_token, site_url('/verify-email')));

    wp_mail($email, $subject, $message);
}
add_action('woocommerce_created_customer', 'send_verification_email');

// Create a custom endpoint for email verification
function custom_email_verification_endpoint() {
    add_rewrite_rule( '^verify-email/?', 'index.php?custom_email_verification=1', 'top' );
}
add_action( 'init', 'custom_email_verification_endpoint' );

// Handle the verification link
function handle_email_verification() {
    if ( isset( $_GET['token'] ) ) {
        $token = sanitize_text_field( $_GET['token'] );

        // Match the token and validate
        $user = get_users( array( 'meta_key' => 'verification_token', 'meta_value' => $token ) );

        if ( $user ) {
            // Mark the user as verified and remove the token
            $user_id = $user[0]->ID;
            delete_user_meta( $user_id, 'verification_token' );
            update_user_meta( $user_id, 'email_verified', true );

            // Redirect to a success page or display a success message
            wp_redirect( home_url( '/email-verified' ) );
            exit();
        }
    }
}
add_action( 'init', 'handle_email_verification' );

// Change the endpoint link in the verification email
function change_verification_link( $email_content, $user_id, $user_email ) {
    $verification_token = get_user_meta( $user_id, 'verification_token', true );
    $verification_link = esc_url( add_query_arg( 'token', $verification_token, home_url( '/verify-email' ) ) );

    $email_content = str_replace( 'http://easyshopusa.local/verify-email?token=', $verification_link, $email_content );

    return $email_content;
}
add_filter( 'woocommerce_email_content_customer_reset_password', 'change_verification_link', 10, 3 );


// Display a notice on the My Account page for unverified emails
function display_unverified_email_notice() {
    $user_id = get_current_user_id();
    $email_verified = get_user_meta( $user_id, 'email_verified', true );

    if( !$email_verified ) {
        echo '<div class="woocommerce-error">Your email is not verified. Please consider verifying your email for full access.</div>';
    }
}
add_action('woocommerce_account_content', 'display_unverified_email_notice' );