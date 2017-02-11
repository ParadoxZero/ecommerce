<?php
class WCMPS_Advance_Shipping_Library {
  
  public $lib_path;
  
  public $lib_url;
  
  public $php_lib_path;
  
  public $php_lib_url;
  
  public $jquery_lib_path;
  
  public $jquery_lib_url;
  
  public $wcmp_lib_path;
  
  public $wcmp_lib_url;

	public function __construct() {
	  global $WCMPS_Advance_Shipping, $WCMp;
	  
	  $this->lib_path = $WCMPS_Advance_Shipping->plugin_path . 'lib/';

    $this->lib_url = $WCMPS_Advance_Shipping->plugin_url . 'lib/';
    
    $this->wcmp_lib_path = $WCMp->plugin_path. 'lib/';
    
    $this->wcmp_lib_url = $WCMp->plugin_url. 'lib/';
    
    $this->php_lib_path = $this->lib_path . 'php/';
    
    $this->php_lib_url = $this->lib_url . 'php/';
    
    $this->jquery_lib_path = $this->lib_path . 'jquery/';
    
    $this->jquery_lib_url = $this->lib_url . 'jquery/';
    
    $this->wcmp_jquery_lib_path = $this->wcmp_lib_path . 'jquery/';
    
    $this->wcmp_jquery_lib_url = $this->wcmp_lib_url . 'jquery/';
    
    
	}
	
	/**
	 * PHP WP fields Library
	 */
	public function load_wp_fields() {
	  global $WCMPS_Advance_Shipping;
	  if ( !class_exists( 'WCMp_WP_Fields' ) )
	    require_once ($this->wcmp_lib_path . 'class-dc-wp-fields.php');
	  $WCMPS_WP_Fields = new WCMp_WP_Fields(); 
	  return $WCMPS_WP_Fields;
	}
	
	/**
	 * Jquery qTip library
	 */
	public function load_qtip_lib() {
	  global $WCMPS_Advance_Shipping;
	  wp_enqueue_script('qtip_js', $this->wcmp_jquery_lib_url . 'qtip/qtip.js', array('jquery'), $WCMPS_Advance_Shipping->version, true);
		wp_enqueue_style('qtip_css',  $this->wcmp_jquery_lib_url . 'qtip/qtip.css', array(), $WCMPS_Advance_Shipping->version);
	}
	
	/**
	 * WP Media library
	 */
	public function load_upload_lib() {
	  global $WCMPS_Advance_Shipping;
	  wp_enqueue_media();
	  wp_enqueue_script('upload_js', $this->wcmp_jquery_lib_url . 'upload/media-upload.js', array('jquery'), $WCMPS_Advance_Shipping->version, true);
	  wp_enqueue_style('upload_css',  $this->wcmp_jquery_lib_url . 'upload/media-upload.css', array(), $WCMPS_Advance_Shipping->version);
	}
	
	/**
	 * WP ColorPicker library
	 */
	public function load_colorpicker_lib() {
	  global $WCMPS_Advance_Shipping;
	  wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_script( 'colorpicker_init', $this->wcmp_jquery_lib_url . 'colorpicker/colorpicker.js', array( 'jquery', 'wp-color-picker' ), $WCMPS_Advance_Shipping->version, true );
    wp_enqueue_style( 'wp-color-picker' );
	}
	
}
