<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version' => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);


require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce = require 'inc/woocommerce/class-storefront-woocommerce.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */

/*=================================================================
 *    Email verification
 *=================================================================*/


/*
* ========================================================
*			Custom Shortcodes
*=========================================================
*/

/* get current user */
add_shortcode( 'current-username' , 'ss_get_current_username' );
function ss_get_current_username(){
    $user = wp_get_current_user();
    return $user->display_name;
}

/* show sign up for logged out visitor */
add_shortcode('new_then_sign_up','trymake_generate_signup_string');
function trymake_generate_signup_string(){
	if(!is_user_logged_in()){
		echo '<a href="http://www.kraker.ml/sign-up"><strong>New here?Sign-up</strong></a>';
	}

}


/*
*==================================================================
*/

/**
 * Adds a top bar to Storefront, before the header.
 */
function storefront_add_topbar() {
    ?>
	<!-- HTML Content to add before body -->
    <?php
	echo '<div class="tm-sticky-search-bar" style="display: none">';
	echo do_shortcode('[wcas-search-form]');
	echo '</div>';
}
add_action( 'storefront_before_header', 'storefront_add_topbar' ,20);

add_action( 'tm_inside_head', 'add_to_head' );
function add_to_head(){
	echo '<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>';
	?> 
		<meta name="theme-color" content="#0a6682"/>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
		<script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.js"></script>
		<script>
		// Wait for window load
		$(window).load(function() {
			// Animate loader off screen
			$(".se-pre-con").fadeOut("slow");;
		});
		</script> 
		<div class="se-pre-con"></div>
	<?php

}

/* add searchbar */
add_action( 'storefront_header', 'SID_add_searchbar');
function SID_add_searchbar(){
	echo '<div class="tm-mainsearch-bar">';
	echo do_shortcode('[wcas-search-form]');
	echo '</div>';
}


add_action( 'wp_footer', 'cart_update_qty_script' );
function cart_update_qty_script() {
  if (is_cart()) :
   	?>
    		<script>
        		jQuery('div.woocommerce').on('change', '.qty', function(){
           			jQuery("[name='update_cart']").trigger("click"); 
        		});
   		</script>
	<?php
	endif;
}

/* remove breadcrumbs */
add_filter( 'woocommerce_get_breadcrumb', '__return_false' );

/*----------------------------------------------
   REdirect checkout loged out users
----------------------------------------------*/
add_action( 'template_redirect', 'checkout_redirect_non_logged_to_login_access');
function checkout_redirect_non_logged_to_login_access() {

    // Here the conditions (woocommerce checkout page and unlogged user)
    if( is_checkout() && !is_user_logged_in()){

        // Redirecting to your custom login area
        wp_redirect( get_permalink( get_option('woocommerce_myaccount_page_id') ) );

        // always use exit after wp_redirect() function.
        exit;
    }
}
// Displaying a message on cart page for non logged users (Optional)
add_action( 'woocommerce_before_cart', 'customer_redirected_displaying_message');
function customer_redirected_displaying_message() {
    if( !is_user_logged_in() ){
        // HERE Type your displayed message and text button
        $message = __('To access checkout, you need first to be logged in', 'woocommerce');
        $button_text = __('Login area', 'woocommerce');

        $cart_link = get_permalink( get_option('woocommerce_myaccount_page_id') );

        wc_add_notice(  $message . '<a href="' . $cart_link . '" class="button wc-forward">' . $button_text . '</a>', 'notice' );
    }
}


