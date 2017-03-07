<?php
$events->addSubSection( array(
	'name'     => esc_html__( 'Features', 'eduma' ),
	'id'       => 'event_features',
	'position' => 5,
) );

$events->createOption( array(
	'name'    => esc_html__( 'Display year', 'eduma' ),
	'id'      => 'event_display_year',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Display year on date of Events.', 'eduma' ),
	'default' => false,
) );