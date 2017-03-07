<?php
$courses->addSubSection( array(
	'name'     => esc_html__( 'Features', 'eduma' ),
	'id'       => 'learnpress_features',
	'position' => 5,
) );

$courses->createOption( array(
	'name'    => esc_html__( 'Enable Login Popup', 'eduma' ),
	'id'      => 'learnpress_single_popup',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Enable login popup when take this course with user not logged in.', 'eduma' ),
	'default' => true,
) );

$courses->createOption( array(
	'name'    => esc_html__( 'One Course ID', 'eduma' ),
	'id'      => 'learnpress_one_course_id',
	'type'    => 'text',
	'desc'    => esc_html__( 'Only use for Demo One Course.', 'eduma' ),
	'default' => '',
) );

$courses->createOption( array(
	'name'    => esc_html__( 'Contact Form 7 Shortcode', 'eduma' ),
	'id'      => 'learnpress_shortcode_contact',
	'type'    => 'text',
	'desc'    => esc_html__( 'Only use for Demo Kindergarten', 'eduma' ),
	'default' => '',
) );

$courses->createOption( array(
	'name'    => esc_html__( 'Timetable Link', 'eduma' ),
	'id'      => 'learnpress_timetable_link',
	'type'    => 'text',
	'desc'    => esc_html__( 'Only use for Demo Kindergarten', 'eduma' ),
	'default' => '',
) );

$courses->createOption( array(
	'name'    => esc_html__( 'Hidden Ads', 'eduma' ),
	'id'      => 'learnpress_hidden_ads',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Hidden ads learnpress on WordPress admin.', 'eduma' ),
	'default' => false,
) );