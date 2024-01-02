<?php
/**
 * Plugin Name: TuMeNy Mobile Payment
 * Plugin URI: https://tumenypay.com
 * Author: Nsisong E.O
 * Author URI: https://nsisongeo.info
 * Description: Mobile Payments Gateway in Zambia.
 * Version: 0.1.0
 * License: GPL2
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: wc-tumeny
 *
 * @package WooCommerce\Tumeny
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

add_action( 'plugins_loaded', 'neo_wc_gateway_tumeny_payment_init', 66 );
//add_filter( 'woocommerce_currencies', 'techiepress_add_ugx_currencies' );
//add_filter( 'woocommerce_currency_symbol', 'techiepress_add_ugx_currencies_symbol', 10, 2 );
add_filter( 'woocommerce_payment_gateways', 'neo_wc_gateway_tumeny_add_payment_gateway');

function neo_wc_gateway_tumeny_payment_init() {
    if( class_exists( 'WC_Payment_Gateway' ) ) {
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-wc-gateway-tumeny.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-wc-gateway-tumeny-api-request.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/constants/payment_status.php';
	}
}

function neo_wc_gateway_tumeny_add_payment_gateway( $gateways ) {
    $gateways[] = 'WC_Gateway_Tumeny';
    return $gateways;
}

//function techiepress_add_ugx_currencies( $currencies ) {
//	$currencies['UGX'] = __( 'Ugandan Shillings', 'payleo-payments-woo' );
//	return $currencies;
//}
//
//function techiepress_add_ugx_currencies_symbol( $currency_symbol, $currency ) {
//	switch ( $currency ) {
//		case 'UGX':
//			$currency_symbol = 'UGX';
//		break;
//	}
//	return $currency_symbol;
//}

/**
 * Registers WooCommerce Blocks integration.
 */
function neo_wc_gateway_tumeny_block_support() {
    if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
        require_once __DIR__ . '/includes/class-wc-gateway-tumeny-blocks-support.php';
        add_action(
            'woocommerce_blocks_payment_method_type_registration',
            static function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
                $payment_method_registry->register( new WC_Gateway_Tumeny_Blocks_Support() );
            }
        );
    }
}
add_action( 'woocommerce_blocks_loaded', 'neo_wc_gateway_tumeny_block_support' );