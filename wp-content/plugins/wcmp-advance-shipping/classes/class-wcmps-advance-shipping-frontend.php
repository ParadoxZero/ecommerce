<?php
class WCMPS_Advance_Shipping_Frontend {

	public function __construct() {
		//enqueue scripts
		add_action('wp_enqueue_scripts', array(&$this, 'frontend_scripts'));
		//enqueue styles
		add_action('wp_enqueue_scripts', array(&$this, 'frontend_styles'));
		add_action( 'wcmps_advance_shipping_frontend_hook', array(&$this, 'wcmps_advance_shipping_frontend_function'), 10, 2 );
	}

	function frontend_scripts() {
		global $WCMPS_Advance_Shipping;
		$frontend_script_path = $WCMPS_Advance_Shipping->plugin_url . 'assets/frontend/js/';
		$frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
		$pluginURL = str_replace( array( 'http:', 'https:' ), '', $WCMPS_Advance_Shipping->plugin_url );
		$suffix 				= defined( 'WCMPS_ADVANCE_SHIPPING_SCRIPT_DEBUG' ) && WCMPS_ADVANCE_SHIPPING_SCRIPT_DEBUG ? '' : '.min';
		if(is_wcmp_shipping_dashboard_page()) {
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-accordion');				
			wp_enqueue_script('wcmps_advance_shipping_multiinput_js', $WCMPS_Advance_Shipping->plugin_url.'assets/frontend/js/multiinput.js', array('jquery'), $WCMPS_Advance_Shipping->version, true);
		}		
		// Enqueue your frontend javascript from here
	}

	function frontend_styles() {
		global $WCMPS_Advance_Shipping;
		$frontend_style_path = $WCMPS_Advance_Shipping->plugin_url . 'assets/frontend/css/';
		$frontend_style_path = str_replace( array( 'http:', 'https:' ), '', $frontend_style_path );
		$suffix 				= defined( 'WCMPS_ADVANCE_SHIPPING_SCRIPT_DEBUG' ) && WCMPS_ADVANCE_SHIPPING_SCRIPT_DEBUG ? '' : '.min';
		if(is_wcmp_shipping_dashboard_page()) {
			wp_enqueue_style('wcmps_advance_shipping_ui_css',  $WCMPS_Advance_Shipping->plugin_url . 'assets/frontend/css/jquery-ui.css', array(), $WCMPS_Advance_Shipping->version);
			wp_enqueue_style('wcmps_advance_shipping',  $WCMPS_Advance_Shipping->plugin_url . 'assets/frontend/css/frontend.css', array(), $WCMPS_Advance_Shipping->version);
		}		
		// Enqueue your frontend stylesheet from here
	}
	
	function dc_wcmps_advance_shipping_frontend_function() {
	  // Do your frontend work here
	  
	}
}
