<?php
$woocommerce->addSubSection( array(
	'name'     => esc_html__( 'Setting', 'eduma' ),
	'id'       => 'woo_setting',
	'position' => 3,
) );

$woocommerce->createOption( array(
	'name'    => esc_html__( 'Grid', 'eduma' ),
	'id'      => 'woo_product_column',
	'type'    => 'select',
	'options' => array(
		'2' => '2',// column bootstrap
		'3' => '3',
		'4' => '4',
	),
	'default' => '4'
) );
$woocommerce->createOption( array(
	'name'    => esc_html__( 'Number Of Products Per Page', 'eduma' ),
	'id'      => 'woo_product_per_page',
	'type'    => 'number',
	'desc'    => esc_html__( 'Insert the number of posts to display per page.', 'eduma' ),
	'default' => '8',
	'max'     => '100',
) );

$woocommerce->createOption( array(
	'name'    => esc_html__( 'Show QuickView', 'eduma' ),
	'id'      => 'woo_set_show_qv',
	'type'    => 'checkbox',
	'default' => true,
) );
