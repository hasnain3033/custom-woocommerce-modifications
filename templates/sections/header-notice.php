<?php

function three_column_section_shortcode() {
    ob_start(); // Start output buffering

    // HTML for the three-column section
    ?>
    <section class="banner-noctice">
    <div class="container">
        <div class="column marketplace-column">
            <p>Currently you’re in</p>
            <h4>EasyshopUSA Marketplace</h4>
        </div>
        <div class="column address-text-cust">
          <?php if (is_user_logged_in()) : 
                    $user = wp_get_current_user();
                    $user_id = get_current_user_id();
                    // Check if the user has the 'customer' role
                    if(in_array('customer', (array) $user->roles)) :
                        $customer_address = get_user_meta($user_id, 'user_code', true);
                    ?>
                        <p>This is your U.S. address: <strong>1 Aeropost Way, <?php echo $customer_address; ?>, Miami, FL 33206, USA</strong> </p>
                    <?php else : ?>
                        <p>You can also shop using a U.S. address with <a href="/my-account/">EasyshopUSA Courier</a></p>
                    <?php endif; 
                else:
                ?>
                <p>You can also shop using a U.S. address with <a href="/my-account/">EasyshopUSA Courier</a></p>
            <?php 
                endif;
            ?>
        </div>
        <div class="column">
            <a href="/shop/" class="bnner-tp-btn">Shop Now</a>
        </div>
    </div>
    </section>
    <?php

    return ob_get_clean(); // Return the buffered content
}
add_shortcode('three_column_section', 'three_column_section_shortcode');
