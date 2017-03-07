<?php

do_action( 'learn_press_before_all_wishlist' );
echo '<h3 class="box-title">' . esc_html__( 'Favorite courses', 'eduma' ) . '</h3>';
do_action( 'learn_press_before_wishlist_course' );
$my_query = learn_press_get_wishlist_courses( $user->ID );
if ( $my_query->have_posts() ) :
	?>
	<div class="thim-carousel-wrapper thim-course-carousel thim-course-grid" data-visible="3" data-pagination="0" data-navigation="1">
		<?php
		while ( $my_query->have_posts() ) : $my_query->the_post();
			learn_press_get_template( 'addons/course-wishlist/wishlist-content.php' );
		endwhile;
		?>
	</div>
	<?php
else :
	do_action( 'learn_press_before_no_wishlist_course' );
	echo '<p>' . esc_html__( 'No courses in your wishlist!', 'eduma' ) . '</p>';
	do_action( 'learn_press_after_no_wishlist_course' );
endif;
do_action( 'learn_press_after_wishlist_course' );
wp_reset_postdata();
do_action( 'learn_press_after_all_wishlist' );

