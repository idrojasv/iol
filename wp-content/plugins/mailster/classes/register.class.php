<?php

class MailsterRegister {

	public function __construct() {

		add_action( 'admin_init', array( &$this, 'init' ) );
		add_action( 'mailster_register_mailster', array( &$this, 'on_register' ), 10, 3 );

	}


	public function init() {

	}


	/**
	 *
	 *
	 * @param unknown $slug     (optional)
	 * @param unknown $verified (optional)
	 * @param unknown $args     (optional)
	 */
	public function form( $slug = null, $verified = null, $args = array() ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-register-style', MAILSTER_URI . 'assets/css/register-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		wp_enqueue_script( 'mailster-register-script', MAILSTER_URI . 'assets/js/register-script' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION );

		wp_localize_script( 'mailster-register-script', 'mailsterregisterL10n', array(
				'wpnonce' => wp_create_nonce( 'mailster_register' ),
		) );

		if ( is_null( $slug ) ) {
			$slug = MAILSTER_SLUG;
		}
		$slug = strtolower( dirname( $slug ) );

		if ( is_null( $verified ) ) {
			$verified = mailster()->is_verified();
		}

		$page = isset( $_GET['page'] ) ? str_replace( 'mailster_', '', $_GET['page'] ) : 'dashboard';

		$args = wp_parse_args( $args, array(
				'pretext' => sprintf( esc_html__( 'Enter Your Purchase Code To Register (Don\'t have one for this site? %s)', 'mailster' ), '<a href="' . esc_url( 'https://mailster.co/go/buy/?utm_campaign=plugin&utm_medium=' . $page ) . '" class="external">' . esc_html__( 'Buy Now!', 'mailster' ) . '</a>' ),
				'purchasecode' => get_option( 'mailster_license' ),
		) );

		$user_id = get_current_user_id();
		$user = get_userdata( $user_id );

		$username = get_option( 'mailster_username', $user->data->user_login );
		$useremail = get_option( 'mailster_email', $user->data->user_email );

		wp_print_styles( 'mailster-register-style' );

?>

		<div class="register_form_wrap register_form_wrap-<?php echo sanitize_key( $slug ); ?> loading<?php echo $verified ? ' step-3': ' step-1' ?>">
			<input type="hidden" class="register-form-slug" name="slug" value="<?php echo esc_attr( $slug ) ?>">
			<div class="register-form-info">
				<span class="step-1"><?php esc_html_e( 'Verifying Purchase Code', 'mailster' ); ?>&hellip;</span>
				<span class="step-2"><?php esc_html_e( 'Finishing Registration', 'mailster' ); ?>&hellip;</span>
			</div>
			<form class="register_form" action="" method="POST">
				<div class="howto"><?php echo $args['pretext'] ?></div>
				<div class="error-msg">&nbsp;</div>
				<input type="text" class="widefat register-form-purchasecode" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" name="purchasecode" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" maxlength="36" value="<?php echo esc_attr( $args['purchasecode'] ) ?>">
				<input type="submit" class="button button-hero button-primary dashboard-register" value="<?php esc_attr_e( 'Register', 'mailster' ) ?>">
				<div class="howto">
					<a href="https://mailster.github.io/images/purchasecode.gif" class="howto-purchasecode"><?php esc_html_e( 'Where can I find my item purchase code?', 'mailster' ); ?></a>
					<!-- &ndash;
					<a href="<?php echo add_query_arg( array( 'action' => 'mailster_envato_verify', 'slug' => $slug, '_wpnonce' => wp_create_nonce( 'mailster_nonce' ) ), admin_url( 'admin-ajax.php' ) ); ?>" class="envato-signup"><?php esc_html_e( 'Register via Envato', 'mailster' ); ?></a>
					-->
				</div>
			</form>
			<form class="register_form_2" action="" method="POST">
				<div class="error-msg">&nbsp;</div>
				<input type="text" class="widefat username" placeholder="<?php _e( 'Username', 'mailster' ); ?>" name="username" value="<?php echo esc_attr( $username ) ?>">
				<input type="email" class="widefat email" placeholder="Email" name="email" value="<?php echo esc_attr( $useremail ) ?>">
				<input type="submit" class="button button-hero button-primary" value="<?php esc_attr_e( 'Complete Registration', 'mailster' ) ?>">
				<div class="howto"><label>* <?php esc_html_e( 'By completing this registration you\'ll receive an invitation to join our exclusive email list which provides you with updates related to Mailster.', 'mailster' ); ?></label></div>
			</form>
			<form class="registration_complete">
				<div class="registration_complete_check"></div>
				<div class="registration_complete_text"><?php esc_html_e( 'All Set!', 'mailster' ); ?></div>
			</form>
		</div>
	<?php

	}


	/**
	 *
	 *
	 * @param unknown $username
	 * @param unknown $email
	 * @param unknown $purchasecode
	 */
	public function on_register( $username, $email, $purchasecode ) {

		update_option( 'mailster_license', $purchasecode );
		delete_transient( 'mailster_verified' );
		mailster_remove_notice( 'verify' );

	}


}
