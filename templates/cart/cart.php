<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
    <div class="cust-woocommerce-cart-form-col">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

    <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
        <thead>
        <tr>
            <!--<th class="product-thumbnail">&nbsp;</th>-->
            <th class="product-name"><?php esc_html_e( 'Product', 'martfury' ); ?></th>
            <th class="product-price"><?php esc_html_e( 'Price', 'martfury' ); ?></th>
            <th class="product-quantity"><?php esc_html_e( 'Quantity', 'martfury' ); ?></th>
            <!-- <th class="product-subtotal" ><?php //esc_html_e( 'Product Total', 'martfury' ); ?><span class="tooltip-trigger">?
			<div class="custom-tooltip">
				This is total with duties and etc.
			</div>
			</span></th> -->
            <th class="product-remove">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
				/**
				 * Filter the product name.
				 *
				 * @since 7.8.0
				 * @param string $product_name Name of the product in the cart.
				 */
				$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				?>
                <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                    <td class="product-thumbnail">
						<?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'thumbnail' ), $cart_item, $cart_item_key );

						if ( ! $product_permalink ) {
							echo wp_kses_post( $thumbnail );
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) );
						}
						?>
							<?php
						echo apply_filters(
							'woocommerce_cart_item_remove_link', sprintf(
							'<a href="%s" class="mf-remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
  <path fill-rule="evenodd" clip-rule="evenodd" d="M12.5 22.9167C18.253 22.9167 22.9167 18.253 22.9167 12.5C22.9167 6.74704 18.253 2.08333 12.5 2.08333C6.74704 2.08333 2.08333 6.74704 2.08333 12.5C2.08333 18.253 6.74704 22.9167 12.5 22.9167ZM12.5 25C19.4035 25 25 19.4035 25 12.5C25 5.59644 19.4035 0 12.5 0C5.59644 0 0 5.59644 0 12.5C0 19.4035 5.59644 25 12.5 25Z" fill="#1B1B1B"/>
  <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9193 8.08061C17.3261 8.48742 17.3261 9.14696 16.9193 9.55375L9.55363 16.9195C9.14684 17.3263 8.4873 17.3263 8.08048 16.9195C7.67369 16.5126 7.67369 15.8531 8.08048 15.4463L15.4462 8.08061C15.853 7.67381 16.5125 7.67381 16.9193 8.08061Z" fill="#1B1B1B"/>
  <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9193 16.9194C16.5125 17.3262 15.853 17.3262 15.4462 16.9194L8.0805 9.55375C7.67369 9.14694 7.67369 8.4874 8.0805 8.08061C8.48729 7.67381 9.14683 7.67381 9.55362 8.08061L16.9193 15.4463C17.3261 15.8531 17.3261 16.5126 16.9193 16.9194Z" fill="#1B1B1B"/>
</svg></a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							esc_html__( 'Remove', 'martfury' ),
							esc_attr( $product_id ),
							esc_attr( $_product->get_sku() )
						), $cart_item_key
						);
						?>
                    </td>

<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'martfury' ); ?>">
    <?php
    if ( ! $product_permalink ) {
        /**
         * Filter the product name.
         *
         * @since 7.8.0
         * @param string $product_name Name of the product in the cart.
         * @param array $cart_item The product in the cart.
         * @param string $cart_item_key Key for the product in the cart.
         */
        $product_name = wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $product_name, $cart_item, $cart_item_key ) );
        $trimmed_product_name = wp_trim_words( $product_name, 7 );
        echo $trimmed_product_name . '&nbsp;';
    } else {
        /**
         * Filter the product name.
         *
         * @since 7.8.0
         * @param string $product_url URL the product in the cart.
         */
        $product_name = apply_filters( 'woocommerce_cart_item_name', $product_name, $cart_item, $cart_item_key );
        $trimmed_product_name = wp_trim_words( $product_name, 7 );
        echo sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $trimmed_product_name );
    }

    do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

    // Meta data.
    echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

    // Backorder notification.
    if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
        echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'martfury' ) . '</p>', $product_id ) );
    }
    ?>
