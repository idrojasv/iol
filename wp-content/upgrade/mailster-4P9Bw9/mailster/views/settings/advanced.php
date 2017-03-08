
<p class="description"><?php esc_html_e( 'Some of these settings may affect your website. In normal circumstance it is not required to change anything on this page.', 'mailster' ) ?></p>

<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Disable Cache', 'mailster' ) ?></th>
		<td><label><input type="hidden" class="wasabi" name="mailster_options[disable_cache]" value=""><input type="checkbox" name="mailster_options[disable_cache]" value="1" <?php checked( mailster_option( 'disable_cache' ) );?>> <?php esc_html_e( 'Disable Object Cache for Mailster', 'mailster' ) ?> <p class="description"><?php echo __( 'If enabled Mailster doesn\'t use cache anymore. This causes an increase in page load time! This option is not recommended!', 'mailster' ); ?></p></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Remove Data', 'mailster' ) ?></th>
		<td><label><input type="hidden" class="wasabi" name="mailster_options[remove_data]" value=""><input type="checkbox" name="mailster_options[remove_data]" value="1" <?php checked( mailster_option( 'remove_data' ) );?>> <?php esc_html_e( 'Remove all data on plugin deletion', 'mailster' ) ?> <p class="description"><?php echo __( 'Mailster will remove all it\'s data if you delete the plugin via the plugin page.', 'mailster' ); ?></p></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'PHP Mailer', 'mailster' ) ?></th>
		<td>
		<?php $phpmailerversion = mailster_option( 'php_mailer' ); ?>
		<label><?php esc_html_e( 'Use version', 'mailster' ) ?>
		<select name="mailster_options[php_mailer]">
			<option value="0" <?php selected( ! $phpmailerversion ); ?>><?php esc_html_e( 'included in WordPress', 'mailster' ) ?></option>
			<option value="latest" <?php selected( 'latest', $phpmailerversion ); ?>><?php printf( __( 'latest (%s)', 'mailster' ), '5.2.22' ); ?></option>
		</select></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Send Test', 'mailster' ) ?></th>
		<td>
		<input type="text" value="<?php echo $current_user->user_email ?>" autocomplete="off" class="form-input-tip" id="mailster_testmail">
		<input type="button" value="<?php esc_html_e( 'Send Test', 'mailster' ) ?>" class="button mailster_sendtest" data-role="basic">
		<div class="loading test-ajax-loading"></div>
		</td>
	</tr>
</table>
