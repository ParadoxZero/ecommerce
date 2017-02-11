<?php
class WCMPS_Advance_shipping_Table_Rate_Shortcode {

	public function __construct() {

	}

	/**
	 * Output the demo shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public function output( $attr ) {
		global $WCMPS_Advance_Shipping;
		$WCMPS_Advance_Shipping->nocache();		
		do_action('wcmps_advance_shipping_template_table_rate');	
	}
}