</td>


                    <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'martfury' ); ?>">
                        <?php
                            $regular_price = get_post_meta($product_id, '_regular_price', true);
                        ?>
						<?php
						echo apply_filters( 'woocommerce_cart_item_price', wc_price($regular_price), $cart_item, $cart_item_key );
						// echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						?>
                    </td>

                    <td class="product-quantity" data-title="Quantity">
						<?php
						if ( $_product->is_sold_individually() ) {
							$min_quantity = 1;
							$max_quantity = 1;
						} else {
							$min_quantity = 0;
							$max_quantity = $_product->get_max_purchase_quantity();
						}

						$product_quantity = woocommerce_quantity_input(
							array(
								'input_name'   => "cart[{$cart_item_key}][qty]",
								'input_value'  => $cart_item['quantity'],
								'max_value'    => $max_quantity,
								'min_value'    => $min_quantity,
								'product_name' => $_product->get_name(),
							), $_product, false );


						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
						?>

                    </td>

                    <!-- <td class="product-subtotal" data-title="<?php esc_attr_e( 'Total', 'martfury' ); ?>">
						<?php
						//echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						?>
                    </td> -->
                    <td class="product-remove" >
						<?php
						echo apply_filters(
							'woocommerce_cart_item_remove_link', sprintf(
							'<a href="%s" class="mf-remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
  <path fill-rule="evenodd" clip-rule="evenodd" d="M12.5 22.9167C18.253 22.9167 22.9167 18.253 22.9167 12.5C22.9167 6.74704 18.253 2.08333 12.5 2.08333C6.74704 2.08333 2.08333 6.74704 2.08333 12.5C2.08333 18.253 6.74704 22.9167 12.5 22.9167ZM12.5 25C19.4035 25 25 19.4035 25 12.5C25 5.59644 19.4035 0 12.5 0C5.59644 0 0 5.59644 0 12.5C0 19.4035 5.59644 25 12.5 25Z" fill="#1B1B1B"/>
  <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9193 8.08061C17.3261 8.48742 17.3261 9.14696 16.9193 9.55375L9.55363 16.9195C9.14684 17.3263 8.4873 17.3263 8.08048 16.9195C7.67369 16.5126 7.67369 15.8531 8.08048 15.4463L15.4462 8.08061C15.853 7.67381 16.5125 7.67381 16.9193 8.08061Z" fill="#1B1B1B"/>
  <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9193 16.9194C16.5125 17.3262 15.853 17.3262 15.4462 16.9194L8.0805 9.55375C7.67369 9.14694 7.67369 8.4874 8.0805 8.08061C8.48729 7.67381 9.14683 7.67381 9.55362 8.08061L16.9193 15.4463C17.3261 15.8531 17.3261 16.5126 16.9193 16.9194Z" fill="#1B1B1B"/>
</svg></a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							esc_html__( 'Remove', 'martfury' ),
							esc_attr( $product_id ),
							esc_attr( $_product->get_sku() )
						), $cart_item_key
						);
						?>
                    </td>
                </tr>
				<?php
			}
		}
		?>

		<?php do_action( 'woocommerce_cart_contents' ); ?>

        <tr>
            <td colspan="6" class="actions">
				<?php do_action( 'woocommerce_cart_actions' ); ?>
	<button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>">
	    <svg xmlns="http://www.w3.org/2000/svg" width="27" height="26" viewBox="0 0 27 26" fill="none">
          <path d="M11.9974 8.30243V13.6189C11.9974 14.1246 12.2824 14.6013 12.7325 14.8614L17.4133 17.5341C17.9534 17.8375 18.6435 17.6641 18.9586 17.1584C19.2736 16.6383 19.1086 15.9738 18.5685 15.6704L14.2628 13.2V8.28799C14.2478 7.71011 13.7377 7.21891 13.1226 7.21891C12.5075 7.21891 11.9974 7.71011 11.9974 8.30243ZM27 9.38596V1.7435C27 1.09338 26.1899 0.77555 25.7248 1.23785L23.0543 3.80942C20.3388 1.19451 16.4681 -0.307974 12.2374 0.0532007C5.9513 0.602186 0.775386 5.47082 0.085265 11.5241C-0.814893 19.3544 5.53122 26 13.4976 26C20.3838 26 26.0698 21.0302 26.895 14.6158C27 13.749 26.2949 12.9977 25.3947 12.9977C24.6446 12.9977 24.0145 13.5322 23.9245 14.2401C23.2793 19.2821 18.7636 23.1828 13.3476 23.1106C7.78162 23.0384 3.08579 18.5165 2.99578 13.1422C2.90576 7.50785 7.6616 2.88481 13.4976 2.88481C16.3931 2.88481 19.0186 4.02612 20.9239 5.84645L17.7884 8.86587C17.3083 9.32817 17.6384 10.1083 18.3135 10.1083H26.2499C26.6699 10.1083 27 9.79047 27 9.38596Z" fill="url(#paint0_linear_163_150)"/>
          <defs>
            <linearGradient id="paint0_linear_163_150" x1="0" y1="26.0001" x2="27.45" y2="26.0001" gradientUnits="userSpaceOnUse">
              <stop stop-color="#E73A84"/>
              <stop offset="0.260417" stop-color="#E73A84"/>
              <stop offset="1" stop-color="#F7982C"/>
            </linearGradient>
          </defs>
        </svg>
	    <?php esc_html_e( 'Update cart', 'woocommerce' ); ?>
	 </button>
				<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
            </td>
        </tr>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
        </tbody>
    </table>
    <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
<div class="cart-collaterals">
    <div class="row">
		<?php
		$cart_class = 'col-md-8 col-sm-12 col-colla';
 ?>
        <div class="<?php echo esc_attr( $cart_class ); ?>">
			<?php do_action( 'woocommerce_cart_collaterals' ); ?>
        </div>
    </div>

</div>
    </div>
    <div class="cust-woocommerce-cart-form-col cust-col-second">
        <div class="cart-right-img">
            <img src="https://ky.easyshopusa.com/wp-content/uploads/2023/09/Frame-3.png" alt="" />
        </div>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
	<?php if ( wc_coupons_enabled() ) { ?>
        <div class="row">
            <div class="col-md-4 col-sm-12 col-coupon">
                <div class="coupon">
                    <label for="coupon_code"><?php esc_html_e( 'Coupon Discount', 'martfury' ); ?></label>
                    <input type="text" name="coupon_code" class="input-text" id="coupon_code" value=""
                           placeholder="<?php esc_attr_e( 'Coupon code', 'martfury' ); ?>"/>
                    <input type="submit" class="button" name="apply_coupon"
                           value="<?php esc_attr_e( 'Apply', 'martfury' ); ?>"/>
					<?php do_action( 'woocommerce_cart_coupon' ); ?>
                </div>
            </div>
        </div>
	<?php } ?>
	
	 </div>
</form>


<?php do_action( 'woocommerce_after_cart' ); ?>
