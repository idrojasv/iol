<?php
/**
 * @author        ThimPress
 * @package       LearnPress/Templates
 * @version       1.0
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php if ( learn_press_is_course() ): ?>
	<div id="lp-single-course" class="learnpress-content learn-press">
<?php else: ?>
	<div id="lp-archive-courses">
<?php endif; ?>
