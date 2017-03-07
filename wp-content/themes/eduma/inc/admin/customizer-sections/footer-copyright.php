<?php
$footer->addSubSection( array(
	'name'     => esc_html__( 'Copyright', 'eduma' ),
	'id'       => 'display_copyright',
	'position' => 3,
) );

$footer->createOption( array(
	'name'        => esc_html__( 'Background Color', 'eduma' ),
	'id'          => 'copyright_bg_color',
	'type'        => 'color-opacity',
	'default'     => '#111111',
	'livepreview' => '$("footer#colophon .copyright-area").css("background-color", value);'
) );

$footer->createOption( array(
	'name'        => esc_html__( 'Text Color', 'eduma' ),
	'id'          => 'copyright_text_color',
	'type'        => 'color-opacity',
	'default'     => '#999999',
	'livepreview' => '$("footer#colophon .copyright-area,footer#colophon .copyright-area ul li a").css("color", value);'
) );

$footer->createOption( array(
	'name'        => esc_html__( 'Copyright Text', 'eduma' ),
	'id'          => 'copyright_text',
	'type'        => 'textarea',
	'default'     => '<a href="' . esc_url( 'https://themeforest.net/item/education-wordpress-theme-education-wp/14058034' ) . '" target="_blank">Education WordPress Theme</a> by <a href="' . esc_url( 'http://www.thimpress.com' ) . '" target="_blank">ThimPress.</a> Powered by WordPress.',
	'livepreview' => '$("footer#colophon .copyright-area .text-copyright").html(function(){return value ;})'
) );

$footer->createOption( array(
	'name'    => esc_html__( 'Back To Top', 'eduma' ),
	'id'      => 'show_to_top',
	'type'    => 'checkbox',
	'des'     => esc_html__( 'Show or hide this button.', 'eduma' ),
	'default' => true

) );