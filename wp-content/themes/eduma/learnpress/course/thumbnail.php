<?php
/**
 * Template for displaying the thumbnail of a course
 */

learn_press_prevent_access_directly();
do_action( 'learn_press_before_course_thumbnail' );
if ( is_singular() ) {
	if ( has_post_thumbnail() ) {
		?>
		<div class="course-thumbnail">
			<?php the_post_thumbnail( 'full', array( 'alt' => get_the_title() ) ); ?>
		</div>
		<?php
	}
} else {
	?>
	<div class="course-thumbnail">
		<a href="<?php echo get_the_permalink(); ?>">
			<?php
			echo thim_get_feature_image( get_post_thumbnail_id( get_the_ID() ), 'full', 450, 450, get_the_title() );
			?>
		</a>
		<?php thim_course_wishlist_button(); ?>
		<?php echo '<a class="course-readmore" href="'.esc_url( get_the_permalink() ).'">'.esc_html__('Read More','eduma').'</a>'; ?>
	</div>
	<?php
}

do_action( 'learn_press_after_course_thumbnail' );
