<?php


$header->addSubSection( array(
	'name'     => esc_html__( 'Toolbar', 'eduma' ),
	'id'       => 'display_toolbar',
	'position' => 3,
) );


$header->createOption( array(
	'name'    => esc_html__( 'Show / Hide Toolbar', 'eduma' ),
	'id'      => 'toolbar_show',
	'type'    => 'checkbox',
	'default' => true,

) );

$header->createOption( array(
	'name'    => esc_html__( 'Font Size', 'eduma' ),
	'id'      => 'font_size_toolbar',
	'type'    => 'select',
	'options' => $font_sizes,
	'default' => '12px',
	'livepreview' => '$("#toolbar").css("fontSize", value);'
) );

$header->createOption( array(
	'name'        => esc_html__( 'Background Color', 'eduma' ),
	'id'          => 'bg_color_toolbar',
	'type'        => 'color-opacity',
	'default'     => '#111111',
	'livepreview' => '$("#toolbar").css("background-color", value);'
) );

$header->createOption( array(
	'name'    => esc_html__( 'Text Color', 'eduma' ),
	'id'      => 'text_color_toolbar',
	'type'    => 'color-opacity',
	'default' => '#ababab',
	'livepreview' => '
		$("#toolbar").css("color", value);
		$("#toolbar .thim-link-login a:first-child").css("border-right-color", value);
		'
) );

$header->createOption( array(
	'name'    => esc_html__( 'Link Color', 'eduma' ),
	'id'      => 'link_color_toolbar',
	'type'    => 'color-opacity',
	'default' => '#fff',
	'livepreview' => '$("#toolbar a, #toolbar span.value").css("color", value);'
) );

