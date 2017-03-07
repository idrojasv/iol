<?php

$footer->addSubSection( array(
	'name'     => esc_html__( 'Footer', 'eduma' ),
	'id'       => 'display_footer',
	'position' => 2,
) );

$footer->createOption( array(
	'name'    => esc_html__( 'Custom Footer Class', 'eduma' ),
	'id'      => 'footer_custom_class',
	'type'    => 'text',
	'default' => '',
) );

$footer->createOption( array(
	'name'    => esc_html__( 'Footer Title Color', 'eduma' ),
	'id'      => 'footer_title_font_color',
	'type'    => 'color-opacity',
	'default' => '#fff',
	'livepreview' => '$("footer#colophon .footer .widget-title, footer#colophon .footer .thim-footer-location .info, footer#colophon .footer .thim-footer-location .info a").css("color", value);'
) );
$footer->createOption( array(
	'name'    => esc_html__( 'Footer Text Color', 'eduma' ),
	'id'      => 'footer_text_font_color',
	'type'    => 'color-opacity',
	'default' => '#999',
	'livepreview' => '$("footer#colophon .footer, footer#colophon .footer a, footer#colophon .footer .thim-footer-location .info .fa").css("color", value);'
) );

$footer->createOption( array(
	'name'        => esc_html__( 'Background Color', 'eduma' ),
	'id'          => 'footer_bg_color',
	'type'        => 'color-opacity',
	'default'     => '#111111',
	'livepreview' => '$("footer#colophon .footer").css("background-color", value);'
) );

$footer->createOption( array(
	'name' => esc_html__( 'Background Image', 'eduma' ),
	'id'   => 'footer_background_img',
	'type' => 'upload',
) );

$footer->createOption( array(
	'name'    => esc_html__( 'Background Position', 'eduma' ),
	'id'      => 'footer_bg_position',
	'type'    => 'select',
	'options' => array(
		'left top'      => esc_html__( 'Left Top', 'eduma' ),
		'left center'   => esc_html__( 'Left Center', 'eduma' ),
		'left bottom'   => esc_html__( 'Left Bottom', 'eduma' ),
		'right top'     => esc_html__( 'Right Top', 'eduma' ),
		'right center'  => esc_html__( 'Right Center', 'eduma' ),
		'right bottom'  => esc_html__( 'Right Bottom', 'eduma' ),
		'center top'    => esc_html__( 'Center Top', 'eduma' ),
		'center center' => esc_html__( 'Center Center', 'eduma' ),
		'center bottom' => esc_html__( 'Center Bottom', 'eduma' )
	),
	'default' => 'center center'
) );