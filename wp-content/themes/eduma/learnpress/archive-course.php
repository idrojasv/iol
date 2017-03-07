<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( is_tax() ) {
	$total = get_queried_object();
	$total = $total->count;
} elseif ( !empty( $_REQUEST['s'] ) ) {
	$total = thim_get_courses_by_search_key( $_REQUEST['s'] );
} else {
	$total = wp_count_posts( 'lpr_course' );
	$total = $total->publish;
}

if ( $total == 0 ) {
	echo '<p class="message message-error">' . esc_html__( 'There are no available courses!', 'eduma' ) . '</p>';
	return;
} elseif ( $total == 1 ) {
	$index = esc_html__( 'Showing only one result', 'eduma' );
} else {
	$courses_per_page = get_option( 'posts_per_page', 10 );
	$paged            = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

	$from = 1 + ( $paged - 1 ) * $courses_per_page;
	$to   = ( $paged * $courses_per_page > $total ) ? $total : $paged * $courses_per_page;

	if ( $from == $to ) {
		$index = sprintf(
			esc_html__( 'Showing last course of %s results', 'eduma' ),
			$total
		);
	} else {
		$index = sprintf(
			esc_html__( 'Showing %s-%s of %s results', 'eduma' ),
			$from,
			$to,
			$total
		);
	}
}

?>

<?php if ( have_posts() ) : ?>
	<div class="thim-course-top switch-layout-container">
		<div class="thim-course-switch-layout switch-layout">
			<a href="#" class="list switchToGrid "><i class="fa fa-th-large"></i></a>
			<a href="#" class="grid switchToList"><i class="fa fa-list-ul"></i></a>
		</div>
		<div class="course-index">
			<span><?php echo( $index ); ?></span>
		</div>
		<div class="courses-searching">
			<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="text" value="" name="s" placeholder="<?php esc_html_e( 'Search our courses', 'eduma' ) ?>" class="thim-s form-control courses-search-input" autocomplete="off" />
				<input type="hidden" value="course" name="ref" />
				<button type="submit"><i class="fa fa-search"></i></button>
				<span class="widget-search-close"></span>
			</form>
			<ul class="courses-list-search list-unstyled"></ul>
		</div>
	</div>

	<!-- <div class="thim-course-grid"> -->
	<div id="thim-course-archive" class="thim-course-grid">
		<?php
		// Start the Loop.

		while ( have_posts() ) : the_post();
			learn_press_get_template( 'archive-course-content.php' );
		endwhile;

		?>
	</div>
	<?php
// Previous/next page navigation.
	learn_press_course_paging_nav();
endif;
?>

