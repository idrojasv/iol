<?php
/**
 * Template for displaying the form let user fill out their information to become a teacher
 */

$method       = $atts['method'];
$request      = $method == 'post' ? $_POST : $_REQUEST;
$is_logged_in = is_user_logged_in();
$disabled     = !$is_logged_in ? 'disabled="disabled"' : '';
?>
<div class="become-teacher-form <?php echo $is_logged_in ? 'allow' : ''; ?>">
	<h4 class="teacher-title"><?php esc_html_e( 'Register to become a teacher', 'eduma' ); ?></h4>
	<?php if ( !$is_logged_in ) { ?>
		<p class="message message-info"><?php printf( __( 'You have to <a href="%s">login</a> to fill out this form', 'eduma' ), add_query_arg( 'redirect_to', get_permalink(), thim_get_login_page_url() ) ); ?></p>
	<?php } ?>
	<form name="become_teacher_form" method="<?php echo esc_attr( $method ); ?>" enctype="multipart/form-data" action="<?php echo esc_attr( $atts['action'] ); ?>">
		<?php if ( $fields ): ?>
			<ul>
				<?php foreach ( $fields as $name => $option ): ?>
					<?php
					$option        = wp_parse_args(
						$option,
						array(
							'title'       => '',
							'type'        => '',
							'def'         => '',
							'placeholder' => ''
						)
					);
					$value         = !empty( $request[$name] ) ? $request[$name] : ( !empty( $option['def'] ) ? $option['def'] : '' );
					$requested     = strtolower( $_SERVER['REQUEST_METHOD'] ) == $method;
					$error_message = null;
					if ( $requested ) {
						$error_message = apply_filters( 'learn_press_become_teacher_form_validate_' . $name, $value );
					}

					?>
					<li>
						<?php
						switch ( $option['type'] ) {
							case 'text':
							case 'email':
								printf( '<input type="%s" name="%s" placeholder="%s" value="%s" %s />', $option['type'], $name, $option['placeholder'], esc_attr( $value ), $disabled );
								break;
						}
						if ( $error_message ) {
							printf( '<p class="error">%s</p>', $error );
						}
						?>
					</li>
				<?php endforeach; ?>
			</ul>
			<input type="hidden" name="action" value="become_a_teacher" />
			<button type="submit" <?php echo $disabled; ?>><?php echo esc_html( $atts['submit_button_text'] ); ?></button>
		<?php endif; ?>
	</form>
</div>