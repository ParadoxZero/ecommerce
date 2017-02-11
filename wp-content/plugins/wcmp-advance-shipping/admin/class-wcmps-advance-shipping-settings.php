<?php
class WCMPS_Advance_Shipping_Settings {
  
  private $tabs = array();  
  private $options;
  
  /**
   * Start up
   */
  public function __construct() {
  	global $WCMPS_Advance_Shipping;
  	// WCMp tabs
    add_filter("wcmp_tabs", array($this,'add_advance_shipping_tab'),10, 1);
    // Settings tabs
    add_action('settings_page_wcmps_advance_shipping_general_tab_init', array(&$this, 'general_tab_init'), 10, 1);    
  } 
  
  /**
   * Add a new tab in wcmp settings tab
   */
  public function add_advance_shipping_tab($tabs) {
  	global $WCMPS_Advance_Shipping;
		$tabs['wcmps_advance_shipping_general'] = __('Advance Shipping', $WCMPS_Advance_Shipping->text_domain);
		return $tabs;  	
  }
  
  /**
   * Add advance shipping settings page
   */
  function general_tab_init($tab) {
    global $WCMPS_Advance_Shipping;
    $WCMPS_Advance_Shipping->admin->load_class("settings-{$tab}", $WCMPS_Advance_Shipping->plugin_path, $WCMPS_Advance_Shipping->token);
    new WCMPS_Advance_Shipping_Settings_Gneral($tab);
  }
  
  
  
}