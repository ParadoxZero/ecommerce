<?php
class WCMPS_Advance_Shipping_Table_Rate {
	
	public $table_rate_obj;	
	public $tax;	
	public $WC_Shipping_Table_Rate_obj;	
	public $rates_table;	
	public $number;	
	public $fee;
	public $order_handling_fee;
	public $tax_status;
	public $calculation_type;
	public $min_cost;
	public $max_cost;
	public $max_shipping_cost;
        public $give_tax_to_vendor = false;
        public $give_shipping_to_vendor = false;
        
        /**
	 * Minimum fee for the method (if applicable).
	 * @var string
	 */
	public $minimum_fee = null;
	
	/**	 
	 * Class WCMPS_Advance_Shipping_Table_Rate contructor 
	 * @return void 
	 * @param  void
	 */
	public function __construct() {
		global $wpdb;		
		$settings = get_option('wcmp_wcmps_advance_shipping_general_settings_name');
		if(isset($settings['is_enable'])) {		
			$this->tax          = new WC_Tax();		
			add_filter( 'woocommerce_package_rates', array($this,'hide_table_rate_shipping_when_prevented_by_admin') , 100, 2 );
			$this->rates_table     = $wpdb->prefix . 'woocommerce_shipping_table_rates';
                        $this->give_tax_to_vendor = get_wcmp_vendor_settings('give_tax', 'payment');
                        $this->give_shipping_to_vendor = get_wcmp_vendor_settings('give_shipping', 'payment');			
                        add_action('woocommerce_checkout_order_processed', array(&$this, 'wcmps_checkout_order_processed'), 50, 2);
                        add_filter('wcmp_vendors_shipping_amount', array($this, 'wcmp_vendors_shipping_amount_by_table_rate'), 30, 4);
		}		
	}
	
