<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/wcmps-advance-shipping/wcmps-advance-shipping_template_table_rate.php
 *
 * @author 		dualcube
 * @package 	wcmps-advance-shipping/Templates
 * @version     0.0.1
 */
global $WCMPS_Advance_Shipping, $wpdb, $WCMp;
?>
<script type="text/javascript">
jQuery(document).ready(function($){
	$(function() {
		$( "#wcmp_advance_shipping_table_rate_accordiaon" ).accordion({
			heightStyle: "content"
		});
	});
});
</script>
<?php
$current_user = wp_get_current_user();
$settings = get_option('wcmp_wcmps_advance_shipping_general_settings_name');
$active_plugins = (array) get_option( 'active_plugins', array() );
$table_data_from_database = array();
$table_data_from_form = array();
$method_ids = array();
$previous_rate_ids = array();
$saved_data = array();
if(is_user_wcmp_vendor($current_user->ID)) {	
	if( isset($settings['is_enable']) && (in_array( 'wcmp-advance-shipping/advance_shipping.php', $active_plugins ) || array_key_exists( 'wcmp-advance-shipping/advance_shipping.php', $active_plugins )) && (in_array( 'woocommerce-table-rate-shipping/woocommerce-table-rate-shipping.php', $active_plugins ) || array_key_exists( 'woocommerce-table-rate-shipping/woocommerce-table-rate-shipping.php', $active_plugins )) && (in_array( 'dc-woocommerce-multi-vendor/dc_product_vendor.php', $active_plugins ) || array_key_exists( 'dc-woocommerce-multi-vendor/dc_product_vendor.php', $active_plugins )) ) {		
		$results_table_rate_method = $wpdb->get_results( "SELECT instance_id, method_id, zone_id FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE `method_id`='table_rate' ORDER BY method_order", OBJECT );
		$shipping_class_id = get_user_meta($current_user->ID, 'shipping_class_id', true);	
		$results_table_rate = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_shipping_table_rates WHERE `rate_class` = {$shipping_class_id} order by 'shipping_method_id' ", OBJECT );
		if(count($results_table_rate_method) > 0) {
			foreach( $results_table_rate_method as $method ) {
				$i = 0;
				foreach ( $results_table_rate as $rate ) {
					if($rate->shipping_method_id == $method->instance_id) {
						$previous_rate_ids[$rate->rate_id] = 'No';						
					}
					$i++;
				}
				$method_ids[] = $method->instance_id;	
                                $zone_ids[] = $method->zone_id;
			}			
		}
                if(isset($_POST) && !empty($_POST)) {
                    if(isset($_POST['vendor_shipping_data'])) {
                        unset($_POST['vendor_shipping_data']);
                    }
                    if(count($_POST) > 0) {
                        foreach( $_POST as $key => $post_data ) {
                            if(!empty($method_ids)) {
                                if(in_array($key, $method_ids)) {
                                    if(is_array($post_data) && !empty($post_data) && count($post_data) > 0) {
                                        foreach( $post_data as $data ) {
                                            if(is_array( $data ) && !empty($data)) {
                                                if($data['rate_id'] == '') {
                                                    $wpdb->insert( "{$wpdb->prefix}woocommerce_shipping_table_rates", 
                                                        array( 
                                                                'rate_class' => $shipping_class_id, 
                                                                'rate_condition' => $data['rate_condition'],
                                                                'rate_min' => $data['rate_min'], 
                                                                'rate_max' => $data['rate_max'],
                                                                'rate_cost' => $data['rate_cost'], 
                                                                'rate_cost_per_item' => $data['rate_cost_per_item'],
                                                                'rate_cost_per_weight_unit' => $data['rate_cost_per_weight_unit'], 
                                                                'rate_cost_percent' => $data['rate_cost_percent'],
                                                                'shipping_method_id' => $key
                                                        ), 
                                                        array( 
                                                                '%d', 
                                                                '%s',
                                                                '%d',
                                                                '%d',
                                                                '%s',
                                                                '%s',
                                                                '%s',
                                                                '%s',
                                                                '%d'
                                                        ) 
                                                    );
                                                } else {
                                                    if( !empty($data['rate_id'])) {
                                                        $wpdb->update( "{$wpdb->prefix}woocommerce_shipping_table_rates", 
                                                                array(
                                                                        'rate_class' => $shipping_class_id, 
                                                                        'rate_condition' => $data['rate_condition'],
                                                                        'rate_min' => $data['rate_min'], 
                                                                        'rate_max' => $data['rate_max'],
                                                                        'rate_cost' => $data['rate_cost'], 
                                                                        'rate_cost_per_item' => $data['rate_cost_per_item'],
                                                                        'rate_cost_per_weight_unit' => $data['rate_cost_per_weight_unit'], 
                                                                        'rate_cost_percent' => $data['rate_cost_percent']
                                                                ), 
                                                                array( 'rate_id' => $data['rate_id'] ),
                                                                array( 
                                                                        '%d', 
                                                                        '%s',
                                                                        '%d',
                                                                        '%d',
                                                                        '%s',
                                                                        '%s',
                                                                        '%s',
                                                                        '%s'
                                                                ), 
                                                                array( 
                                                                        '%d' 
                                                                ) 
                                                        );
                                                        $previous_rate_ids[$data['rate_id']] = 'Yes';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if(!empty($previous_rate_ids) && is_array($previous_rate_ids) && count($previous_rate_ids) > 0) {
                        foreach( $previous_rate_ids as $rate_key => $status ) {
                                if($status == "No") {
                                        $wpdb->delete( "{$wpdb->prefix}woocommerce_shipping_table_rates", array( 'rate_id' => $rate_key ), array( '%d' ) );
                                }
                        }					
                    }
                }
                
		$shipping_zones = wcmps_get_table_rate_shipping_zone_list($zone_ids); 
		if(count($shipping_zones) > 0) {
		?>		
		<tr>
			<td><label><?php echo __('Table Rate Shipping',$WCMPS_Advance_Shipping->text_domain); ?></label></td>
		</tr>
		<tr>
			<td>
				<div id="wcmp_advance_shipping_table_rate_accordiaon">
					<?php foreach( $shipping_zones as $shipping_zone ) {?>
						<h2>Shipping Zone : <?php echo $shipping_zone->zone_name; ?></h2>
						<div>
                                                    <?php
                                                        $methods = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE `zone_id` = {$shipping_zone->zone_id} AND `method_id`='table_rate'", OBJECT );
                                                        if(count($methods)) {
                                                            foreach($methods as $method) {                                                                
                                                                $option = 'woocommerce_'.$method->method_id.'_'.$method->instance_id.'_settings';
                                                                $method_details = get_option($option, TRUE);
                                                    ?>
                                                    <label><?php echo __('Enter "'.(isset($method_details['title']) ? $method_details['title'] : $method->method_id).'" Shipping Cost', $WCMPS_Advance_Shipping->text_domain); ?></label>
                                                    <table class="form-table">
                                                            <tbody>
                                                                            <?php $WCMp->wcmp_wp_fields->dc_generate_form_field( get_wcmps_shipping_zone_methods_table_rate_field( $shipping_zone->zone_id, $method, $current_user->ID ), array('in_table' => 1) ); ?>
                                                            </tbody>
                                                    </table>
                                                        <?php } } ?>
						</div>
					<?php }?>		
				</div>
			</td>
		</tr>							
		<?php
		}
		else {
			echo "<p>".__('sorry no shipping zone created ',$WCMPS_Advance_Shipping->text_domain)."</p>";
		}
	}	
}
else {
	echo "<p>".__('sorry only vendor allowed ',$WCMPS_Advance_Shipping->text_domain)."</p>";
}
?>
