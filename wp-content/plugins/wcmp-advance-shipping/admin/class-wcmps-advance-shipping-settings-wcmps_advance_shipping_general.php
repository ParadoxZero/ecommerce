<?php
class WCMPS_Advance_Shipping_Settings_Gneral {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  
  private $tab;

  /**
   * Start up
   */
  public function __construct($tab) {
    $this->tab = $tab;
    $this->options = get_option( "wcmp_{$this->tab}_settings_name" );
    $this->settings_page_init();    
  }
  
  /**
   * Register and add settings
   */
  public function settings_page_init() {
    global $WCMPS_Advance_Shipping, $WCMp;    
    $settings_tab_options = array("tab" => "{$this->tab}",
                                  "ref" => &$this,
                                  "sections" => array(
                                                      "table_rate_shipping_settings_section" => array("title" =>  __('Table Rate Shipping', $WCMPS_Advance_Shipping->text_domain), // Section one
                                                                                         "fields" => array(                                                                                          
                                                                                                           "is_enable" => array('title' => __('Enable', $WCMPS_Advance_Shipping->text_domain), 'type' => 'checkbox', 'id' => 'is_enable', 'label_for' => 'is_enable', 'name' => 'is_enable', 'value' => 'Enable'), // Checkbox
                                                                                                           "disable_from_frontend_shipping" => array('title' => __('Disable Table Rate Shipping', $WCMPS_Advance_Shipping->text_domain), 'type' => 'checkbox', 'id' => 'disable_from_frontend_shipping', 'label_for' => 'disable_from_frontend_shipping', 'name' => 'disable_from_frontend_shipping', 'value' => 'Enable', 'desc'=>__('Disable table rate shipping from frontend for vendor setting purpose',$WCMPS_Advance_Shipping->text_domain)), // Checkbox
                                                                                                                                                                                                                
                                                                                                           )
                                                                                         )
                                                      
                                                      )
                                  );
    
    $WCMp->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function wcmp_wcmps_advance_shipping_general_settings_sanitize( $input ) {
    global $WCMPS_Advance_Shipping;
    $new_input = array();
    
    $hasError = false;  

    if( isset( $input['is_enable'] ) )
      $new_input['is_enable'] = sanitize_text_field( $input['is_enable'] ); 
    if( isset( $input['disable_from_frontend_shipping'] ) )
    	$new_input['disable_from_frontend_shipping'] = sanitize_text_field( $input['disable_from_frontend_shipping'] );
    
    if(!$hasError) {
      add_settings_error(
        "wcmp_{$this->tab}_settings_name",
        esc_attr( "wcmp_{$this->tab}_settings_admin_updated" ),
        __('Advance shipping settings updated', $WCMPS_Advance_Shipping->text_domain),
        'updated'
      );
    }

    return $new_input;
  }

  /** 
   * Print the Section text
   */
  public function table_rate_shipping_settings_section_info() {
    global $WCMPS_Advance_Shipping;
    _e('Please configure the table rate shipping', $WCMPS_Advance_Shipping->text_domain);
  } 
  
  
}