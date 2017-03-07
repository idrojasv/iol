<?php
/*
 * Creating a logo Options
 */

$support = $titan->createThemeCustomizerSection( array(
	'id'       => 'support_documentation',
	'position' => 1,
	'name'     => esc_html__( 'Support & Documentation', 'eduma' ),
	'desc' => '<div class="support-documentation">
					<a href="https://themeforest.net/item/education-wordpress-theme-education-wp/14058034" target="_blank">Theme Details</a>
					<a href="http://docs.thimpress.com/eduma/" target="_blank">Theme Documentation</a>
					<a href="https://wordpress.org/plugins/learnpress/" target="_blank">LearnPress Community</a>
					<div class="description">
						<p>Want to ask <b>support question</b> or <b>report bug</b>? Please use our <a href="https://thimpress.com/forums/forum/eduma/" target="_blank" title="(Support\'s response time can be up to 1 business day. Our time zone is GMT+7)">support forum.</a></p>
						<p>Already submitted a support topic for <b>more than 3 days</b> but it hasn\'t been answered or the answer is not up to your expectation yet?</p>
						<p><b>Your ticket may have got lost somewhere!</b> Please send <a href="mailto:tungnd@foobla.com" target="_blank">Tung</a> an email and he will assign your issue directly to a <b>developer team member</b>.</p>
						<p>If you like this theme, I\'d appreciate any of the following:</p>
					</div>
					<div class="ratings-social">
						<a href="https://themeforest.net/downloads" target="_blank">Rate this Theme</a>
						<a href="https://www.facebook.com/ThimPress/" target="_blank">Like on Facebook</a>
						<a href="https://twitter.com/thimpress" target="_blank">Follow on Twitter</a>
						<a href="https://www.youtube.com/c/ThimPressDesign" target="_blank">Subscribe Youtube</a>
					</div>
				</div>'
) );

$support->createOption(
	array(
		'name' => 'Test options',
		'id' => 'my_text_option',
		'type' => 'text',
		'desc' => 'This is our test option'
	)
);
