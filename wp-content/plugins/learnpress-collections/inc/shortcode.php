<?php

/**
 * Adds LP_Collections_Widget widget.
 */
class LP_Collections_Shortcode {

	protected $shortcode_tag = '';

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		add_shortcode( $this->shortcode_tag , array( $this, 'shortcode_func' ) );
	}

	/**
	 * Front-end display
	 *
	 * @param array $atts
	 * @param array $instance
	 */
	public function shortcode_func( $atts, $instance='' ) {
		$atts = shortcode_atts( array(
			'before_shortcode'	=> '',
			'before_title'		=> '',
			'title'				=> '',
			'after_title'		=> '',
			'number'			=> -1,
			'after_shortcode'	=> '',
		), $atts, 'bartag' );
		echo $atts['before_shortcode'];
		if ( !empty( $atts['title'] ) ) {
			echo $atts['before_title'] . $atts['title'] . $atts['after_title'];
		}
		// Query collections
		$query_args = array(
			'post_type'      => 'lp_collection',
			'post_status'    => 'publish',
			'posts_per_page' => ( !empty ( $atts['number'] ) ? $atts['number'] : - 1 )
		);
		// The Query
		$the_query = new WP_Query( $query_args );

		// The Loop
		if ( $the_query->have_posts() ) {
			echo '<ul class="learn-press-collection-widget">';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
			}
			echo '</ul>';
		}
		/* Restore original Post Data */
		wp_reset_postdata();
		echo $atts['after_shortcode'];
	}

}
