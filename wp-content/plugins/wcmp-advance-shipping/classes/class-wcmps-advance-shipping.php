<?php
class WCMPS_Advance_Shipping {

	public $plugin_url;

	public $plugin_path;

	public $version;

	public $token;
	
	public $text_domain;
	
	public $library;

	public $shortcode;

	public $admin;

	public $frontend;

	public $template;

	public $ajax;

	private $file;
	
	public $settings;
	
	public $dc_wp_fields;
	
	public $wcmps_table_rate;
	
	public $weight_unit;

	public function __construct($file) {

		$this->file = $file;
		$this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
		$this->plugin_path = trailingslashit(dirname($file));
		$this->token = WCMPS_ADVANCE_SHIPPING_PLUGIN_TOKEN;
		$this->text_domain = WCMPS_ADVANCE_SHIPPING_TEXT_DOMAIN;
		$this->version = WCMPS_ADVANCE_SHIPPING_PLUGIN_VERSION;		
		add_action('init', array(&$this, 'init'), 15);		
	}
	
	/**
	 * initilize plugin on WP init
	 */
	function init() {
		global $WCMp;		
		// Init Text Domain
		$this->load_plugin_textdomain();
		
		// Init library
		$this->load_class('library');
		$this->library = new WCMPS_Advance_Shipping_Library();
    
    // init table rate 
		$this->load_class('table-rate');
		$this->wcmps_table_rate = new WCMPS_Advance_Shipping_Table_Rate();

		if (is_admin()) {
			$this->load_class('admin');
			$this->admin = new WCMPS_Advance_Shipping_Admin();
		}

		if (!is_admin() || defined('DOING_AJAX')) {
			$this->load_class('frontend');
			$this->frontend = new WCMPS_Advance_Shipping_Frontend();			
			// init shortcode
      $this->load_class('shortcode');
      $this->shortcode = new WCMPS_Advance_Shipping_Shortcode();
  
      // init templates
      $this->load_class('template');
      $this->template = new WCMPS_Advance_Shipping_Template();
		}
		
		$this->weight_unit = get_option('woocommerce_weight_unit');
		
		// DC Wp Fields
		$this->dc_wp_fields = $WCMp->wcmp_wp_fields;
	}
	
	/**
   * Load Localisation files.
   *
   * Note: the first-loaded translation file overrides any following ones if the same translation is present
   *
   * @access public
   * @return void
   */
  public function load_plugin_textdomain() {
    $locale = apply_filters( 'plugin_locale', get_locale(), $this->token );

    load_textdomain( $this->text_domain, WP_LANG_DIR . "/wcmps-advance-shipping/wcmps-advance-shipping-$locale.mo" );
    load_textdomain( $this->text_domain, $this->plugin_path . "/languages/wcmps-advance-shipping-$locale.mo" );
  }

	public function load_class($class_name = '') {
		if ('' != $class_name && '' != $this->token) {
			require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}// End load_class()
	
	/** Cache Helpers *********************************************************/

	/**
	 * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
	 *
	 * @access public
	 * @return void
	 */
	function nocache() {
		if (!defined('DONOTCACHEPAGE'))
			define("DONOTCACHEPAGE", "true");
		// WP Super Cache constant
	}

}
