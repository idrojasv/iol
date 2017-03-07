<?php $review_is_enable = thim_plugin_active( 'learnpress-course-review/learnpress-course-review.php' ); ?>


<div class="course-item">
	<div class="course-thumbnail">
		<a href="<?php echo get_the_permalink(); ?>">
			<?php
			echo thim_get_feature_image( get_post_thumbnail_id(), 'full', 450, 450, get_the_title() );
			?>
		</a>
		<?php echo '<a class="course-readmore" href="' . esc_url( get_the_permalink() ) . '">' . esc_html__( 'Read More', 'eduma' ) . '</a>'; ?>
	</div>
	<div class="thim-course-content">
		<?php do_action( 'learn_press_before_own_course_title' ); ?>
		<h2 class="course-title">
			<a rel="bookmark" href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a>
		</h2>

		<div class="course-passed">
			<?php $passed = learn_press_count_students_passed(); ?>
			<?php if ( $passed > 1 ) : ?>
				<?php echo '<p><span class="value">' . $passed . '</span>' . esc_html__( ' students passed', 'eduma' ) . '</p>'; ?>
			<?php else : ?>
				<?php echo '<p><span class="value">' . $passed . '</span>' . esc_html__( ' student passed', 'eduma' ) . '</p>'; ?>
			<?php endif; ?>
		</div>
		<?php do_action( 'learn_press_after_own_course_title' ); ?>
		<div class="course-meta">
			<div class="course-students">
				<div class="value"><i class="fa fa-group"></i>
					<?php echo learn_press_count_students_enrolled(); ?>
				</div>
			</div>
			<?php if ( $review_is_enable ) : ?>
				<div class="course-comments-count">
					<div class="value"><i class="fa fa-comment"></i>
						<?php echo learn_press_get_course_rate_total( get_the_ID() ) ? learn_press_get_course_rate_total( get_the_ID() ) : 0; ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="course-price">
				<div class="value <?php echo learn_press_is_free_course() ? 'free-course' : ''; ?>">
					<?php echo learn_press_get_course_price( '', true ); ?>
				</div>
			</div>
		</div>
	</div>
</div>


