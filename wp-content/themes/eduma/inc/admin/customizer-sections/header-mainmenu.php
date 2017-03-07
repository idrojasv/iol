<?php
// main menu
$header->addSubSection( array(
	'name'     => esc_html__( 'Main Menu', 'eduma' ),
	'id'       => 'display_main_menu',
	'position' => 5,
) );

$header->createOption( array(
	'name'    => esc_html__( 'Select a Layout', 'eduma' ),
	'id'      => 'header_style',
	'type'    => 'radio-image',
	'options' => array(
		"header_v1" => get_template_directory_uri( 'template_directory' ) . "/images/admin/header/header_v1.jpg",
		"header_v2" => get_template_directory_uri( 'template_directory' ) . "/images/admin/header/header_v2.jpg",
		"header_v3" => get_template_directory_uri( 'template_directory' ) . "/images/admin/header/header_v3.jpg",
	),
	'default' => 'header_v1',
) );

$header->createOption( array(
	'name'    => esc_html__( 'Header Position', 'eduma' ),
	'id'      => 'header_position',
	'type'    => 'select',
	'options' => array(
		'header_default' => esc_html__( 'Default', 'eduma' ),
		'header_overlay' => esc_html__( 'Overlay', 'eduma' ),
	),
	'default' => 'header_overlay',

) );

$header->createOption( array(
	'name'        => esc_html__( 'Background Color', 'eduma' ),
	'id'          => 'bg_main_menu_color',
	'default'     => '',
	'type'        => 'color-opacity',
	'livepreview' => '$(".site-header").css("background", value);'
) );

$header->createOption( array(
	'name'        => esc_html__( 'Text Color', 'eduma' ),
	'id'          => 'main_menu_text_color',
	'default'     => '#ffffff',
	'type'        => 'color-opacity',
	'livepreview' => '$(".navigation .navbar-nav > li > a,.navigation .navbar-nav > li > span,.widget_shopping_cart .minicart_hover .cart-items-number,.thim-course-search-overlay .search-toggle").css("color", value);'
) );

$header->createOption( array(
	'name'        => esc_html__( 'Text Hover Color', 'eduma' ),
	'id'          => 'main_menu_text_hover_color',
	'default'     => '#ffffff',
	'type'        => 'color-opacity',
	'livepreview' => '
		var menu_color = $(".navigation .navbar-nav > li > a").css("color");
		$(".navigation .navbar-nav > li > a,.navigation .navbar-nav > li > span").on({
			"mouseenter": function(){
				$(this).css("color", value);
			},
			"mouseleave" : function(){
				$(this).css("color", menu_color);
			}
		});
		'
) );

$header->createOption( array(
	'name'        => esc_html__( 'Font Size', 'eduma' ),
	'id'          => 'font_size_main_menu',
	'default'     => '14px',
	'type'        => 'select',
	'options'     => $font_sizes,
	'livepreview' => '$(".navigation .navbar-nav > li > a,.navigation .navbar-nav > li > span").css("fontSize", value);'
) );

$header->createOption( array(
	'name'        => esc_html__( 'Font Weight', 'eduma' ),
	'id'          => 'font_weight_main_menu',
	'default'     => '600',
	'type'        => 'select',
	'options'     => array(
		'bold'   => 'Bold',
		'normal' => 'Normal',
		'100'    => '100',
		'200'    => '200',
		'300'    => '300',
		'400'    => '400',
		'500'    => '500',
		'600'    => '600',
		'700'    => '700',
		'800'    => '800',
		'900'    => '900'
	),
	'livepreview' => '$(".navigation .navbar-nav > li > a,.navigation .navbar-nav > li > span").css("font-weight", value);'
) );
