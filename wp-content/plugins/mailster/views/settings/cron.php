
<table class="form-table">
	<tr valign="top" class="wp_cron">
		<th scope="row"><?php esc_html_e( 'Interval for sending emails', 'mailster' ) ?></th>
		<td><p><?php printf( __( 'Send emails at most every %1$s minutes', 'mailster' ), '<input type="text" name="mailster_options[interval]" value="' . mailster_option( 'interval' ) . '" class="small-text">' ) ?></p><p class="description"><?php esc_html_e( 'Optional if a real cron service is used', 'mailster' );?></p></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Cron Service', 'mailster' ) ?></th>
		<td>
			<?php $cron = mailster_option( 'cron_service' );?>
			<label><input type="radio" class="cron_radio" name="mailster_options[cron_service]" value="wp_cron" <?php checked( $cron == 'wp_cron' );?> > <?php esc_html_e( 'Use the wp_cron function to send newsletters', 'mailster' ) ?></label><br>
			<?php if ( ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) ) : ?>
			<div class="error inline"><p><strong><?php printf( __( 'WordPress cron is disabled! Uncomment the %s constant in the wp-config.php or use a real cron instead', 'mailster' ), '<code>DISABLE_WP_CRON</code>' ); ?></strong></p></div>
			<?php endif; ?>
			<label><input type="radio" class="cron_radio" name="mailster_options[cron_service]" value="cron" <?php checked( $cron == 'cron' ) ?> > <?php esc_html_e( 'Use a real cron to send newsletters', 'mailster' ) ?></label> <span class="description"><?php esc_html_e( 'reccomended for many subscribers', 'mailster' ) ?></span>
			<?php if ( file_exists( MAILSTER_UPLOAD_DIR . '/CRON_LOCK' ) && ( time() - filemtime( MAILSTER_UPLOAD_DIR . '/CRON_LOCK' ) ) < 10 ) : ?>
			<div class="error inline"><p><strong><?php esc_html_e( 'Cron is currently running!', 'mailster' );?></strong></p></div>
			<?php endif; ?>
		</td>
	</tr>
	<tr valign="top" class="cron_opts cron" <?php if ( $cron != 'cron' ) {
		echo ' style="display:none"';
}
?>>
		<th scope="row"><?php esc_html_e( 'Cron Settings', 'mailster' ) ?></th>
		<td>
			<p>
			<input type="text" name="mailster_options[cron_secret]" value="<?php echo esc_attr( mailster_option( 'cron_secret' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'a secret hash which is required to execute the cron', 'mailster' ) ?></span>
			</p>
			<?php $cron_url = mailster( 'cron' )->url();
?>
			<p><?php esc_html_e( 'You can keep a browser window open with following URL', 'mailster' ) ?><br>
			<a href="<?php echo $cron_url ?>" class="external"><code><?php echo $cron_url ?></code></a><br>
			<?php esc_html_e( 'call it directly', 'mailster' ) ?><br>
			<code>curl --silent <?php echo $cron_url ?></code><br>
			<?php esc_html_e( 'or set up a cron', 'mailster' ) ?><br>
			<code>*/<?php echo mailster_option( 'interval' ) ?> * * * * GET <?php echo $cron_url ?> > /dev/null</code></p>
			<p class="description"><?php esc_html_e( 'You can setup an interval as low as one minute, but should consider a reasonable value of 5-15 minutes as well.', 'mailster' );?></p>
			<p class="description"><?php esc_html_e( 'If you need help setting up a cron job please refer to the documentation that your provider offers.', 'mailster' );?></p>
			<p class="description"><?php printf( __( 'Anyway, chances are high that either %1$s, %2$s or %3$s  documentation will help you.', 'mailster' ), '<a href="https://docs.cpanel.net/display/ALD/Cron+Jobs" class="external">the CPanel</a>', '<a href="http://download1.parallels.com/Plesk/PP10/10.3.1/Doc/en-US/online/plesk-administrator-guide/plesk-control-panel-user-guide/index.htm?fileName=65208.htm" class="external">Plesk</a>', '<a href="http://www.thegeekstuff.com/2011/07/php-cron-job/" class="external">the crontab</a>' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Cron Lock', 'mailster' ) ?></th>
		<td>

			<?php $cron_lock = mailster_option( 'cron_lock' ); ?>
			<select name="mailster_options[cron_lock]">
				<option value="file" <?php selected( $cron_lock, 'file' ); ?>><?php _e( 'File based', 'mailster' ) ?></option>
				<option value="db" <?php selected( $cron_lock, 'db' ); ?>><?php _e( 'Database based', 'mailster' ) ?></option>
			</select>
			<p class="description"><?php esc_html_e( 'A Cron Lock ensures your cron is not overlapping and causing duplicate emails. Select which method you like to use.', 'mailster' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Last hit', 'mailster' ) ?></th>
		<td>
		<?php $last_hit = get_option( 'mailster_cron_lasthit' );
		if ( $last_hit && time() - $last_hit['timestamp'] > 720 && mailster( 'cron' )->is_locked() ) : ?>
			<div class="error inline">
			<p><?php printf( __( 'Looks like your Cron Lock is still in place after %1$s! Read more about why this can happen %2$s.', 'mailster' ), '<strong>' . human_time_diff( $last_hit['timestamp'] ) . '</strong>', '<a href="https://kb.mailster.co/what-is-a-cron-lock/" class="external">' . __( 'here', 'mailster' ) . '</a>' ); ?></p>
			</div>
			<?php endif; ?>

		<ul class="lasthit highlight">
		<?php if ( $last_hit ) :
				$interv = round( ( $last_hit['timestamp'] - $last_hit['oldtimestamp'] ) / 60 );
			?>
			<li>IP: <?php echo $last_hit['ip'];
			if ( $last_hit['ip'] == mailster_get_ip() ) { echo ' (' . __( 'probably you', 'mailster' ) . ')'; } ?></li>
			<li><?php echo $last_hit['user'] ?></li>
			<li><?php echo date( $timeformat, $last_hit['timestamp'] + $timeoffset ) . ', <strong>' . sprintf( __( '%s ago', 'mailster' ), human_time_diff( $last_hit['timestamp'] ) ) . '</strong>' ?></li>
			<?php if ( $interv ) : ?>
			<li><?php echo __( 'Interval', 'mailster' ) . ': <strong>' . $interv . ' ' . _x( 'min', 'short for minute', 'mailster' ) . '</strong>'; ?></li>
			<?php endif; ?>
			<?php if ( $last_hit['timemax'] ) : ?>
			<li><?php echo __( 'Max Execution Time', 'mailster' ) . ': ' . round( $last_hit['timemax'], 3 ) . ' ' . _x( 'sec', 'short for second', 'mailster' ); ?></li>
			<?php endif; ?>
		<?php else : ?>
			<li><strong><?php esc_html_e( 'never', 'mailster' ) ?></strong>
			(<a href="https://kb.mailster.co/how-do-i-know-if-my-cron-is-working-correctly/" class="external"><?php esc_html_e( 'why?', 'mailster' ) ?></a>)</li>
		<?php endif; ?>

		</ul>

		</td>
	</tr>
</table>