	/**
        * WCMp Calculate shipping for order
         *
         * @support table rate per item 
         * @param int $order_id
         * @param object $order_posted
         * @return void
         */
        public function wcmps_checkout_order_processed($order_id, $order_posted) {
            global $wpdb, $WCMp;
            //echo '<pre>';
            $order = new WC_Order($order_id);
            $shipping_method = $order->get_shipping_methods();
            foreach ($shipping_method as $key => $method) {
                $method_id = $method['method_id'];
                break;
            }
            $method_arr = explode(':', $method_id);
            
            $method_id = $method_arr[0];
            if($method_id != 'table_rate') return;
            $instance_id = $method_arr[1];
            if(isset($method_arr[2])) {
                $rate_id = $method_arr[2];
            } else {
                $rate_id = '';
            }
            
            $woocommerce_shipping_method_settings = get_option('woocommerce_' . $method_id . '_' . $instance_id . '_settings');
            
            $this->enabled              = 'yes';
            $this->title                = isset($woocommerce_shipping_method_settings['title']) ? $woocommerce_shipping_method_settings['title'] : '';
            $this->fee                  = isset($woocommerce_shipping_method_settings['handling_fee']) ? $woocommerce_shipping_method_settings['handling_fee'] : '';
            $this->order_handling_fee   = isset($woocommerce_shipping_method_settings['order_handling_fee']) ? $woocommerce_shipping_method_settings['order_handling_fee'] : '';
            $this->tax_status           = isset($woocommerce_shipping_method_settings['tax_status']) ? $woocommerce_shipping_method_settings['tax_status'] : '';
            $this->calculation_type     = isset($woocommerce_shipping_method_settings['calculation_type']) ? $woocommerce_shipping_method_settings['calculation_type'] : '';
            $this->min_cost             = isset($woocommerce_shipping_method_settings['min_cost']) ? $woocommerce_shipping_method_settings['min_cost'] : '';
            $this->max_cost             = isset($woocommerce_shipping_method_settings['max_cost']) ? $woocommerce_shipping_method_settings['max_cost'] : '';
            $this->max_shipping_cost    = isset($woocommerce_shipping_method_settings['max_shipping_cost']) ? $woocommerce_shipping_method_settings['max_shipping_cost'] : '';

            // Table rate specific variables
            $this->rates_table     = $wpdb->prefix . 'woocommerce_shipping_table_rates';
            $this->available_rates = array();
                        
            $line_items = $order->get_items('line_item');
            require_once ABSPATH.'wp-content/plugins/woocommerce-table-rate-shipping/includes/class-wc-shipping-table-rate.php';
            $table_rate = new WC_Shipping_Table_Rate($instance_id);
            
            // Get rates, depending on type
            if ( $this->calculation_type == 'item' ) {
                    // For each ITEM get matching rates
                    $costs = array();
                    $matched = false;
                    $vendor_costs = array();
                    
                    foreach($line_items as $key=>$line_item) {
                        $qty = isset($line_item['qty']) ? $line_item['qty'] : '';
                        $tax_class = isset($line_item['tax_class']) ? $line_item['tax_class'] : '';
                        $product_id = isset($line_item['product_id']) ? $line_item['product_id'] : '';
                        $variation_id = isset($line_item['variation_id']) ? $line_item['variation_id'] : '';
                        $line_subtotal = isset($line_item['line_subtotal']) ? $line_item['line_subtotal'] : '';
                        $line_total = isset($line_item['line_total']) ? $line_item['line_total'] : '';
                        $line_subtotal_tax = isset($line_item['line_subtotal_tax']) ? $line_item['line_subtotal_tax'] : '';
                        $line_tax = isset($line_item['line_tax']) ? $line_item['line_tax'] : '';
                        $line_tax_data = isset($line_item['line_tax_data']) ? $line_item['line_tax_data'] : '';
                        $sold_by = isset($line_item['Sold By']) ? $line_item['Sold By'] : '';
                        $vendor_id = isset($line_item['vendor_id']) ? $line_item['vendor_id'] : '';
                        // Ensure we don't add a variation to the cart directly by variation ID
                        if ( 'product_variation' == get_post_type( $product_id ) ) {
                                $variation_id = $product_id;
                                $product_id   = wp_get_post_parent_id( $variation_id );
                        }
                        // Get the product
                        $product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
                        // Check product is_purchasable
                        $product_data->is_purchasable();
                        // Stock check - only check if we're managing stock and backorders are not allowed
                        $product_data->is_in_stock();
                        $product_data->has_enough_stock( $qty );
                        //echo $product_data->needs_shipping();die;
                        $matching_rates = $table_rate->query_rates( array(
                                    'price'             => $product_data->price,
                                    'weight'            => $product_data->get_weight(),
                                    'count'             => 1,
                                    'count_in_class'    => $this->count_items_in_class( $line_items, $product_data->get_shipping_class_id() ),
                                    'shipping_class_id' => $product_data->get_shipping_class_id()
                            ) );
                        //print_r($matching_rates);
                        $item_weight = round( $product_data->get_weight(), 2 );
                        $item_fee    = $this->get_fee( $this->fee, $product_data->price );
                        $item_cost   = 0;

                        foreach ( $matching_rates as $rate ) {
                                $item_cost += $rate->rate_cost;
                                $item_cost += $rate->rate_cost_per_weight_unit * $item_weight;
                                $item_cost += ( $rate->rate_cost_percent / 100 ) * $product_data->price;
                                $matched = true;
                                if ( $rate->rate_abort ) {
                                    return;
                                }
                                if ( $rate->rate_priority ) {
                                    break;
                                }
                        }

                        $cost = ( $item_cost + $item_fee ) * $qty;

                        if ( $this->min_cost && $cost < $this->min_cost ) {
                                $cost = $this->min_cost;
                        }
                        if ( $this->max_cost && $cost > $this->max_cost ) {
                                $cost = $this->max_cost;
                        }
                        $costs[ $key ] = $cost;    
                        if(!empty($vendor_id)) {
                            $vendor_costs[$vendor_id] = isset($vendor_costs[$vendor_id]) ? ($vendor_costs[$vendor_id] + $cost) : $cost;
                        }
                    }
                    
                    if ( $matched ) {
                        if ( $this->order_handling_fee ) {
                                $costs['order'] = $this->order_handling_fee;
                        } else {
                                $costs['order'] = 0;
                        }
                        if ( $this->max_shipping_cost && ( $costs['order'] + array_sum( $costs ) ) > $this->max_shipping_cost ) {
                                $rates[] = array(
                                        'cost'  => $this->max_shipping_cost,
                                        'package'  => $line_items,
                                );
                        } else {
                                $rates[] = array(
                                        'cost'     => array_sum($costs),
                                        'calc_tax' => 'per_item',
                                        'package'  => $line_items,
                                );
                        }
                    }                    
            } elseif ( $this->calculation_type == 'line' ) {

                // For each LINE get matching rates
                $costs = array();
                $vendor_costs = array();
                $matched = false;

                foreach($line_items as $key=>$line_item) {
                    $qty = isset($line_item['qty']) ? $line_item['qty'] : '';
                    $tax_class = isset($line_item['tax_class']) ? $line_item['tax_class'] : '';
                    $product_id = isset($line_item['product_id']) ? $line_item['product_id'] : '';
                    $variation_id = isset($line_item['variation_id']) ? $line_item['variation_id'] : '';
                    $line_subtotal = isset($line_item['line_subtotal']) ? $line_item['line_subtotal'] : '';
                    $line_total = isset($line_item['line_total']) ? $line_item['line_total'] : '';
                    $line_subtotal_tax = isset($line_item['line_subtotal_tax']) ? $line_item['line_subtotal_tax'] : '';
                    $line_tax = isset($line_item['line_tax']) ? $line_item['line_tax'] : '';
                    $line_tax_data = isset($line_item['line_tax_data']) ? $line_item['line_tax_data'] : '';
                    $sold_by = isset($line_item['Sold By']) ? $line_item['Sold By'] : '';
                    $vendor_id = isset($line_item['vendor_id']) ? $line_item['vendor_id'] : '';

                    // Ensure we don't add a variation to the cart directly by variation ID
                    if ( 'product_variation' == get_post_type( $product_id ) ) {
                            $variation_id = $product_id;
                            $product_id   = wp_get_post_parent_id( $variation_id );
                    }
                    // Get the product
                    $product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
                    // Check product is_purchasable
                    $product_data->is_purchasable();
                    // Stock check - only check if we're managing stock and backorders are not allowed
                    $product_data->is_in_stock();
                    $product_data->has_enough_stock( $qty );

                    if ( $qty > 0 && $product_data->needs_shipping() ) {

                            $matching_rates = $table_rate->query_rates( array(
                                    'price'             => $this->wcmps_get_product_price( $product_data, $qty ),
                                    'weight'            => $product_data->get_weight() * $qty,
                                    'count'             => $qty,
                                    'count_in_class'    => $this->count_items_in_class( $line_items, $product_data->get_shipping_class_id() ),
                                    'shipping_class_id' => $product_data->get_shipping_class_id()
                            ) );

                            $item_weight = round( $product_data->get_weight() * $qty, 2 );
                            $item_fee    = $this->get_fee( $this->fee, $this->wcmps_get_product_price( $product_data, $qty ) );
                            $item_cost   = 0;

                            foreach ( $matching_rates as $rate ) {
                                    $item_cost += $rate->rate_cost;
                                    $item_cost += $rate->rate_cost_per_item * $qty;
                                    $item_cost += $rate->rate_cost_per_weight_unit * $item_weight;
                                    $item_cost += ( $rate->rate_cost_percent / 100 ) * ( $this->wcmps_get_product_price( $product_data, $qty ) );
                                    $matched = true;

                                    if ( $rate->rate_abort ) {
                                        return;
                                    }
                                    if ( $rate->rate_priority ) {
                                        break;
                                    }
                            }

                            $item_cost = $item_cost + $item_fee;

                            if ( $this->min_cost && $item_cost < $this->min_cost ) {
                                    $item_cost = $this->min_cost;
                            }
                            if ( $this->max_cost && $item_cost > $this->max_cost ) {
                                    $item_cost = $this->max_cost;
                            }
                            $costs[ $key ] = $item_cost;
                            if(!empty($vendor_id)) {
                                $vendor_costs[$vendor_id] = isset($vendor_costs[$vendor_id]) ? ($vendor_costs[$vendor_id] + $item_cost) : $item_cost;
                            }
                    }

                }

                if ( $matched ) {
                        if ( $this->order_handling_fee ) {
                                $costs['order'] = $this->order_handling_fee;
                        } else {
                                $costs['order'] = 0;
                        }

                        if ( $this->max_shipping_cost && ( $costs['order'] + array_sum( $costs ) ) > $this->max_shipping_cost ) {
                                $rates[] = array(
                                        'cost'    => $this->max_shipping_cost,
                                        'package' => $line_items,
                                );
                        } else {
                                $rates[] = array(
                                        'cost'     => array_sum($costs),
                                        'calc_tax' => 'per_item',
                                        'package'  => $line_items,
                                );
                        }
                }

        } elseif ( $this->calculation_type == 'class' ) {

                // For each CLASS get matching rates
                $total_cost	= 0;

                // First get all the rates in the table
                $all_rates = $table_rate->get_shipping_rates();

                // Now go through cart items and group items by class
                $classes 	= array();
                
                $costs = array();
                $vendor_costs = array();

                foreach($line_items as $key=>$line_item) {
                    $qty = isset($line_item['qty']) ? $line_item['qty'] : '';
                    $tax_class = isset($line_item['tax_class']) ? $line_item['tax_class'] : '';
                    $product_id = isset($line_item['product_id']) ? $line_item['product_id'] : '';
                    $variation_id = isset($line_item['variation_id']) ? $line_item['variation_id'] : '';
                    $line_subtotal = isset($line_item['line_subtotal']) ? $line_item['line_subtotal'] : '';
                    $line_total = isset($line_item['line_total']) ? $line_item['line_total'] : '';
                    $line_subtotal_tax = isset($line_item['line_subtotal_tax']) ? $line_item['line_subtotal_tax'] : '';
                    $line_tax = isset($line_item['line_tax']) ? $line_item['line_tax'] : '';
                    $line_tax_data = isset($line_item['line_tax_data']) ? $line_item['line_tax_data'] : '';
                    $sold_by = isset($line_item['Sold By']) ? $line_item['Sold By'] : '';
                    $vendor_id = isset($line_item['vendor_id']) ? $line_item['vendor_id'] : '';

                    // Ensure we don't add a variation to the cart directly by variation ID
                    if ( 'product_variation' == get_post_type( $product_id ) ) {
                            $variation_id = $product_id;
                            $product_id   = wp_get_post_parent_id( $variation_id );
                    }
                    // Get the product
                    $product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
                    // Check product is_purchasable
                    $product_data->is_purchasable();
                    // Stock check - only check if we're managing stock and backorders are not allowed
                    $product_data->is_in_stock();
                    $product_data->has_enough_stock( $qty );

                    if ( $qty > 0 && $product_data->needs_shipping() ) {

                                $shipping_class = $product_data->get_shipping_class_id();

                                if ( ! isset( $classes[ $shipping_class ] ) ) {
                                        $classes[ $shipping_class ] = new stdClass();
                                        $classes[ $shipping_class ]->price = 0;
                                        $classes[ $shipping_class ]->weight = 0;
                                        $classes[ $shipping_class ]->items = 0;
                                        $classes[ $shipping_class ]->items_in_class = 0;
                                }

                                $classes[ $shipping_class ]->price          += $this->wcmps_get_product_price( $product_data, $qty );
                                $classes[ $shipping_class ]->weight         += $product_data->get_weight() * $qty;
                                $classes[ $shipping_class ]->items          += $qty;
                                $classes[ $shipping_class ]->items_in_class += $qty;
                        }
                }

                $matched = false;
                $total_cost = 0;
                $stop = false;

                // Now we have groups, loop the rates and find matches in order
                foreach ( $all_rates as $rate ) {

                        foreach ( $classes as $class_id => $class ) {

                                if ( $class_id == "" ) {
                                        if ( $rate->rate_class != 0 && $rate->rate_class !== '' )
                                                continue;
                                } else {
                                        if ( $rate->rate_class != $class_id && $rate->rate_class !== '' )
                                                continue;
                                }

                                $rate_match = false;

                                switch ( $rate->rate_condition ) {
                                        case '' :
                                                $rate_match = true;
                                        break;
                                        case 'price' :
                                        case 'weight' :
                                        case 'items_in_class' :
                                        case 'items' :

                                                $condition = $rate->rate_condition;
                                                $value = $class->$condition;

                                                if ( $rate->rate_min === '' && $rate->rate_max === '' )
                                                        $rate_match = true;

                                                if ( $value >= $rate->rate_min && $value <= $rate->rate_max )
                                                        $rate_match = true;

                                                if ( $value >= $rate->rate_min && ! $rate->rate_max )
                                                        $rate_match = true;

                                                if ( $value <= $rate->rate_max && ! $rate->rate_min )
                                                        $rate_match = true;

                                        break;
                                }

                                // Rate matched class
                                if ( $rate_match ) {

                                        $class_cost = 0;
                                        $class_cost += $rate->rate_cost;
                                        $class_cost += $rate->rate_cost_per_item * $class->items_in_class;
                                        $class_cost += $rate->rate_cost_per_weight_unit * $class->weight;
                                        $class_cost += ( $rate->rate_cost_percent / 100 ) * $class->price;

                                        if ( $rate->rate_abort ) {
                                                return;
                                        }

                                        if ( $rate->rate_priority ) {
                                                $stop = true;
                                        }

                                        $matched = true;

                                        $class_fee	= $this->get_fee( $this->fee, $class->price );
                                        $class_cost += $class_fee;

                                        if ( $this->min_cost && $class_cost < $this->min_cost ) {
                                                $class_cost = $this->min_cost;
                                        }
                                        if ( $this->max_cost && $class_cost > $this->max_cost ) {
                                                $class_cost = $this->max_cost;
                                        }

                                        $total_cost += $class_cost;
                                        $vendor_id = get_woocommerce_term_meta($class_id, 'vendor_id', TRUE);
                                        if(!empty($vendor_id)) {
                                            if(isset($vendor_costs[$vendor_id])){
                                                $vendor_costs[$vendor_id] += $class_cost;
                                            }
                                            else {
                                                $vendor_costs[$vendor_id] = $class_cost;
                                            }
                                            if(isset($costs[$vendor_id])){
                                                $costs[$vendor_id] += $class_cost;
                                            }
                                            else {
                                                $costs[$vendor_id] = $class_cost;
                                            }                                            
                                        }
                                }
                        }

                        // Breakpoint
                        if ( $stop ) {
                                break;
                        }
                }

                if ( $this->order_handling_fee ) {
                        $total_cost += $this->order_handling_fee;
                        $costs['order'] = $this->order_handling_fee;
                }

                if ( $this->max_shipping_cost &&  $total_cost > $this->max_shipping_cost ) {
                        $total_cost = $this->max_shipping_cost;
                }

                if ( $matched ) {
                        $rates[] = array(
                                'cost'    => $total_cost,
                                'package' => $line_items,
                        );
                }

        }  else {
                    $vendor_id = '';
                    $costs = array();
                    $vendor_costs = array();
                    
                    // For the ORDER get matching rates
                    $shipping_class = $this->get_cart_shipping_class_id( $line_items );
                    $price          = 0;
                    $weight         = 0;
                    $count          = 0;
                    $count_in_class = 0;

                    foreach($line_items as $key=>$line_item) {
                        $qty = isset($line_item['qty']) ? $line_item['qty'] : '';
                        $tax_class = isset($line_item['tax_class']) ? $line_item['tax_class'] : '';
                        $product_id = isset($line_item['product_id']) ? $line_item['product_id'] : '';
                        $variation_id = isset($line_item['variation_id']) ? $line_item['variation_id'] : '';
                        $line_subtotal = isset($line_item['line_subtotal']) ? $line_item['line_subtotal'] : '';
                        $line_total = isset($line_item['line_total']) ? $line_item['line_total'] : '';
                        $line_subtotal_tax = isset($line_item['line_subtotal_tax']) ? $line_item['line_subtotal_tax'] : '';
                        $line_tax = isset($line_item['line_tax']) ? $line_item['line_tax'] : '';
                        $line_tax_data = isset($line_item['line_tax_data']) ? $line_item['line_tax_data'] : '';
                        $sold_by = isset($line_item['Sold By']) ? $line_item['Sold By'] : '';
                        $vendor_id = isset($line_item['vendor_id']) ? $line_item['vendor_id'] : '';
                        // Ensure we don't add a variation to the cart directly by variation ID
                        if ( 'product_variation' == get_post_type( $product_id ) ) {
                                $variation_id = $product_id;
                                $product_id   = wp_get_post_parent_id( $variation_id );
                        }
                        // Get the product
                        $product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
                        // Check product is_purchasable
                        $product_data->is_purchasable();
                        // Stock check - only check if we're managing stock and backorders are not allowed
                        $product_data->is_in_stock();
                        $product_data->has_enough_stock( $qty );

                            if ( $qty > 0 && $product_data->needs_shipping() ) {

                                    $price  += ! empty( $line_total ) ? $line_total : $this->wcmps_get_product_price( $product_data, $qty );
                                    $weight += ( $product_data->get_weight() * $qty );
                                    $count  += $qty;

                                    if ( $product_data->get_shipping_class_id() == $shipping_class )
                                            $count_in_class += $qty;

                            }
                    }

                    $matching_rates = $table_rate->query_rates( array(
                            'price'             => $price,
                            'weight'            => $weight,
                            'count'             => $count,
                            'count_in_class'    => $count_in_class,
                            'shipping_class_id' => $shipping_class
                    ) );

                    foreach ( $matching_rates as $rate ) {
                        if(!empty($rate_id) && $rate->rate_id != $rate_id) continue;
                            $label = $rate->rate_label;
                            if ( ! $label )
                                    $label = $this->title;

                            if ( $rate->rate_abort ) {                                    
                                    $rates = array(); // Clear rates
                                    break;
                            }

                            if ( $rate->rate_priority )
                                    $rates = array();

                            $cost = $rate->rate_cost;
                            $cost += $rate->rate_cost_per_item * $count;
                            $cost += $this->get_fee( $this->fee, $price );
                            $cost += $rate->rate_cost_per_weight_unit * $weight;
                            $cost += ( $rate->rate_cost_percent / 100 ) * $price;                            
                            
                            if(!empty($vendor_id)) {
                                $vendor_costs[$vendor_id] = isset($vendor_costs[$vendor_id]) ? ($vendor_costs[$vendor_id] + $cost) : $cost;
                            }
                            if ( $this->order_handling_fee ) {
                                    $cost += $this->order_handling_fee;
                            }

                            if ( $this->min_cost && $cost < $this->min_cost ) {
                                    $cost = $this->min_cost;
                            }

                            if ( $this->max_cost && $cost > $this->max_cost ) {
                                    $cost = $this->max_cost;
                            }

                            if ( $this->max_shipping_cost && $cost > $this->max_shipping_cost ) {
                                    $cost = $this->max_shipping_cost;
                            }
                            
                            $costs[] = $cost;
                            
                            $rates[] = array(
                                    'cost'    => $cost,
                                    'package' => $package,
                            );

                            if ( $rate->rate_priority ) {
                                    break;
                            }
                    }

            }
            $total_cost_before_condition = array_sum($costs);
            $total_cost_after_condition = $rates[0]['cost'];
            $condition_less = $total_cost_before_condition - $total_cost_after_condition;
            if($condition_less > 0) {
                $percentage_condition_less = ($condition_less/$total_cost_before_condition)*100;
                
                foreach($vendor_costs as $key=>$vendor_cost) {
                    $resulted_cost = round(($vendor_cost - ($percentage_condition_less * $vendor_cost)/100), 2);
                    $vendor_costs[$key] = $resulted_cost;
                }
            }
            $shipping = $order->get_items('shipping');
            if (!empty($shipping)) {
                foreach ($shipping as $shipping_id => $value) {
                    $shipping_total = $value['cost'];
                    $shipping_taxes = unserialize($value['taxes']);
                    $tax_rates = array();
                    foreach ($shipping_taxes as $tax_class_id => $tax_amount) {
                        $tax_rates[$tax_class_id] = ($tax_amount / $shipping_total) * 100;
                        $shipping_total = $shipping_total + $tax_amount;
                    }
                    foreach ($vendor_costs as $key => $vendor_cost) {
                        $cost_item_id = $vendor_cost;
                        $flat_shipping_per_vendor = wc_get_order_item_meta($shipping_id, 'vendor_cost_' . $key, true);
                        if (!$flat_shipping_per_vendor) {
                            wc_add_order_item_meta($shipping_id, 'vendor_cost_' . $key, round($cost_item_id, 2));
                            $flat_shipping_per_vendor = $cost_item_id;
                        }
                        $shipping_vendor_taxes = array();
                        foreach ($tax_rates as $tax_class_id => $tax_rate) {
                            $vendor_tax_amount = ($flat_shipping_per_vendor * $tax_rate) / 100;
                            $shipping_vendor_taxes[$tax_class_id] = round($vendor_tax_amount, 2);
                            $flat_shipping_per_vendor = $flat_shipping_per_vendor + $vendor_tax_amount;
                        }
                        $tax_shipping_per_vendor = wc_get_order_item_meta($shipping_id, 'vendor_tax_' . $key, true);
                        if (!$tax_shipping_per_vendor)
                            wc_add_order_item_meta($shipping_id, 'vendor_tax_' . $key, $shipping_vendor_taxes);
                    }
                    if ($this->give_shipping_to_vendor == 'Enable') {
                        wc_add_order_item_meta($shipping_id, '_give_shipping_to_vendor', 1);
                    } else {
                        wc_add_order_item_meta($shipping_id, '_give_shipping_to_vendor', 0);
                    }
                    if ($this->give_tax_to_vendor == 'Enable') {
                        wc_add_order_item_meta($shipping_id, '_give_tax_to_vendor', 1);
                    } else {
                        wc_add_order_item_meta($shipping_id, '_give_tax_to_vendor', 0);
                    }
                }
            }
        }
        
