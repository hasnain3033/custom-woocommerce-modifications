<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2><?php _e( 'Cart totals', 'custom-woocommerce-modifications' ); ?></h2>

	<table cellspacing="0" class="shop_table shop_table_responsive">

		<tr class="cart-base-subtotal">
			<th><?php _e( 'Base Price', 'custom-woocommerce-modifications' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Base Price', 'custom-woocommerce-modifications' ); ?>"><?php echo wc_price(calculate_cart_regular_price()); ?></td>
		</tr>
		<tr class="cart-duties">
			<th><?php _e( 'Duties', 'custom-woocommerce-modifications' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Duties', 'custom-woocommerce-modifications' ); ?>"><?php echo wc_price(calculate_cart_duties_values()); ?></td>
		</tr>
		<tr class="cart-admin">
			<th><?php _e( 'Admin Fee', 'custom-woocommerce-modifications' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Admin Fee', 'custom-woocommerce-modifications' ); ?>"><?php echo wc_price(calculate_cart_admin_values()); ?></td>
		</tr>
		<tr class="cart-pkg">
			<th><?php _e( 'Packaging Fee', 'custom-woocommerce-modifications' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Packaging Fee', 'custom-woocommerce-modifications' ); ?>"><?php echo wc_price(calculate_cart_pkg_values()); ?></td>
		</tr>
		<tr class="cart-subtotal">
			<th><?php _e( 'Subtotal', 'custom-woocommerce-modifications' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Subtotal', 'custom-woocommerce-modifications' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>
		<tr class="cart-shipping-expenses">
			<th><strong>Shipping Expenses</strong></th>
			<td data-title="<?php esc_attr_e( 'Shipping Expenses', 'custom-woocommerce-modifications' ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000" height="20px" width="20px" version="1.1" id="Layer_1" viewBox="0 0 330 330" xml:space="preserve">
					<path id="XMLID_225_" d="M325.607,79.393c-5.857-5.857-15.355-5.858-21.213,0.001l-139.39,139.393L25.607,79.393  c-5.857-5.857-15.355-5.858-21.213,0.001c-5.858,5.858-5.858,15.355,0,21.213l150.004,150c2.813,2.813,6.628,4.393,10.606,4.393  s7.794-1.581,10.606-4.394l149.996-150C331.465,94.749,331.465,85.251,325.607,79.393z"/>
				</svg>
			</td>
		</tr>
		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo $fee->name; ?></th>
				<td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) :
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping()
					? sprintf( ' <small>' . __( '(estimated for %s)', 'custom-woocommerce-modifications' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] )
					: '';

			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
					<tr class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
						<th><?php echo esc_html( $tax->label ) . $estimated_text; ?></th>
						<td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; ?></th>
					<td data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

		<tr class="order-total-kyd">
			<th><?php _e( 'Total in KYD', 'custom-woocommerce-modifications' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Total in KYD', 'custom-woocommerce-modifications' ); ?>"><?php echo wc_price(get_cart_total_value()); ?></td>
		</tr>
		<tr class="order-total">
			<th><?php _e( 'Total', 'custom-woocommerce-modifications' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Total in KYD', 'custom-woocommerce-modifications' ); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	</table>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
