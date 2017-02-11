<?php
class WCMPS_Advance_Shipping_Admin {
  
  public $settings;

    public function __construct() {
            //admin script and style		
            add_action('wcmps_advance_shipping_dualcube_admin_footer', array(&$this, 'dualcube_admin_footer_for_wcmps_advance_shipping'));
            $this->load_class('settings');
            $this->settings = new WCMPS_Advance_Shipping_Settings();
            $settings = get_option('wcmp_wcmps_advance_shipping_general_settings_name');
            if(isset($settings['is_enable'])) {
                    add_action('admin_footer', array($this,'remove_order_and_class_calculation_type'),1000);
            }
    }

    function remove_order_and_class_calculation_type() {
            $screen = get_current_screen();
            if( $screen->id == 'woocommerce_page_shipping_zones') {		
                    ?>
                    <script type="text/javascript">
                    jQuery(document).ready(function($) {					
                            $('#woocommerce_table_rate_calculation_type option').each(function(e){
                                    if($(this).val() == '' || $(this).val() == 'class') {
                                            $(this).remove();
                                    }
                            });
                            var ele = $('input#woocommerce_table_rate_max_shipping_cost');
                            ele.parent().parent().parent().hide();
                    });			
                    </script>			
                    <?php			
            }
    }

    function load_class($class_name = '') {
      global $WCMPS_Advance_Shipping;
            if ('' != $class_name) {
                    require_once ($WCMPS_Advance_Shipping->plugin_path . '/admin/class-' . esc_attr($WCMPS_Advance_Shipping->token) . '-' . esc_attr($class_name) . '.php');
            } // End If Statement
    }// End load_class()
	
    function dualcube_admin_footer_for_wcmps_advance_shipping() {
        global $WCMPS_Advance_Shipping;
        ?>
        <div style="clear: both"></div>
        <div id="dc_admin_footer">
          <?php _e('Powered by', $WCMPS_Advance_Shipping->text_domain); ?> <a href="http://dualcube.com" target="_blank"><img src="<?php echo $WCMPS_Advance_Shipping->plugin_url.'/assets/images/dualcube.png'; ?>"></a><?php _e('Dualcube', $WCMPS_Advance_Shipping->text_domain); ?> &copy; <?php echo date('Y');?>
        </div>
        <?php
    }
	
}