        /**
	 * count_items_in_class function.
	 * @return int
	 */
	public function count_items_in_class( $line_items, $class_id ) {
		$count = 0;

		// Find shipping classes for products in the package
		foreach ( $line_items as $key => $line_item ) {
                    $product_id = $line_item['product_id'];
                    $variation_id = $line_item['variation_id'];
                    if ( 'product_variation' == get_post_type( $product_id ) ) {
                        $variation_id = $product_id;
                        $product_id   = wp_get_post_parent_id( $variation_id );
                    }
                    // Get the product
                    $product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
                    if ( $product_data->needs_shipping() && $product_data->get_shipping_class_id() == $class_id ) {
                            $count += $line_item['qty'];
                    }
		}

		return $count;
	}
	
        /**
	 * get_cart_shipping_class_id function.
	 * @return int
	 */
	public function get_cart_shipping_class_id( $package ) {
		// Find shipping class for cart
		$found_shipping_classes = array();
		$shipping_class_id = 0;
		$shipping_class_slug = '';

		// Find shipping classes for products in the package
		if ( sizeof( $package ) > 0 ) {
			foreach ( $package as $item_id => $line_item ) {
                            $qty = isset($line_item['qty']) ? $line_item['qty'] : '';
                            $product_id = isset($line_item['product_id']) ? $line_item['product_id'] : '';
                            $variation_id = isset($line_item['variation_id']) ? $line_item['variation_id'] : '';
                            $vendor_id = isset($line_item['vendor_id']) ? $line_item['vendor_id'] : '';
                            // Ensure we don't add a variation to the cart directly by variation ID
                            if ( 'product_variation' == get_post_type( $product_id ) ) {
                                    $variation_id = $product_id;
                                    $product_id   = wp_get_post_parent_id( $variation_id );
                            }
                            // Get the product
                            $product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
                            // Check product is_purchasable
                            $product_data->is_purchasable();
                            // Stock check - only check if we're managing stock and backorders are not allowed
                            $product_data->is_in_stock();
                            $product_data->has_enough_stock( $qty );
                            if ( $product_data->needs_shipping() ) {
                                    $found_shipping_classes[ $product_data->get_shipping_class_id() ] = $product_data->get_shipping_class();
                            }
			}
		}

		$found_shipping_classes = array_unique( $found_shipping_classes );

		if ( sizeof( $found_shipping_classes ) == 1 ) {
			$shipping_class_slug = current( $found_shipping_classes );
		} elseif ( $found_shipping_classes > 1 ) {

			// Get class with highest priority
			$priority   = get_option( 'woocommerce_table_rate_default_priority_' . $this->instance_id );
			$priorities = get_option( 'woocommerce_table_rate_priorities_' . $this->instance_id );

			foreach ( $found_shipping_classes as $class ) {
				if ( isset( $priorities[ $class ] ) && $priorities[ $class ] < $priority ) {
					$priority = $priorities[ $class ];
					$shipping_class_slug = $class;
				}
			}
		}

		$found_shipping_classes = array_flip( $found_shipping_classes );

		if ( isset( $found_shipping_classes[ $shipping_class_slug ] ) )
			$shipping_class_id = $found_shipping_classes[ $shipping_class_slug ];

		return $shipping_class_id;
	}
        
