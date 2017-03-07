<?php

vc_map( array(
	'name'        => esc_html__( 'Thim: Tab events', 'eduma' ),
	'base'        => 'thim-tab-event',
	'category'    => esc_html__( 'Thim Shortcodes', 'eduma' ),
	'description' => esc_html__( 'Show all event with tab', 'eduma' ),
	'params'      => array(
		array(
			'type'        => 'textfield',
			'admin_label' => true,
			'heading'     => esc_html__( 'Title', 'eduma' ),
			'param_name'  => 'title',
			'value'       => '',
		),
	)
) );