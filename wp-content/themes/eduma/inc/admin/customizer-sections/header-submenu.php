<?php
// main menu

$header->addSubSection( array(
	'name'     => esc_html__( 'Sub Menu', 'eduma' ),
	'id'       => 'display_sub_menu',
	'position' => 6,
) );

$header->createOption( array(
	'name'        => esc_html__( 'Background Color', 'eduma' ),
	'id'          => 'sub_menu_bg_color',
	'default'     => '#fff',
	'type'        => 'color-opacity',
	'livepreview' => ' $(".navigation .navbar-nav>li .sub-menu").css("background-color", value); '
) );

$header->createOption( array(
	'name'    => esc_html__( 'Border Color', 'eduma' ),
	'id'      => 'sub_menu_border_color',
	'default' => 'rgba(43,43,43,0)',
	'type'    => 'color-opacity',
	'livepreview' => ' $(".navigation .navbar-nav>li .sub-menu li>a, .navigation .navbar-nav>li .sub-menu li>span").css("border-bottom-color", value); '
) );

$header->createOption( array(
	'name'        => esc_html__( 'Text Color', 'eduma' ),
	'id'          => 'sub_menu_text_color',
	'default'     => '#999',
	'type'        => 'color-opacity',
	'livepreview' => ' $(".navigation .navbar-nav>li .sub-menu li>a, .navigation .navbar-nav>li .sub-menu li>span").css("color", value); '
) );
$header->createOption( array(
	'name'    => esc_html__( 'Text Color Hover', 'eduma' ),
	'id'      => 'sub_menu_text_color_hover',
	'default' => '#333',
	'type'    => 'color-opacity',
	'livepreview' => '
		var sub_menu_color = $(".navigation .navbar-nav>li .sub-menu li>a").css("color");
		$(".navigation .navbar-nav>li .sub-menu li>a, .navigation .navbar-nav>li .sub-menu li>span").on({
			"mouseenter": function(){
				$(this).css("color", value);
			},
			"mouseleave" : function(){
				$(this).css("color", sub_menu_color);
			}
		});
		'
) );

