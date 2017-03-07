<?php
$share_setting = $titan->createMetaBox( array(
	'name'      => esc_html__( 'Share This and Number Related', 'eduma' ),
	'post_type' => array( 'post', ),
) );
$share_setting->createOption( array(
	'name'    => esc_html__( 'Number Related', 'eduma' ),
	'id'      => 'number_related',
	'type'    => 'number',
	'default' => 3,
	'min'     => '1',
	'max'     => '10'
) );