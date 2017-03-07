<?php

$header->addSubSection( array(
	'name'     => esc_html__( 'Drawer', 'eduma' ),
	'id'       => 'display_right_header',
	'position' => 2,
) );

$header->createOption( array(
	'name'        => esc_html__( 'Show / Hide Drawer', 'eduma' ),
	'id'          => 'show_drawer',
	'type'        => 'checkbox',
	'default'     => false,
	'livepreview' => '
		if(value == false){
			$("#rt-drawer").css("display", "none");
		}else{
			$("#rt-drawer").css("display", "block");
		}
	'
) );

$header->createOption( array(
	'name'        => esc_html__( 'Drawer Background Color', 'eduma' ),
	'id'          => 'bg_drawer_color',
	'type'        => 'color-opacity',
	'default'     => '#ffffff',
	'livepreview' => '$("#rt-drawer").css("background-color", value);'
) );

$header->createOption( array(
	'name'        => esc_html__( 'Drawer Text Color', 'eduma' ),
	'id'          => 'drawer_text_color',
	'type'        => 'color-opacity',
	'default'     => '#ffffff',
	'livepreview' => '$("#rt-drawer a,#rt-drawer,#rt-drawer .widget-title").css("color", value)'
) );

$header->createOption( array(
	'name'        => esc_html__( 'Drawer Style', 'eduma' ),
	'id'          => 'drawer_style',
	'type'        => 'radio-image',
	'options'     => array(
		'style1' => THIM_URI . 'images/patterns/drawer_1.jpg',
		'style2' => THIM_URI . 'images/patterns/drawer_2.jpg',
	),
	'livepreview' => '
		if(value == "style1"){
			$("#rt-drawer").addClass("style1");
			$("#rt-drawer").removeClass("style2");
		}else{
			$("#rt-drawer").addClass("style2");
			$("#rt-drawer").removeClass("style1");
		}
	'
) );

$header->createOption( array(
	'name'        => esc_html__( 'Open / Close Drawer', 'eduma' ),
	'id'          => 'drawer_open',
	'type'        => 'checkbox',
	'livepreview' => '
		if(value == false){
			$("#collapseDrawer").css("height", "0");
			$("#collapseDrawer").removeClass("in");
		}else{
			$("#collapseDrawer").css("height", "auto");
			$("#collapseDrawer").addClass("in");
		}
	'
) );