	/**	 
	 * get table rate shipping settings for choosen methods
	 * @param  string
	 * @param  array
	 * @return string 
	 *  
	 */	
	public function get_settings_option($option_name, $settings) {
		$option_value = '';
		if(isset($settings) && isset($option_name) && is_array($settings)) {
			$option_value = $settings[$option_name] ? $settings[$option_name] : '';
		}
		return $option_value;
	}
		
	
	/**	 
	 * get setting name of table rate method.
	 * @param  int
	 * @return string 
	 *  
	 */
	public function get_method_settings_name($rate_class_number) {
		return $method_setting_name = "woocommerce_table_rate-{$rate_class_number}_settings";		
	}	
	
	
	/**
	 * Hide table rate shipping from shipping method list 
	 *
	 * @access public
	 * @param array $rates (default: array shipping rate required*)
	 * @param array $package (default: array() optional )	 * 
	 * @return array 
	 */	
	public function hide_table_rate_shipping_when_prevented_by_admin($rates, $package) {
		global $WCMPS_Advance_Shipping;
		$settings = get_option('wcmp_wcmps_advance_shipping_general_settings_name');		
		if(isset($settings['is_enable']) && isset($settings['disable_from_frontend_shipping'])) {
			$search = 'table_rate-*';
			$table_rate_list = array_key_exists_wildcard( $rates, $search );
			foreach ($table_rate_list as $table_rate) {
				unset($rates[$table_rate]);
			}			
		}
		return $rates;	
	}
	/**
	 * get_product_price function.
	 *
	 * @param object $_product
	 * @return array
	 */
	public function wcmps_get_product_price( $_product, $qty = 1 ) {
		$row_base_price = $_product->get_price() * $qty;
		$row_base_price = apply_filters( 'wcmps_woocommerce_table_rate_package_row_base_price', $row_base_price, $_product, $qty );

		if ( ! $_product->is_taxable() )
			return $row_base_price;

		if ( get_option('woocommerce_prices_include_tax') == 'yes' ) {

			$base_tax_rates = $this->tax->get_shop_base_rate( $_product->tax_class );
			$tax_rates      = $this->tax->get_rates( $_product->get_tax_class() );

			if ( $tax_rates !== $base_tax_rates ) {
				$base_taxes     = $this->tax->calc_tax( $row_base_price, $base_tax_rates, true, true );
				$modded_taxes   = $this->tax->calc_tax( $row_base_price - array_sum( $base_taxes ), $tax_rates, false );
				$row_base_price = ( $row_base_price - array_sum( $base_taxes ) ) + array_sum( $modded_taxes );
			}
		}

		return $row_base_price;
	}	
	
