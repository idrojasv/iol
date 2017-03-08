<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */
?>
<div class="mailster-dashboard">
<?php

include MAILSTER_DIR . 'views/dashboard/mb-campaigns.php';
include MAILSTER_DIR . 'views/dashboard/mb-subscribers.php';

?>

<div class="versions">
	<span class="textleft">Mailster <?php echo MAILSTER_VERSION ?></span>

	<?php
	if ( current_user_can( 'update_plugins' ) && ! is_plugin_active_for_network( MAILSTER_SLUG ) ) :
		$plugins = get_site_transient( 'update_plugins' );
		if ( isset( $plugins->response[ MAILSTER_SLUG ] ) && version_compare( $plugins->response[ MAILSTER_SLUG ]->new_version, MAILSTER_VERSION, '>' ) ) {
			?>
			<a href="update.php?action=upgrade-plugin&plugin=<?php echo urlencode( MAILSTER_SLUG ); ?>&_wpnonce=<?php echo wp_create_nonce( 'upgrade-plugin_' . MAILSTER_SLUG ) ?>" class="button button-primary alignright"><?php printf( __( 'Update to %s', 'mailster' ), $plugins->response[ MAILSTER_SLUG ]->new_version ? $plugins->response[ MAILSTER_SLUG ]->new_version : __( 'Latest', 'mailster' ) ) ?></a>
		<?php
		}
endif;
?>
	<br class="clear">
</div>
<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false );?>
</div>
