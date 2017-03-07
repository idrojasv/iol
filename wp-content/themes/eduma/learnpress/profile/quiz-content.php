<?php

$result = learn_press_get_quiz_result( $user_id, $quiz_id );
?>

<div class="quiz-results">

	<h3 class="box-title"><?php echo get_the_title( $quiz_id ); ?></h3>

	<div class="result-summary">
		<div class="result-field correct">
			<span><?php esc_html_e( 'Correct', 'eduma' ); ?></span>
			<span class="value"><?php printf( '%d', $result['correct'] ); ?></span>
		</div>
		<div class="result-field wrong">
			<span><?php esc_html_e( 'Incorrect', 'eduma' ); ?></span>
			<span class="value"><?php printf( '%d', $result['wrong'] ); ?></span>
		</div>
		<div class="result-field empty">
			<span><?php esc_html_e( 'Skipped', 'eduma' ); ?></span>
			<span class="value"><?php printf( '%d', $result['empty'] ); ?></span>
		</div>
		<div class="result-field points">
			<span><?php esc_html_e( 'Points', 'eduma' ); ?></span>
			<span class="value"><?php printf( '%d/%d', $result['mark'], $result['mark_total'] ); ?></span>
		</div>
	</div>
</div>
