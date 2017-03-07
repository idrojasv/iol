<?php if( !learn_press_get_quiz_questions() ) : ?>
	<p class="message message-error"><?php esc_html_e( 'No question in this quiz!', 'eduma' ); ?></p>
<?php endif; ?>