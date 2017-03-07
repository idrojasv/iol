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
		<div class="course-author">
			<?php echo get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
			<div class="author-contain">
				<div class="value">
					<a href="<?php echo esc_url( apply_filters( 'learn_press_instructor_profile_link', '#', $user_id = null, get_the_ID() ) ); ?>">
						<?php echo get_the_author(); ?>
					</a>
				</div>
			</div>
		</div>
		<?php do_action( 'learn_press_before_passed_course_title' ); ?>
		<h2 class="course-title">
			<a rel="bookmark" href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a>
		</h2>
		<?php do_action( 'learn_press_after_passed_course_title' ); ?>
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


