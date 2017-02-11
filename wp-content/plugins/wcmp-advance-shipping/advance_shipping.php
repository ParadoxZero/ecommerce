<?php
/*
Plugin Name: WCMp Advance Shipping
Plugin URI: http://wc-marketplace.com/
Description: A free Addon Bridging WC Marketplace and Wc Table Rate Shipping.
Author: WC Marketplace, The Grey Parrots
Version: 1.0.2
Author URI: http://wc-marketplace.com/
*/

if ( ! class_exists( 'WCMps_Dependencies' ) ) {
	require_once trailingslashit(dirname(__FILE__)).'includes/class-wcmps-dependencies.php';
}
require_once trailingslashit(dirname(__FILE__)).'includes/wcmps-advance-shipping-core-functions.php';
require_once trailingslashit(dirname(__FILE__)).'wcmps-advance-shipping-config.php';
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if(!defined('WCMPS_ADVANCE_SHIPPING_PLUGIN_TOKEN')) exit;
if(!defined('WCMPS_ADVANCE_SHIPPING_TEXT_DOMAIN')) exit;

if(!WCMps_Dependencies::woocommerce_plugin_active_check()) {
	add_action( 'admin_notices', 'wcmps_advance_shipping_woocommerce_inactive_notice' );
}
	
elseif(!WCMps_Dependencies::wc_marketplace_plugin_active_check()) {
	add_action( 'admin_notices', 'wcmps_advance_shipping_wcmp_inactive_notice' );
}
elseif(!WCMps_Dependencies::table_rate_shipping_plugin_active_check()) {
	add_action( 'admin_notices', 'wcmps_advance_shipping_table_rate_shipping_inactive_notice' );
}
else {
	if(!class_exists('WCMPS_Advance_Shipping')) {
		require_once( trailingslashit(dirname(__FILE__)).'classes/class-wcmps-advance-shipping.php' );
		global $WCMPS_Advance_Shipping;
		$WCMPS_Advance_Shipping = new WCMPS_Advance_Shipping( __FILE__ );
		$GLOBALS['WCMPS_Advance_Shipping'] = $WCMPS_Advance_Shipping;
	}
}
?>