	/**
     * Get fee for the shipping method.
     *
     * @param mixed $fee
     * @param mixed $total
     * @return float
     */
    public function get_fee( $fee, $total ) {
        if ( strstr( $fee, '%' ) ) {
            $fee = ( $total / 100 ) * str_replace( '%', '', $fee );
        }
        if ( ! empty( $this->minimum_fee ) && $this->minimum_fee > $fee ) {
            $fee = $this->minimum_fee;
        }
        return $fee;
    }
    
    
    /**	 
    * calculate the shipping amount and shipping tax as per choosen table rate method called by wcmp_vendors_shipping_amount filter of WCMp.
    * @param  array
    * @param  int
    * @param  array 
    *  
    */
   public function wcmp_vendors_shipping_amount_by_table_rate($vendor_shipping_costs, $order_id, $product) {
        $method = '';
        $postobj = get_post($product[ 'product_id' ]);
        $author_id = $postobj->post_author;
        $vendor_obj = new WCMp_Vendor( $author_id );
        $product_id = $product['variation_id'] ? $product['variation_id'] : $product[ 'product_id' ];
        $_product     = get_product( $product_id );
        $order = wc_get_order( $order_id );
        $shipping_methods = $order->get_shipping_methods();

        foreach ($shipping_methods as $shipping_method) {
                $method = $shipping_method['method_id'];
                break;
        }
        if(strlen(strstr($method,'table_rate-')) > 0 ) {
                $number = $this->number = intval(str_replace('table_rate-','',$method));
                $method_settings_name = $this->get_method_settings_name($number);
                $method_settings = get_option($method_settings_name);			
                $this->fee                = $this->get_settings_option( 'handling_fee', $method_settings );
                $this->order_handling_fee = $this->get_settings_option( 'order_handling_fee', $method_settings );
                $this->tax_status         = $this->get_settings_option( 'tax_status', $method_settings );
                $this->calculation_type   = $this->get_settings_option( 'calculation_type', $method_settings );
                $this->min_cost           = $this->get_settings_option( 'min_cost', $method_settings );
                $this->max_cost           = $this->get_settings_option( 'max_cost', $method_settings );
                $this->max_shipping_cost  = $this->get_settings_option( 'max_shipping_cost', $method_settings );

                if ( $product[ 'qty' ] > 0 && $_product->needs_shipping() ) {
                        if ( $this->calculation_type == 'item' ) {
                                $matching_rates = $this->wcmps_query_rates( array(
                                        'price'             => $this->wcmps_get_product_price( $_product ),
                                        'weight'            => $_product->get_weight(),
                                        'count'             => 1,
                                        'count_in_class'    => 0,
                                        'shipping_class_id' => $_product->get_shipping_class_id()
                                ), $number );
                                $item_weight = round( $_product->get_weight(), 2 );
                                $item_fee    =  $this->get_fee( $this->fee, $this->wcmps_get_product_price( $_product ) );
                                $item_cost   = 0;
                                foreach ( $matching_rates as $rate ) {					
                                        $item_cost += $rate->rate_cost;
                                        $item_cost += $rate->rate_cost_per_weight_unit * $item_weight;
                                        $item_cost += ( $rate->rate_cost_percent / 100 ) * $this->wcmps_get_product_price( $_product );
                                        $matched = true;					
                                } 
                                $cost = ( $item_cost + $item_fee ) * $product['qty'];
                                if ( $this->min_cost && $cost < $this->min_cost ) {
                                        $cost = $this->min_cost;
                                }
                                if ( $this->max_cost && $cost > $this->max_cost ) {
                                        $cost = $this->max_cost;
                                }					
                                $shipping_tax = $vendor_obj->calculate_shipping_tax($cost, $order);	
                                $vendor_shipping_costs['shipping_amount'] = $cost;
                                $vendor_shipping_costs['shipping_tax'] = $shipping_tax;
                        } 
                        elseif( $this->calculation_type == 'line' ) {
                                $matching_rates = $this->wcmps_query_rates( array(
                                        'price'             => $this->wcmps_get_product_price( $_product ),
                                        'weight'            => $_product->get_weight(),
                                        'count'             => 1,
                                        'count_in_class'    => 0,
                                        'shipping_class_id' => $_product->get_shipping_class_id()
                                ), $number );
                                $item_weight = round( $_product->get_weight() * $product['qty'], 2 );
                                $item_fee    = $this->get_fee( $this->fee, $this->wcmps_get_product_price( $_product, $product['qty'] ) );
                                $item_cost   = 0;
                                foreach ( $matching_rates as $rate ) {					
                                        $item_cost += $rate->rate_cost;
                                        $item_cost += $rate->rate_cost_per_item * $product['qty'];
                                        $item_cost += $rate->rate_cost_per_weight_unit * $item_weight;
                                        $item_cost += ( $rate->rate_cost_percent / 100 ) * ( $this->wcmps_get_product_price( $_product, $product['qty'] ) );
                                        $matched = true;					
                                } 
                                $cost = ( $item_cost + $item_fee );
                                if ( $this->min_cost && $cost < $this->min_cost ) {
                                        $cost = $this->min_cost;
                                }
                                if ( $this->max_cost && $cost > $this->max_cost ) {
                                        $cost = $this->max_cost;
                                }
                                $shipping_tax = $vendor_obj->calculate_shipping_tax($cost, $order);	
                                $vendor_shipping_costs['shipping_amount'] = $cost;
                                $vendor_shipping_costs['shipping_tax'] = $shipping_tax;					
                        }				
                }
                return $vendor_shipping_costs;
        }
        else {
                return $vendor_shipping_costs;
        }

     }

