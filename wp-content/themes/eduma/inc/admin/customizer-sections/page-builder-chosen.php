<?php
$page_builder->createOption( array(
	'name'    => esc_html__( 'Page Builder', 'eduma' ),
	'id'      => 'page_builder_chosen',
	'type'    => 'select',
	'options' => array(
		''                => esc_html__( 'Choose your page builder', 'eduma' ),
		'site_origin'     => esc_html__( 'Site Origin', 'eduma' ),
		'visual_composer' => esc_html__( 'Visual Composer', 'eduma' ),
	),
	'default' => 'site_origin',
) );

$page_builder->createOption( array(
	'name' => esc_html__( 'Footer Bottom Background Image', 'eduma' ),
	'id'   => 'footer_bottom_bg_img',
	'type' => 'upload',
	'desc' => esc_html__('Choose background image for footer button. This option work with Visual Composer.', 'eduma')
) );