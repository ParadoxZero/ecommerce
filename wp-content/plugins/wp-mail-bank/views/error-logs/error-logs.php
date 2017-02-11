<?php
/**
* This Template is used for sending feedback.
*
* @author  Tech Banker
* @package wp-mail-bank/views/error-logs
* @version 2.0.0
*/
if(!defined("ABSPATH")) exit; // Exit if accessed directly
if(!is_user_logged_in())
{
	return;
}
else
{
	$access_granted = false;
	foreach($user_role_permission as $permission)
	{
		if(current_user_can($permission))
		{
			$access_granted = true;
			break;
		}
	}
	if(!$access_granted)
	{
		return;
	}
	elseif(error_logs_mail_bank == "1")
	{
    $clear_error_logs_nonce = wp_create_nonce("clear_error_logs_nonce");
		?>
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<li>
					<i class="icon-custom-home"></i>
					<a href="admin.php?page=mb_email_configuration">
						<?php echo $wp_mail_bank; ?>
					</a>
					<span>></span>
				</li>
				<li>
					<span>
						<?php echo $mb_error_logs;?>
					</span>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="portlet box vivid-green">
					<div class="portlet-title">
						<div class="caption">
							<i class="icon-custom-shield"></i>
							<?php echo $mb_error_logs; ?>
						</div>
					</div>
					<div class="portlet-body form">
						<form id="ux_frm_error_logs">
							<div class="form-body">
								<form id="ux_frm_error_logs">
									<div class="form-body">
										<div class="form-actions">
											<div class="btn-set pull-right">
												<a type="button" href="<?php echo plugins_url("views/error-logs/error-logs.txt",dirname(dirname(__FILE__)));?>" download="error_logs.txt" class="btn vivid-green  system-report" name="ux_btn_download_error_logs" id="ux_btn_download_error_logs"><?php echo $mb_error_download;?></a>
												<button type="button" class="btn vivid-green btn_clear_log"  name="ux_btn_report_error_logs"  id="ux_btn_report_error_logs"><?php echo $mb_error_clear;?></button>
											</div>
										</div>
										<div class="line-separator"></div>
										<table class="table table-striped table-hover">
											<div class="form-group">
												<label class="control-label">
													<?php echo $mb_error_output; ?> :
													<i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_error_logs_tooltip; ?>" data-placement="right"></i>
												</label>
												<textarea rows="25" class="form-control" name="ux_txt_error_logs" id="ux_txt_error_logs" readonly="true"><?php echo dbHelper_mail_bank::file_reader(MAIL_BANK_LOGFILE_PATH);?></textarea>
											</div>
										</table>
										<div class="line-separator"></div>
										<div class="form-actions">
											<div class="btn-set pull-right">
												<a type="button" href="<?php echo plugins_url("views/error-logs/error-logs.txt",dirname(dirname(__FILE__)));?>" download="error_logs.txt" class="btn vivid-green  system-report" name="ux_btn_download_error_logs" id="ux_btn_download_error_logs"><?php echo $mb_error_download;?></a>
												<button type="button" class="btn vivid-green btn_clear_log"  name="ux_btn_report_error_logs"  id="ux_btn_report_error_logs"><?php echo $mb_error_clear;?></button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	else
	{
		?>
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<li>
					<i class="icon-custom-home"></i>
					<a href="admin.php?page=mb_email_configuration">
						<?php echo $wp_mail_bank; ?>
					</a>
					<span>></span>
				</li>
				<li>
					<span>
						<?php echo $mb_error_logs;?>
					</span>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="portlet box vivid-green">
					<div class="portlet-title">
						<div class="caption">
							<i class="icon-custom-shield"></i>
							<?php echo $mb_error_logs;?>
						</div>
					</div>
					<div class="portlet-body form">
						<div class="form-body">
						 <strong><?php echo $mb_user_access_message; ?></strong>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
?>
