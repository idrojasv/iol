<?php $plugin_info = mailster()->plugin_info(); ?>
<div class="locked">
	<h2><span class="not-valid"><?php esc_html_e( 'Please Validate', 'mailster' ); ?></span><span class="valid"><?php esc_html_e( 'Validated!', 'mailster' ); ?></span>
	</h2>
</div>
<dl class="mailster-icon mailster-icon-finished valid">
	<dt><?php esc_html_e( 'Verified', 'mailster' ); ?></dt>
	<dd><span class="version"><?php esc_html_e( 'Your license is verified', 'mailster' ); ?></span></dd>
	<dd>
		<?php if ( current_user_can( 'mailster_manage_licenses' ) ) : ?>
		<a href="https://mailster.co/manage-licenses/" class="external"><?php esc_html_e( 'Manage Licenses', 'mailster' ); ?></a> |
		<a href="<?php echo admin_url( 'admin.php?page=mailster_dashboard&reset_license=' . wp_create_nonce( 'mailster_reset_license' ) ) ?>" class="reset-license"><?php esc_html_e( 'Reset License', 'mailster' ); ?></a> |
		<?php endif; ?>
		<a href="https://mailster.co/go/buy/?utm_campaign=plugin&utm_medium=dashboard" class="external"><?php esc_html_e( 'Buy new License', 'mailster' ); ?></a>
	</dd>
</dl>
<dl class="mailster-icon mailster-icon-delete not-valid">
	<dt><?php esc_html_e( 'Not Verified', 'mailster' ); ?></dt>
	<dd><span class="version"><?php esc_html_e( 'Your license has not been verified', 'mailster' ); ?></span></dd>
	<dd>
		<?php if ( current_user_can( 'mailster_manage_licenses' ) ) : ?>
		<a href="https://mailster.co/manage-licenses/" class="external"><?php esc_html_e( 'Manage Licenses', 'mailster' ); ?></a> |
		<?php endif; ?>
		<a href="https://mailster.co/go/buy/?utm_campaign=plugin&utm_medium=dashboard" class="external"><?php esc_html_e( 'Buy new License', 'mailster' ); ?></a>
	</dd>
</dl>
<dl class="mailster-icon mailster-icon-reload">
	<dt><?php printf( esc_html__( 'Installed Version %s', 'mailster' ), MAILSTER_VERSION ) ?></dt>
	<?php if ( ! $this->update ) : ?>
		<dd><span class="version"><?php esc_html_e( 'You have the latest version', 'mailster' ); ?></span></dd>
		<dd><?php isset( $plugin_info->last_update ) ? printf( __( 'checked %s ago', 'mailster' ), human_time_diff( $plugin_info->last_update ) ) :null ?></dd>

	<?php else : ?>
		<dd><span class="version"><?php esc_html_e( 'A new Version is available', 'mailster' ); ?></span></dd>
		<dd><a class="thickbox" href="<?php echo network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=mailster&amp;section=changelog&amp;TB_iframe=true&amp;width=772&amp;height=745' ) ?>"><?php esc_html_e( 'view changelog', 'mailster' ) ?></a> <?php esc_html_e( 'or', 'mailster' ) ?> <a href="update.php?action=upgrade-plugin&plugin=<?php echo urlencode( MAILSTER_SLUG ); ?>&_wpnonce=<?php echo wp_create_nonce( 'upgrade-plugin_' . MAILSTER_SLUG ) ?>" class="update-button"><?php printf( __( 'update to %s now', 'mailster' ), $plugin_info->new_version ) ?></a></dd>
	<?php endif; ?>
</dl>
<dl class="mailster-icon mailster-icon-support">
	<dt><?php esc_html_e( 'Support', 'mailster' ); ?></dt>
	<dd>
		<a href="https://docs.revaxarts.com/mailster/" class="external"><?php esc_html_e( 'Documentation', 'mailster' ); ?></a> |
		<a href="https://kb.mailster.co/" class="external"><?php esc_html_e( 'Knowledge Base', 'mailster' ); ?></a> |
		<a href="https://mailster.co/login/" class="external"><?php esc_html_e( 'Support', 'mailster' ); ?></a>
	</dd>
</dl>
