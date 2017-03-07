<?php

// header Options
$header->addSubSection( array(
	'name'     => esc_html__( 'Sticky Menu', 'eduma' ),
	'id'       => 'display_header_menu',
	'position' => 14,
) );

$header->createOption( array(
	'name'    => esc_html__( 'Sticky On Scroll', 'eduma' ),
	'desc'    => esc_html__( 'Check to enable a fixed header when scrolling, un-check to disable.', 'eduma' ),
	'id'      => 'header_sticky',
	'type'    => 'checkbox',
	'default' => true
) );

$header->createOption( array(
	'name'    => esc_html__( 'Config Sticky Menu', 'eduma' ),
	'desc'    => '',
	'id'      => 'config_att_sticky',
	'options' => array(
		'sticky_same'   => esc_html__( 'The same with main menu', 'eduma' ),
		'sticky_custom' => esc_html__( 'Custom', 'eduma' ),
	),
	'default' => 'sticky_custom',
	'type'    => 'select'
) );

$header->createOption( array(
	'name'        => esc_html__( 'Background Color', 'eduma' ),
	'id'          => 'sticky_bg_main_menu_color',
	'default'     => '#fff',
	'type'        => 'color-opacity',
	'livepreview' => ' $(".site-header.bg-custom-sticky.affix").css("background-color", value); '
) );

$header->createOption( array(
	'name'        => esc_html__( 'Text Color', 'eduma' ),
	'id'          => 'sticky_main_menu_text_color',
	'default'     => '#333',
	'type'        => 'color-opacity',
	'livepreview' => ' $(".site-header.bg-custom-sticky.affix .navbar-nav>li>a, .site-header.bg-custom-sticky.affix .navbar-nav>li>span,.site-header.affix .widget_shopping_cart .minicart_hover .cart-items-number,.site-header.affix .thim-course-search-overlay .search-toggle").css("color", value); '

) );

$header->createOption( array(
	'name'        => esc_html__( 'Text Hover Color', 'eduma' ),
	'id'          => 'sticky_main_menu_text_hover_color',
	'default'     => '#333',
	'type'        => 'color-opacity',
	'livepreview' => '
		var sticky_color = $(".site-header.bg-custom-sticky.affix .navbar-nav>li>a").css("color");
		$(".site-header.bg-custom-sticky.affix .navbar-nav>li>a, .site-header.bg-custom-sticky.affix .navbar-nav>li>span").on({
			"mouseenter": function(){
				$(this).css("color", value);
			},
			"mouseleave" : function(){
				$(this).css("color", sticky_color);
			}
		});
		'
) );