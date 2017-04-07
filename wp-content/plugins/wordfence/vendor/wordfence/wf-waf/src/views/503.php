<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
	<title>Your access to this site has been limited</title>
</head><body>
<h1>Your access to this site has been limited</h1>
<p>Your access to this service has been temporarily limited. Please try again in a few minutes. (HTTP response code 503)</p>
<p>Reason: <span style="color: #F00;"><?php echo $reason; ?></span></p>
<p style="width: 600px;"><b>Important note for site admins: </b>If you are the administrator of this website note that your access has been limited because you broke one of the Wordfence advanced blocking rules.
	The reason your access was limited is: <b>"<?php echo $reason; ?>"</b>.
	<br /><br />
	If this is a false positive, meaning that your access to your own site has been limited incorrectly, then you
	will need to regain access to your site, go to the Wordfence "options" page, go to the section for Rate Limiting Rules and disable the rule that caused you to be blocked. For example,
	if you were blocked because it was detected that you are a fake Google crawler, then disable the rule that blocks fake google crawlers. Or if you were blocked because you
	were accessing your site too quickly, then increase the number of accesses allowed per minute.
	<br /><br />
	If you're still having trouble, then simply disable the Wordfence advanced blocking and you will
	still benefit from the other security features that Wordfence provides.
</p>

<?php
$nonce = $waf->createNonce('wf-form');
if (!empty($homeURL) && !empty($nonce)) : ?>
<br /><br />

If you are a site administrator and have been accidentally locked out, please enter your email in the box below and click "Send". If the email address you enter belongs to a known site administrator or someone set to receive Wordfence alerts, we will send you an email to help you regain access. <a href="https://docs.wordfence.com/en/Help!_I_locked_myself_out_and_can't_get_back_in._What_can_I_do%3F" target="_blank">Please read this FAQ entry if this does not work.</a>
<br /><br />
<form method="POST" action="<?php echo $homeURL; ?>?_wfsf=unlockEmail">
	<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
	<input type="text" size="50" name="email" value="" maxlength="255" />&nbsp;<input type="submit" name="s" value="Send me an unlock email" />
</form>
<?php endif; ?>
<br /><br />

<p style="color: #999999;margin-top: 2rem;"><em>Generated by Wordfence at <?php echo gmdate('D, j M Y G:i:s T', wfWAFUtils::normalizedTime()); ?>.<br>Your computer's time: <script type="application/javascript">document.write(new Date().toUTCString());</script>.</em></p>
</body></html>
