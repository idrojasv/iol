<?php
/**
 * The default template for displaying content
 *
 * Used for single posts display
 *
 * @package WordPress
 * @subpackage Next
 * @since 1.0
 */

$kleo_post_format = get_post_format();
?>

<div class="small-thumbs">
	<article id="post-<?php the_ID(); ?>" <?php post_class(array("clearfix")); ?>>

		<div class="entry-c">

			<?php if ( ! is_single() ) : ?>

			<header class="entry-header">
				<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
			</header>

			<?php endif; ?>

			

			<?php if ( ! in_array( $kleo_post_format, array('status', 'quote', ) ) ): ?>

				<div class="entry-content">

					<?php the_content( wp_kses_post( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'buddyapp' ) ) ); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'buddyapp' ), 'after' => '</div>' ) ); ?>

				</div><!--end entry-content-->

			<?php endif;?>

		</div>

	</article>
</div>