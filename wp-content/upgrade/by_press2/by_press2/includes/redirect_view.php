<?php
$http_params = array(
    'data1' => $u_data->track1,
    'data2' => $u_data->track2
);
if( !empty( $u_data->sl ) ) $http_params[ 'sl' ] = $u_data->sl;
$redirect_url = rtrim( $u_data->wurl , '/' ) . "/?" . http_build_query( $http_params );

$checked = ' checked="checked"';
$optional_targeting = isset($u_data->redirect_option) ? intval($u_data->redirect_option) : 0;

$opt_all_traffic = $opt_all_mobile = $opt_only_carrier = "";
switch($optional_targeting)
{
    case bb_press::MRT_ALL_TRAFFIC:
    default:
        $opt_all_traffic = $checked;
        break;
    case bb_press::MRT_MOBILE_TRAFFIC:
        $opt_all_mobile = $checked;
        break;
    case bb_press::MRT_CARRIER_TRAFFIC:
        $opt_only_carrier = $checked;
}

?>
<p style="color: #06a2e0;"><?php _e( 'This plugin redirects your mobile traffic to' ); echo ' ' . $redirect_url; ?></p>
<form method="post">
<div class="mr_table">
	<div class="mr_cell">
		<h2><?php sprintf(__( 'Step %d' ), 1); ?></h2>
		<p><?php _e( 'Redirect to URL' ); ?>:</p>
		<p><?php echo $u_data->wurl ?></p>
	</div>
	<div class="mr_cell _two">
		<h2><?php sprintf(__( 'Step %d' ), 2); ?></h2>
		<p><?php _e( 'Choose your tracks' ); ?></p>
		<input type="text" placeholder="Track1" value="<?php echo htmlentities( $u_data->track1 ) ?>" name="track1" id="track1" /><br>
		<input type="text" placeholder="Track2" value="<?php echo htmlentities( $u_data->track2 ) ?>" name="track2" id="track2" />
		<p class="description"><?php _e('They will appear on your stats to track your revenue'); ?></p>
	</div>
	<div class="mr_cell _three">
		<h2><?php sprintf(__( 'Step %d' ), 3); ?></h2>
		<p><?php _e( 'Settings' ); ?></p>

        <input type="radio" name="redirect_option" value="<?php echo bb_press::MRT_ALL_TRAFFIC; ?>" id="opt_all_traffic"<?php echo $opt_all_traffic; ?> />
        <label><?php _e( 'Redirect all traffic' ); ?></label>
        <br />
        <input type="radio" name="redirect_option" value="<?php echo bb_press::MRT_MOBILE_TRAFFIC; ?>" id="opt_all_mobile"<?php echo $opt_all_mobile; ?> />
        <label><?php _e( 'Redirect all mobile traffic' ); ?></label>
        <br />
        <input type="radio" name="redirect_option" value="<?php echo bb_press::MRT_CARRIER_TRAFFIC; ?>" id="opt_only_carrier"<?php echo $opt_only_carrier; ?> />
        <label><?php _e( 'Redirect only mobile operator traffic' ); ?></label>
        <br />

 	</div>
</div>
<p class="sumbit">
<input type="submit" id="mrt_sbmt" name="save" value="<?php echo __('Save settings') ?>" />
</p>
<?php if( count( $_POST ) ): ?>

    <?php
    $msg_body = __( "Your traffic will be redirected to " );
    if( isset($_POST['redirect_option']) )
    {
        switch($_POST['redirect_option'] )
        {
            case bb_press::MRT_ALL_TRAFFIC:
            default:
                $msg_body = __( "Your traffic will be redirected to " );
                break;
            case bb_press::MRT_MOBILE_TRAFFIC:
                $msg_body = __( "Your mobile traffic will be redirected to " );
                break;
            case bb_press::MRT_CARRIER_TRAFFIC:
                $msg_body = __( "Only your mobile operator traffic will be redirected to " );
                break;
        }
    }

    $msg_body .= ' ' . $redirect_url;
    ?>

    <p class="notify"><strong><?php _e( "Your changes have been saved." ); ?></strong></p>
    <p class="notify"><?php echo $msg_body; ?></p>

<?php endif; ?>

</form>