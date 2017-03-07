<li class="section">
	<?php do_action( 'learn_press_before_section' ); ?>
	<?php if ( $cur_name = $curriculum_course['name'] ) { ?>
		<h4 class="section-header">
			<?php do_action( 'learn_press_before_section_header' ); ?>
			<?php echo esc_html( $cur_name ); ?>
			<?php do_action( 'learn_press_after_section_header' ); ?>
		</h4>
	<?php } ?>
	<?php if ( array_key_exists( 'lesson_quiz', $curriculum_course ) ) { ?>
		<ul class="section-content">
			<?php
			$count = array();
			foreach ( $curriculum_course['lesson_quiz'] as $lesson_quiz ) {
				$post_type = str_replace( 'lpr_', '', get_post_type( $lesson_quiz ) );
				if ( empty( $count[$post_type] ) ) {
					$count[$post_type] = 1;
				} else {
					$count[$post_type] ++;
				}
				$index = $section_index . '.' . $count[$post_type];
				if ( !in_array( $post_type, array( 'lesson', 'quiz', 'assignment' ) ) ) continue;
				$args = array(
					'lesson_quiz' => $lesson_quiz,
					'index'       => $index
				);
				learn_press_get_template( "course/loop-curriculum-{$post_type}.php", $args );
			}
			?>
		</ul>
	<?php } ?>
	<?php do_action( 'learn_press_after_section' ); ?>
</li>