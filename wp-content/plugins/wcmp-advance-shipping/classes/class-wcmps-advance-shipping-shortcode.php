<?php
class WCMPS_Advance_Shipping_Shortcode {

	public $list_product;

	public function __construct() {
		//shortcodes
		add_shortcode('wcmps_advance_shipping_table_rate', array(&$this, 'wcmps_advance_shipping_table_rate_shortcode'));
	}
	
	public function wcmps_advance_shipping_table_rate_shortcode($attr) {
		global $WCMPS_Advance_Shipping;
		$this->load_class('table-rate-shortcode');
		return $this->shortcode_wrapper(array('WCMPS_Advance_shipping_Table_Rate_Shortcode', 'output'));
	}

	

	/**
	 * Helper Functions
	 */

	/**
	 * Shortcode Wrapper
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public function shortcode_wrapper($function, $atts = array()) {
		ob_start();
		call_user_func($function, $atts);
		return ob_get_clean();
	}

	/**
	 * Shortcode CLass Loader
	 *
	 * @access public
	 * @param mixed $class_name
	 * @return void
	 */
	public function load_class($class_name = '') {
		global $WCMPS_Advance_Shipping;
		if ('' != $class_name && '' != $WCMPS_Advance_Shipping->token) {
			require_once ('shortcode/class-' . esc_attr($WCMPS_Advance_Shipping->token) . '-shortcode-' . esc_attr($class_name) . '.php');
		}
	}

}
?>
