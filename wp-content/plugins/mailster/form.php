<?php

$time_start = microtime( true );
if ( ! defined( 'ABSPATH' ) ) {

	require_once '../../../wp-load.php';

}

do_action( 'mailster_form_header' );
do_action( 'mymail_form_header' );

?><!DOCTYPE html>
<!--[if IE 8]><html class="lt-ie10 ie8" <?php language_attributes();?>><![endif]-->
<!--[if IE 9]><html class="lt-ie10 ie9" <?php language_attributes();?>><![endif]-->
<!--[if gt IE 9]><!--><html <?php language_attributes();?>><!--<![endif]-->
<html <?php language_attributes();?> class="mailster-emebed-form">
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' );?>; charset=<?php echo get_option( 'blog_charset' ); ?>" />
	<meta name='robots' content='noindex,nofollow'>
	<?php do_action( 'mailster_form_head' ); ?>
	<?php do_action( 'mymail_form_head' ); ?>

</head>
<body>
	<div class="mailster-form-body">
		<div class="mailster-form-wrap">
			<div class="mailster-form-inner">
			<?php do_action( 'mailster_form_body' ); ?>
			<?php do_action( 'mymail_form_body' ); ?>
			</div>
		</div>
	</div>
<?php do_action( 'mailster_form_footer' ); ?>
<?php do_action( 'mymail_form_footer' ); ?>
</body>
</html>
