<?php
/**
 * Error notices for woocommerce plugin not found
 */
if(!function_exists('wcmps_advance_shipping_woocommerce_inactive_notice')) {
	function wcmps_advance_shipping_woocommerce_inactive_notice() {
		?>
		<div id="message" class="error">
		<p><?php printf( __( '%sWCMPS Advance Shipping is inactive.%s The %sWooCommerce plugin%s must be active for the WCMPS Advance Shipping to work. Please %sinstall & activate WooCommerce%s', WCMPS_ADVANCE_SHIPPING_TEXT_DOMAIN ), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url( 'plugin-install.php?tab=search&s=woocommerce' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
		</div>
		<?php
	}
}
/**
 * Error notices for wc-marketplace plugin not found
 */
if(!function_exists('wcmps_advance_shipping_wcmp_inactive_notice')) {
	function wcmps_advance_shipping_wcmp_inactive_notice() {
		?>
		<div id="message" class="error">
		<p><?php printf( __( '%sWCMPS Advance Shipping is inactive.%s The %sWC Marketplace%s must be active for the WCMPS Advance Shipping to work. Please %sinstall & activate WC Marketplace%s', WCMPS_ADVANCE_SHIPPING_TEXT_DOMAIN ), '<strong>', '</strong>', '<a target="_blank" href="https://wordpress.org/plugins/dc-woocommerce-multi-vendor/">', '</a>', '<a href="' . admin_url( 'plugin-install.php?tab=search&s=wc+marketplace' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
		</div>
		<?php
	}
}
/**
 * Error notices for table rate shipping plugin not found
 */
if(!function_exists('wcmps_advance_shipping_table_rate_shipping_inactive_notice')) {
	function wcmps_advance_shipping_table_rate_shipping_inactive_notice() {
		?>
		<div id="message" class="error">
		<p><?php printf( __( '%sWCMPS Advance Shipping is inactive.%s The %sTable Rate Shipping%s must be active for the WCMPS Advance Shipping to work. Please %sinstall & activate Table Rate Shipping%s', WCMPS_ADVANCE_SHIPPING_TEXT_DOMAIN ), '<strong>', '</strong>', '<a target="_blank" href="https://www.woothemes.com/products/table-rate-shipping/">', '</a>', '<a href="' . admin_url( 'plugins.php?tab=search&s=table+rate+shipping' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
		</div>
		<?php
	}
}
/**
 * Return the wildcard key or value list from an array 
 *
 * @param array $array (default: array())
 * @param string $search (default: '')
 * @param string $return (default: '')
 * @return array 
 */
if(!function_exists('array_key_exists_wildcard')) {
	function array_key_exists_wildcard ( $array, $search, $return = '' ) {
		$search = str_replace( '\*', '.*?', preg_quote( $search, '/' ) );
		$result = preg_grep( '/^' . $search . '$/i', array_keys( $array ) );
		if ( $return == 'key-value' )
			return array_intersect_key( $array, array_flip( $result ) );
		return $result;
	}
}

if(!function_exists('is_wcmp_shipping_dashboard_page')) {
	function is_wcmp_shipping_dashboard_page() {
		$pages = get_option("wcmp_pages_settings_name");
		if(isset($pages['vendor_shipping'])) {
			$vendor_shipping = is_page( $pages['vendor_shipping'] ) ? true : false;
			return apply_filters('wcmps_advance_shipping_js_css_load', $vendor_shipping );
		}
		return false;
	}
}

/**
 * Return all shipping zone created by admin. 
 *
 * 
 * @return object 
 */
if(!function_exists('wcmps_get_table_rate_shipping_zone_list')) {
	function wcmps_get_table_rate_shipping_zone_list ( $zone_ids = array() ) {		
		global $wpdb;
                if(count($zone_ids)) {
                    $zone_list = array();
                    $zone_id = implode("','", $zone_ids);
                    $database_prefix = $wpdb->prefix;
                    $results = $wpdb->get_results( "SELECT * FROM {$database_prefix}woocommerce_shipping_zones WHERE zone_id IN ('{$zone_id}') order by 'zone_order'", OBJECT );
                    return $results;
                } else {
                    return array();
                }
	}
}

/**
 * Return all shipping zone created by admin. 
 *
 * 
 * @return object 
 */
if(!function_exists('get_wcmps_shipping_zone_methods_table_rate_field')) {
	function get_wcmps_shipping_zone_methods_table_rate_field ( $shipping_zone_id, $shipping_method, $user_id ) {
		global $WCMp, $WCMPS_Advance_Shipping, $wpdb;
                $shipping_class_id = get_user_meta($user_id, 'shipping_class_id', true);
		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_shipping_table_rates WHERE `rate_class` = {$shipping_class_id} AND `shipping_method_id`='{$shipping_method->instance_id}'", OBJECT );
                
		if(isset($results) && !empty($results)) {
                    foreach($results as $result) {
                        $shipp_data[] = (array)$result;
                    }                    
                } else {
                    $shipp_data[] = (array)$shipping_method;
                }	
                
		$shipping_class_id = get_user_meta($user_id, 'shipping_class_id', true);
		$rate_condition_options = array(''=>__('Please select condition', $WCMPS_Advance_Shipping->text_domain), 'price'=>__('Price',$WCMPS_Advance_Shipping->text_domain), 'weight'=>__('Weight',$WCMPS_Advance_Shipping->text_domain), 'items'=>__('Items',$WCMPS_Advance_Shipping->text_domain) );
		
		$fields = array(
			$shipping_method->instance_id => array('title' => __('Configure Table Rate Shipping', $WCMPS_Advance_Shipping->text_domain) , 'type' => 'multiinput', 'value'=>$shipp_data, 'options' => array( // Multi Input
				"rate_class" => array('title' => '', 'name'=>'rate_class', 'type' => 'hidden', 'value' => $shipping_class_id, 'dfvalue' => $shipping_class_id),
				"rate_id" => array('title' => '', 'name'=>'rate_id', 'type' => 'hidden' ),
				"rate_condition" => array('label' => __('Conditions', $WCMPS_Advance_Shipping->text_domain) , 'type' => 'select', 'label_for' => 'rate_condition', 'name' => 'rate_condition', 'options'=>$rate_condition_options, 'class' => 'regular-text'),
				"rate_min" => array('label' => __('Min', $WCMPS_Advance_Shipping->text_domain) , 'type' => 'text', 'label_for' => 'rate_min', 'name' => 'rate_min',  'class' => 'regular-text'),
				"rate_max" => array('label' => __('Max', $WCMPS_Advance_Shipping->text_domain) , 'type' => 'text', 'label_for' => 'rate_max', 'name' => 'rate_max',  'class' => 'regular-text'),
				"rate_cost" => array('label' => __('Row Cost', $WCMPS_Advance_Shipping->text_domain) , 'type' => 'text', 'label_for' => 'rate_cost', 'name' => 'rate_cost',  'class' => 'regular-text'),
				"rate_cost_per_item" => array('label' => __('Item Cost', $WCMPS_Advance_Shipping->text_domain) , 'type' => 'text', 'label_for' => 'rate_cost_per_item', 'name' => 'rate_cost_per_item',  'class' => 'regular-text'),
				"rate_cost_per_weight_unit" => array('label' => __(sprintf('%s Cost', ucfirst($WCMPS_Advance_Shipping->weight_unit)), $WCMPS_Advance_Shipping->text_domain) , 'type' => 'text', 'label_for' => 'rate_cost_per_weight_unit', 'name' => 'rate_cost_per_weight_unit',  'class' => 'regular-text'),
				"rate_cost_percent" => array('label' => __('Cost %', $WCMPS_Advance_Shipping->text_domain) , 'type' => 'text', 'label_for' => 'rate_cost_percent', 'name' => 'rate_cost_percent',  'class' => 'regular-text'),
				)
			)																																																				 
		);
		return $fields;
	}
}

if(!function_exists('get_advance_shipping_settings')) {
  function get_advance_shipping_settings($name = '', $tab = '') {
    if(empty($tab) && empty($name)) return '';
    if(empty($tab)) return get_option($name);
    if(empty($name)) return get_option("dc_{$tab}_settings_name");
    $settings = get_option("dc_{$tab}_settings_name");
    if(!isset($settings[$name])) return '';
    return $settings[$name];
  }
}
?>
