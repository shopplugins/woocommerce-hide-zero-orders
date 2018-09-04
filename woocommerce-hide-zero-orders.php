<?php
/**
 * Plugin Name: WooCommerce Hide Zero Orders
 * Plugin URI: https://github.com/shopplugins/woocommerce-hide-zero-orders
 * Description: Adds a filter to the Edit Orders admin page to show all or show non-zero orders. Handy to hide $0 orders.
 * Version: 1.0.0
 * Author: Shop Plugins
 * Author URI: https://shopplugins.com
 * Text Domain: sp-hide-zero-orders
 */

if ( is_admin() ) {
	add_action( 'restrict_manage_posts', 'sp_restrict_orders', 50 );
	function sp_restrict_orders() {
		global $typenow;

		if ( 'shop_order' != $typenow ) {
			return;
		}

		?>
		<select name='sp_order_view' id='dropdown_sp_order_view'>
			<option <?php
				if ( isset( $_GET['sp_order_view'] ) && $_GET['sp_order_view'] ) {
					selected( 'all', $_GET['sp_order_view'] );
				}
			?> value="all"><?php esc_html_e( 'Show all orders', 'sp-hide-zero-orders' ); ?></option>
			<option <?php
			        if ( isset( $_GET['sp_order_view'] ) && $_GET['sp_order_view'] ) {
				        selected( 'non-zero', $_GET['sp_order_view'] );
			        }
			        ?>value="non-zero"><?php esc_html_e( 'Show non-zero orders', 'sp-hide-zero-orders' ); ?></option>
		</select>
		<?php
	}

	add_filter( 'request', 'sp_orders_by_restrict_option', 100 );
	function sp_orders_by_restrict_option( $vars ) {
		global $typenow;
		$key = 'post__not_in';
		if ( 'shop_order' == $typenow && isset( $_GET['sp_order_view'] ) ) {
			if ( 'non-zero' == $_GET['sp_order_view'] ) {
				if ( ! empty( $key ) ) {
					$vars[ $key ] = get_posts( array(
						'posts_per_page' => -1,
						'post_type'      => 'shop_order',
						'post_status'    => 'any',
						'fields'         => 'ids',
						'orderby'        => 'date',
						'order'          => 'DESC',
						'meta_query'     => array(
							array(
								'key'     => '_order_total',
								'value'   => '0.00',
								'compare' => '=',
							),
						),
					) );

				}

			}

		}

		return $vars;
	}
}