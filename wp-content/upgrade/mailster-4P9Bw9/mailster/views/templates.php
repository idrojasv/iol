<?php

$t = mailster( 'templates' );

$templates = $t->get_templates();
$mailster_templates = $t->get_mailster_templates();
$licensecodes = $t->get_license();

$notice = false;
$default = mailster_option( 'default_template', 'mymail' );
if ( ! isset( $templates[ $default ] ) ) {
	$default = 'mymail';
	mailster_update_option( 'default_template', 'mymail' );
	$notice[] = sprintf( __( 'Template %s is missing or broken. Reset to default', 'mailster' ), '"' . $default . '"' );

	// mymail template is missing => redownload it.
	if ( ! isset( $templates[ $default ] ) ) {
		$result = $t->renew_default_template();
		if ( is_wp_error( $result ) ) {
			echo '<div class="error"><h3>' . esc_html__( 'There was a problem loading the templates', 'mailster' ) . '</h3><p>' . $result->get_error_message() . '</p></div>';
			return;
		}
		$templates = $t->get_templates();
	}
}
if ( $updates = $t->get_updates() ) : ?>
<div class="update-nag below-h2">
	<?php printf( _n( '%d Update available', '%d Updates available', $updates, 'mailster' ), $updates ) ?>
</div>
<?php endif; ?>
<div class="wrap">
<div id="mailster_templates">
<?php
$template = $templates[ $default ];

if ( ! isset( $_GET['more'] ) ) :
?>

<ul>
<li id="templateeditor">
	<h3></h3>
	<input type="hidden" id="slug">
	<input type="hidden" id="file">

		<div class="inner">
			<div class="template-file-selector">
				<label><?php esc_html_e( 'Select template file', 'mailster' ) ?>:</label> <span></span>
			</div>
			<div class="edit-buttons">
				<span class="spinner template-ajax-loading"></span>
				<span class="message"></span>
				<button class="button-primary save"><?php esc_html_e( 'Save', 'mailster' ) ?></button>
				<button class="button saveas"><?php esc_html_e( 'Save as', 'mailster' ) ?>&hellip;</button> <?php esc_html_e( 'or', 'mailster' ) ?>
				<a class="cancel" href="#"><?php esc_html_e( 'Cancel', 'mailster' ) ?></a>
			</div>
				<textarea class="editor"></textarea>
			<div class="edit-buttons">
				<span class="message"></span>
				<span class="spinner template-ajax-loading"></span>
				<button class="button-primary save"><?php esc_html_e( 'Save', 'mailster' ) ?></button>
				<button class="button saveas"><?php esc_html_e( 'Save as', 'mailster' ) ?>&hellip;</button> <?php esc_html_e( 'or', 'mailster' ) ?>
				<a class="cancel" href="#"><?php esc_html_e( 'Cancel', 'mailster' ) ?></a>
			</div>
		</div>
	<br class="clear">
</li>
</ul>
<h1><?php esc_html_e( 'Templates', 'mailster' ) ?> <a href="edit.php?post_type=newsletter&page=mailster_templates&more" class="add-new-h2"> <?php esc_html_e( 'More Templates', 'mailster' );?> </a></h1>
<?php
wp_nonce_field( 'mailster_nonce' );
if ( $notice ) {
	foreach ( $notice as $note ) {?>
<div class="updated below-h2"><p><?php echo $note ?></p></div>
<?php }
}?>
<ul id="installed-templates">
<?php
$i = 0;
unset( $templates[ $default ] );

$new = isset( $_GET['new'] ) && isset( $templates[ $_GET['new'] ] ) ? esc_attr( $_GET['new'] ) : null;

if ( $new ) {
	$new_template = $templates[ $new ];
	unset( $templates[ $new ] );
	$templates = array( $new => $new_template ) + $templates;
}
$templates = array( $default => $template ) + $templates;


foreach ( $templates as $slug => $data ) {

	include MAILSTER_DIR . 'views/templates/installed-template.php';

}

if ( current_user_can( 'mailster_upload_templates' ) ) : ?>
	<li class="upload-field"><?php $t->media_upload_form();?></li>
<?php endif; ?>

</ul>

<?php else : ?>

<h1><?php esc_html_e( 'More Templates', 'mailster' ) ?> <a href="edit.php?post_type=newsletter&page=mailster_templates" class="add-new-h2"> <?php esc_html_e( 'Back to Overview', 'mailster' );?> </a></h1>

<ul id="available-templates">
<?php

if ( empty( $mailster_templates ) ) :

	echo '<div class="error below-h2"><p>' . __( 'Looks like there was a problem getting the list of templates', 'mailster' ) . '</p></div>';

else :

	$existing = @array_intersect_assoc( $mailster_templates, $templates );
	$others = @array_diff_assoc( $mailster_templates, $existing );

	$mailster_templates = $existing + $others;

	foreach ( $mailster_templates as $slug => $data ) {

		include MAILSTER_DIR . 'views/templates/available-template.php';

	}

endif;

?>
</ul>
<?php endif; ?>
<div id="thickboxbox">
	<ul class="thickbox-filelist"></ul>
	<iframe class="thickbox-iframe" src=""></iframe>
</div>
<div id="ajax-response"></div>
<br class="clear">
</div>
