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



function wooc_extra_register_fields() {?>
       
       <p class="form-row form-row-wide">
       	<label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?><span class="required">*</span></label>
       	<input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php esc_attr_e( $_POST['billing_phone'] ); ?>" />
       </p>
       <div class="clear"></div>
       <?php
 }
 add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields' );

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

add_shortcode('new_then_sign_up','trymake_generate_signup_string');
function trymake_generate_signup_string(){
	if(!is_user_logged_in()){
		echo '<a href="http://www.kraker.ml/sign-up"><strong>New here?Sign-up</strong></a>';
	}
	else{
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
	<!-- HTML code Before header -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script>

		$(document).ready(function() {
			$('.col-full').show();
});
	</script>
    <?php
}
add_action( 'storefront_before_header', 'storefront_add_topbar' );

/* add searchbar */

add_action( 'storefront_header', 'SID_add_searchbar');
function SID_add_searchbar(){
	echo do_shortcode('[wcas-search-form]');
}

/* remove breadcrumbs */
add_filter( 'woocommerce_get_breadcrumb', '__return_false' );