     /**
      * wcmps_query_rates function get the matching rates as per defined.
      *
      * @param array $args
      * @param int $number
      * @return array
      */
     public function wcmps_query_rates( $args, $number ) {
         global $wpdb;

         $defaults = array(
                 'price'             => '',
                 'weight'            => '',
                 'count'             => 1,
                 'count_in_class'    => 1,
                 'shipping_class_id' => ''
         );

         $args = apply_filters( 'wcmps_woocommerce_table_rate_query_rates_args', wp_parse_args( $args, $defaults ) );

         extract( $args, EXTR_SKIP );

         if ( $shipping_class_id == "" ) {
                 $shipping_class_id_in = " AND rate_class IN ( '', '0' )";
         } else {
                 $shipping_class_id_in = " AND rate_class IN ( '', '" . absint( $shipping_class_id ) . "' )";
         }
         $rates = $wpdb->get_results(
                 $wpdb->prepare( "
                         SELECT rate_id, rate_cost, rate_cost_per_item, rate_cost_per_weight_unit, rate_cost_percent, rate_label, rate_priority, rate_abort, rate_abort_reason
                         FROM {$this->rates_table}
                         WHERE shipping_method_id IN ( %s )
                         {$shipping_class_id_in}
                         AND
                         (
                                 rate_condition = ''
                                 OR
                                 (
                                         rate_condition = 'price'
                                         AND
                                         (
                                                 ( ( rate_min + 0 ) = '' AND ( rate_max + 0 ) = '' )
                                                 OR
                                                 ( ( rate_min + 0 ) >= 0 AND ( rate_max + 0 ) >=0 AND '{$price}' >= ( rate_min + 0 ) AND '{$price}' <= ( rate_max + 0 ) )
                                                 OR
                                                 ( ( rate_min + 0 ) >= 0 AND ( rate_max + 0 ) = '' AND '{$price}' >= ( rate_min + 0 ) )
                                                 OR
                                                 ( ( rate_min + 0 ) = '' AND ( rate_max + 0 ) >= 0 AND '{$price}' <= ( rate_max + 0 ) )
                                         )
                                 )
                                 OR
                                 (
                                         rate_condition = 'weight'
                                         AND
                                         (
                                                 ( ( rate_min + 0 ) = '' AND ( rate_max + 0 ) = '' )
                                                 OR
                                                 ( ( rate_min + 0 ) >= 0 AND ( rate_max + 0 ) >=0 AND '{$weight}' >= ( rate_min + 0 ) AND '{$weight}' <= ( rate_max + 0 ) )
                                                 OR
                                                 ( ( rate_min + 0 ) >= 0 AND ( rate_max + 0 ) = '' AND '{$weight}' >= ( rate_min + 0 ) )
                                                 OR
                                                 ( ( rate_min + 0 ) = '' AND ( rate_max + 0 ) >= 0 AND '{$weight}' <= ( rate_max + 0 ) )
                                         )
                                 )
                                 OR
                                 (
                                         rate_condition = 'items'
                                         AND
                                         (
                                                 ( ( rate_min + 0 ) = '' AND ( rate_max + 0 ) = '' )
                                                 OR
                                                 ( ( rate_min + 0 ) >= 0 AND ( rate_max + 0 ) >=0 AND '{$count}' >= ( rate_min + 0 ) AND '{$count}' <= ( rate_max + 0 ) )
                                                 OR
                                                 ( ( rate_min + 0 ) >= 0 AND ( rate_max + 0 ) = '' AND '{$count}' >= ( rate_min + 0 ) )
                                                 OR
                                                 ( ( rate_min + 0 ) = '' AND ( rate_max + 0 ) >= 0 AND '{$count}' <= ( rate_max + 0 ) )
                                         )
                                 )
                                 OR
                                 (
                                         rate_condition = 'items_in_class'
                                         AND
                                         (
                                                 ( ( rate_min + 0 ) = '' AND ( rate_max + 0 ) = '' )
                                                 OR
                                                 ( ( rate_min + 0 ) >= 0 AND ( rate_max + 0 ) >= 0 AND '{$count_in_class}' >= ( rate_min + 0 ) AND '{$count_in_class}' <= ( rate_max + 0 ) )
                                                 OR
                                                 ( ( rate_min + 0 ) >= 0 AND ( rate_max + 0 ) = '' AND '{$count_in_class}' >= ( rate_min + 0 ) )
                                                 OR
                                                 ( ( rate_min + 0 ) = '' AND ( rate_max + 0 ) >= 0 AND '{$count_in_class}' <= ( rate_max + 0 ) )
                                         )
                                 )
                         )
                         ORDER BY rate_order ASC
                 ", $number )
         );
         update_option('test_option_update1',$rates);
         return apply_filters( 'wcmps_woocommerce_table_rate_query_rates', $rates );
     }

}
