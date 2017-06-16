<?php
/**
 * Registration form.
 *
 * @author 	Jeroen Sormani
 * @package 	WooCommerce-Simple-Registration
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;
wp_enqueue_script( 'wc-password-strength-meter' );

?><div class="registration-form woocommerce">
<script src="https://use.fontawesome.com/f774f0b233.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<?php wc_print_notices(); ?>
    <div class="container">
        <div id="regbox" style="margin-top:50px;" class="mainbox col-md-7 col-md-offset-4 col-sm-12  ">
            <div class="panel panel-info" >
                <div class="panel-heading">
                    <div class="panel-title"> <h2><?php _e( 'Register', 'woocommerce' ); ?></h2></div>
                </div>
                <div style="padding-top:30px" class="panel-body" >
	<form method="post" class="register, form-horizontal" role="form">

		<?php do_action( 'woocommerce_register_form_start' ); ?>
		<div style="margin-bottom: 25px" class="input-group">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
                      <input id="reg_billing_phone" type="text" class="form-control" name="billing_phone" value="<?php esc_attr_e( $_POST['billing_phone'] ); ?>" placeholder="Phone number" style="font-style: italic;">
                </div>


		<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

			<!--<p class="woocommerce-FormRow woocommerce-FormRow- -wide form-row form-row-wide">
				<label for="reg_username"><?php // _e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input- -text input-text" name="username" id="reg_username" placeholder="Username" value="<?php //if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
			</p>-->
                       <div style="margin-bottom: 25px ; display:none;" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input id="reg_username" type="text" class="form-control" name="username" value=""<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?> placeholder="username" style="font-style: italic;">
                        </div>


		<?php endif; ?>

		<!--<p class ="woocommerce-FormRow woocommerce-FormRow- -wide form-row form-row-wide">
			<label for="reg_email"><?php// _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
			<input type="email" class="woocommerce-Input woocommerce-Input- -text input-text" name="email" id="reg_email" placeholder="Username"	value="<?php //if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
		</p>-->
			 <div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i></span>
                            <input id="reg_email" type="email" class="form-control" name="email" value=""<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?> placeholder="email" style="font-style: italic;">
                        </div>



		<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

			<!--<p class="woocommerce-FormRow woocommerce-FormRow- -wide form-row form-row-wide">
				<label for="reg_password"><?php //_e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="password" class="woocommerce-Input woocommerce-Input- -text input-text" name="password" id="reg_password" placeholder="Password" />
			</p>-->
			<div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input id="login-password" type="password" class="form-control" name="password" placeholder="password" id="reg_password" style="font-style: italic;">
                        </div>
		<?php endif; ?>

		<!-- Spam Trap -->
		<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'woocommerce' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" autocomplete="off" style="display:None;"/></div>

		<?php do_action( 'woocommerce_register_form' ); ?>
		<?php do_action( 'register_form' ); ?>

		<p class="woocomerce-FormRow form-row " style="text-align:center">
			<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
			<input type="submit" class="woocommerce-Button button" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"style="width: 40%;border-radius: 5px;" />
		</p>

		<?php do_action( 'woocommerce_register_form_end' ); ?>

	</form>

</div>
	<script>
		$('#reg_email').change(function() {
		$('#reg_username').val($(this).val());
		});
	</script>
