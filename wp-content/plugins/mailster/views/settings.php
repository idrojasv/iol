<?php

global $wpdb, $current_user, $wp_post_statuses, $wp_roles, $locale;

$customfields = mailster()->get_custom_fields();
$roles = $wp_roles->get_names();
$translations = get_transient( '_mailster_translation' );

?>
<form id="mailster-settings-form" method="post" action="options.php" autocomplete="off" enctype="multipart/form-data">
<input style="display:none"><input type="password" style="display:none">
<div class="wrap">
	<p class="alignright">
		<input type="submit" class="submit-form button-primary" value="<?php esc_html_e( 'Save Changes', 'mailster' ) ?>" disabled />
	</p>
<h1><?php esc_html_e( 'Newsletter Settings', 'mailster' ) ?></h1>
<?php

$active = count( mailster_get_active_campaigns() );

$templatefiles = mailster( 'templates' )->get_files( mailster_option( 'default_template' ) );
$timeformat = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
$timeoffset = mailster( 'helper' )->gmt_offset( true );

if ( $active ) {
	echo '<div class="error inline"><p>' . sprintf( _n( '%d campaign is active. You should pause it before you change the settings!', '%d campaigns are active. You should pause them before you change the settings!', $active, 'mailster' ), $active ) . '</p></div>';
}

?>
<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false );?>
<?php settings_fields( 'mailster_settings' );?>
<?php do_settings_sections( 'mailster_settings' );?>

<?php
$sections = array(
	'general' => __( 'General', 'mailster' ),
	'template' => __( 'Template', 'mailster' ),
	'frontend' => __( 'Front End', 'mailster' ),
	'subscribers' => __( 'Subscribers', 'mailster' ),
	'wordpress-users' => __( 'WordPress Users', 'mailster' ),
	'texts' => __( 'Texts', 'mailster' ) . ( $translations ? ' <span class="update-translation-available wp-ui-highlight" title="' . __( 'update available', 'mailster' ) . '"><span>!</span></span>' : '' ),
	'tags' => __( 'Tags', 'mailster' ),
	'delivery' => __( 'Delivery', 'mailster' ),
	'cron' => __( 'Cron', 'mailster' ),
	'capabilities' => __( 'Capabilities', 'mailster' ),
	'bounce' => __( 'Bouncing', 'mailster' ),
	'authentication' => __( 'Authentication', 'mailster' ),
	'advanced' => __( 'Advanced', 'mailster' ),
	'system_info' => __( 'System Info', 'mailster' ),
	'manage-settings' => __( 'Manage Settings', 'mailster' ),
);
$sections = apply_filters( 'mymail_setting_sections', apply_filters( 'mailster_setting_sections', $sections ) );

if ( ! current_user_can( 'mailster_manage_capabilities' ) && ! current_user_can( 'manage_options' ) ) {
	unset( $sections['capabilities'] );
}

if ( ! current_user_can( 'manage_options' ) ) {
	unset( $sections['manage_settings'] );
}

?>

	<div class="settings-wrap">
	<div class="settings-nav">
		<div class="mainnav contextual-help-tabs hide-if-no-js">
		<ul>
		<?php foreach ( $sections as $id => $name ) {?>
			<li><a href="#<?php echo $id; ?>" class="nav-<?php echo $id; ?>"><?php echo $name; ?></a></li>
		<?php }?>
		<?php do_action( 'mailster_settings_tabs' ); ?>
		<?php do_action( 'mymail_settings_tabs' ); ?>
		</ul>
		</div>
	</div>

	<div class="settings-tabs"> <div class="tab"><h3>&nbsp;</h3></div>

	<?php foreach ( $sections as $id => $name ) {
?>
	<div id="tab-<?php echo esc_attr( $id ) ?>" class="tab">
		<h3><?php echo esc_html( strip_tags( $name ) ); ?></h3>
		<?php do_action( 'mailster_section_tab' ); ?>
		<?php do_action( 'mailster_section_tab_' . $id ); ?>
		<?php do_action( 'mymail_section_tab' ); ?>
		<?php do_action( 'mymail_section_tab_' . $id ); ?>

		<?php if ( file_exists( MAILSTER_DIR . 'views/settings/' . $id . '.php' ) ) {
			include MAILSTER_DIR . 'views/settings/' . $id . '.php';
}
?>

	</div>
	<?php }?>

<?php
$extra_sections = apply_filters( 'mymail_extra_setting_sections', apply_filters( 'mailster_extra_setting_sections', array() ) );

foreach ( $extra_sections as $id => $name ) {?>
	<div id="tab-<?php echo esc_attr( $id ) ?>" class="tab">
		<h3><?php echo esc_html( strip_tags( $name ) ); ?></h3>
		<?php do_action( 'mailster_section_tab' ); ?>
		<?php do_action( 'mailster_section_tab_' . $id ); ?>
		<?php do_action( 'mymail_section_tab' ); ?>
		<?php do_action( 'mymail_section_tab_' . $id ); ?>
	</div>
	<?php }?>
		<p class="submitbutton">
			<input type="submit" class="submit-form button-primary" value="<?php esc_html_e( 'Save Changes', 'mailster' ) ?>" disabled />
		</p>
	</div>

	</div>

	<?php do_action( 'mailster_settings' ); ?>
	<?php do_action( 'mymail_settings' ); ?>

	<input type="text" class="hidden" name="mailster_options[got_url_rewrite]" value="<?php echo esc_attr( mailster_option( 'got_url_rewrite' ) ); ?>">
	<input type="text" class="hidden" name="mailster_options[profile_form]" value="<?php echo esc_attr( mailster_option( 'profile_form', 0 ) ); ?>">
	<input type="text" class="hidden" name="mailster_options[ID]" value="<?php echo esc_attr( mailster_option( 'ID' ) ); ?>">

	<br class="clearfix">
<span id="settingsloaded"></span>
</div>
</form>
