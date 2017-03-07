<?php

$typography = $titan->createThimCustomizerSection( array(
	'name'     => esc_html__( 'Typography', 'eduma' ),
	'position' => 50,
	'id'       => 'typography'
) );

$typography->addSubSection( array(
	'name'     => esc_html__( 'Body', 'eduma' ),
	'id'       => 'typography_font_body',
	'position' => 1,
) );
$typography->createOption( array(
	'name'                => esc_html__( 'Select Font', 'eduma' ),
	'id'                  => 'font_body',
	'type'                => 'font-color',
	'show_font_family'    => true,
	'show_font_weight'    => true,
	'show_font_size'      => true,
	'show_line_height'    => true,
	'show_font_style'     => false,
	'show_letter_spacing' => false,
	'show_text_transform' => false,
	'show_font_variant'   => false,
	'show_text_shadow'    => false,
	'default'             => array(
		'font-family'   => 'Roboto',
		'color-opacity' => '#666666',
		'line-height'   => '1.7em',
		'font-weight'   => 'normal',
		'font-size'     => '15px'
	),
) );
$typography->addSubSection( array(
	'name'     => esc_html__( 'Heading', 'eduma' ),
	'id'       => 'typography_font_title',
	'position' => 2,
) );


$typography->createOption( array(
	'name'                => esc_html__( 'Select Font', 'eduma' ),
	'id'                  => 'font_title',
	'type'                => 'font-color',
	'show_font_family'    => true,
	'show_color'          => true,
	'show_font_weight'    => true,
	'show_font_style'     => false,
	'show_font_size'      => false,
	'show_line_height'    => false,
	'show_letter_spacing' => false,
	'show_text_transform' => false,
	'show_font_variant'   => false,
	'show_text_shadow'    => false,
	'default'             => array(
		'font-family'    => 'Roboto Slab',
		'font-weight'    => '700',
		'color-opacity'  => '#333333',
	)
) );

$typography->addSubSection( array(
	'name'     => esc_html__( 'H1', 'eduma' ),
	'id'       => 'typography_font_h1',
	'position' => 3,
) );
$typography->createOption( array(
	'name'                => esc_html__( 'Select Font', 'eduma' ),
	'id'                  => 'font_h1',
	'type'                => 'font-color',
	'show_font_family'    => true,
	'show_font_weight'    => true,
	'show_font_style'     => true,
	'show_font_size'      => false,
	'show_line_height'    => false,
	'show_text_transform' => false,
	'show_letter_spacing' => false,
	'show_font_variant'   => false,
	'show_text_shadow'    => false,
	'default'             => array(
		'font-family'    => 'Roboto Slab',
		'font-weight'    => '700',
		'color-opacity'  => '#333333',
		'font-style'     => 'normal',
		'line-height'    => '1.6em',
		'text-transform' => 'none',
		'font-size'      => '36px'
	)
) );
$typography->addSubSection( array(
	'name'     => esc_html__( 'H2', 'eduma' ),
	'id'       => 'typography_font_h2',
	'position' => 4,
) );

$typography->createOption( array(
	'name'                => esc_html__( 'Select Font', 'eduma' ),
	'id'                  => 'font_h2',
	'type'                => 'font-color',
	'show_font_family'    => true,
	'show_font_weight'    => true,
	'show_font_style'     => true,
	'show_font_size'      => false,
	'show_line_height'    => false,
	'show_letter_spacing' => false,
	'show_text_transform' => false,
	'show_font_variant'   => false,
	'show_text_shadow'    => false,
	'default'             => array(
		'font-family'    => 'Roboto Slab',
		'font-weight'    => '700',
		'color-opacity'  => '#333333',
		'font-style'     => 'normal',
		'line-height'    => '1.6em',
		'text-transform' => 'none',
		'font-size'      => '28px'
	)
) );

$typography->addSubSection( array(
	'name'     => esc_html__( 'H3', 'eduma' ),
	'id'       => 'typography_font_h3',
	'position' => 5,
) );

$typography->createOption( array(
	'name'                => esc_html__( 'Select Font', 'eduma' ),
	'id'                  => 'font_h3',
	'type'                => 'font-color',
	'show_font_family'    => true,
	'show_font_size'      => true,
	'show_font_weight'    => true,
	'show_font_style'     => false,
	'show_line_height'    => false,
	'show_letter_spacing' => false,
	'show_text_transform' => false,
	'show_font_variant'   => false,
	'show_text_shadow'    => false,
	'default'             => array(
		'font-family'    => 'Roboto Slab',
		'font-weight'    => '700',
		'color-opacity'  => '#333333',
		'font-style'     => 'normal',
		'line-height'    => '1.6em',
		'text-transform' => 'none',
		'font-size'      => '24px'
	)
) );

$typography->addSubSection( array(
	'name'     => esc_html__( 'H4', 'eduma' ),
	'id'       => 'typography_font_h4',
	'position' => 6,
) );

$typography->createOption( array(
	'name'                => esc_html__( 'Select Font', 'eduma' ),
	'id'                  => 'font_h4',
	'type'                => 'font-color',
	'show_font_family'    => true,
	'show_font_size'      => true,
	'show_font_weight'    => true,
	'show_font_style'     => false,
	'show_line_height'    => false,
	'show_letter_spacing' => false,
	'show_text_transform' => false,
	'show_font_variant'   => false,
	'show_text_shadow'    => false,
	'default'             => array(
		'font-family'    => 'Roboto Slab',
		'font-weight'    => '700',
		'color-opacity'  => '#333333',
		'font-style'     => 'normal',
		'line-height'    => '1.6em',
		'text-transform' => 'none',
		'font-size'      => '18px'
	),
) );

$typography->addSubSection( array(
	'name'     => esc_html__( 'H5', 'eduma' ),
	'id'       => 'typography_font_h5',
	'position' => 7,
) );

$typography->createOption( array(
	'name'                => esc_html__( 'Select Font', 'eduma' ),
	'id'                  => 'font_h5',
	'type'                => 'font-color',
	'show_font_family'    => true,
	'show_font_weight'    => true,
	'show_font_style'     => true,
	'show_line_height'    => false,
	'show_font_size'      => false,
	'show_letter_spacing' => false,
	'show_text_transform' => false,
	'show_font_variant'   => false,
	'show_text_shadow'    => false,
	'default'             => array(
		'font-family'    => 'Roboto Slab',
		'font-weight'    => '700',
		'color-opacity'  => '#333333',
		'font-style'     => 'normal',
		'line-height'    => '1.6em',
		'text-transform' => 'none',
		'font-size'      => '16px'
	)
) );

$typography->addSubSection( array(
	'name'     => esc_html__( 'H6', 'eduma' ),
	'id'       => 'typography_font_h6',
	'position' => 8,
) );

$typography->createOption( array(
	'name'                => esc_html__( 'Select Font', 'eduma' ),
	'id'                  => 'font_h6',
	'type'                => 'font-color',
	'show_font_family'    => true,
	'show_font_weight'    => true,
	'show_font_size'      => false,
	'show_font_style'     => true,
	'show_line_height'    => false,
	'show_letter_spacing' => false,
	'show_text_transform' => false,
	'show_font_variant'   => false,
	'show_text_shadow'    => false,
	'default'             => array(
		'font-family'    => 'Roboto Slab',
		'font-weight'    => '700',
		'color-opacity'  => '#333333',
		'font-style'     => 'normal',
		'line-height'    => '1.4em',
		'text-transform' => 'none',
		'font-size'      => '16px'
	)
) );