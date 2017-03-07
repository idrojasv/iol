<?php
/*
 * Post and Page Display Settings
 */
$display->addSubSection( array(
	'name'     => esc_html__( 'Archive', 'eduma' ),
	'id'       => 'display_archive',
	'position' => 2,
) );

$display->createOption( array(
	'name'    => esc_html__( 'Layout', 'eduma' ),
	'id'      => 'archive_cate_layout',
	'type'    => 'radio-image',
	'options' => array(
		'full-content'  => THIM_URI . 'images/admin/layout/body-full.png',
		'sidebar-left'  => THIM_URI . 'images/admin/layout/sidebar-left.png',
		'sidebar-right' => THIM_URI . 'images/admin/layout/sidebar-right.png'
	),
	'default' => 'sidebar-right'
) );

$display->createOption( array(
	'name'    => esc_html__( 'Layout Type', 'eduma' ),
	'id'      => 'archive_cate_display_layout',
	'type'    => 'select',
	'options' => array(
		'default' => esc_html__( 'Default', 'eduma' ),
		'grid'    => esc_html__( 'Grid/List', 'eduma' ),
	),
	'default' => 'default'
) );

$display->createOption( array(
	'name'    => esc_html__( 'Hide Breadcrumbs', 'eduma' ),
	'id'      => 'archive_cate_hide_breadcrumbs',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Check this box to hide/show breadcrumbs.', 'eduma' ),
	'default' => false,
) );

$display->createOption( array(
	'name'    => esc_html__( 'Hide Title', 'eduma' ),
	'id'      => 'archive_cate_hide_title',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Check this box to hide/show title.', 'eduma' ),
	'default' => false,
) );

$display->createOption( array(
	'name'        => esc_html__( 'Top Image', 'eduma' ),
	'id'          => 'archive_cate_top_image',
	'type'        => 'upload',
	'desc'        => esc_html__( 'Enter URL or upload a top image file for header.', 'eduma' ),
	'default'     => THIM_URI . 'images/bg-page.jpg',
	'livepreview' => ''
) );

$display->createOption( array(
	'name'        => esc_html__( 'Heading Background Color', 'eduma' ),
	'id'          => 'archive_cate_heading_bg_color',
	'type'        => 'color-opacity',
	'livepreview' => ''
) );

$display->createOption( array(
	'name'    => esc_html__( 'Heading Text Color', 'eduma' ),
	'id'      => 'archive_cate_heading_text_color',
	'type'    => 'color-opacity',
	'default' => '#fff',
) );

$display->createOption( array(
	'name'    => esc_html__( 'Sub Heading Text Color', 'eduma' ),
	'id'      => 'archive_cate_sub_heading_text_color',
	'type'    => 'color-opacity',
	'default' => '#878787',
) );

$display->createOption( array(
	'name'    => esc_html__( 'Sub Title', 'eduma' ),
	'id'      => 'archive_cate_sub_title',
	'type'    => 'text',
	'default' => '',
) );

$display->createOption( array(
	'name'    => esc_html__( 'Excerpt Length', 'eduma' ),
	'id'      => 'archive_excerpt_length',
	'type'    => 'number',
	'desc'    => esc_html__( 'Enter the number of words you want to cut from the content to be the excerpt of search and archive and portfolio page.', 'eduma' ),
	'default' => '40',
	'max'     => '100',
	'min'     => '10',
) );


$display->createOption( array(
	'name'    => esc_html__( 'Show Category', 'eduma' ),
	'id'      => 'show_category',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'show/hidden', 'eduma' ),
	'default' => true,
) );

$display->createOption( array(
	'name'    => esc_html__( 'Show Date', 'eduma' ),
	'id'      => 'show_date',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'show/hidden', 'eduma' ),
	'default' => true,
) );

$display->createOption( array(
	'name'    => esc_html__( 'Show Comment', 'eduma' ),
	'id'      => 'show_comment',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'show/hidden', 'eduma' ),
	'default' => true,
) );

$display->createOption( array(
	'name'    => esc_html__( 'Show Author', 'eduma' ),
	'id'      => 'show_author',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'show/hidden', 'eduma' ),
	'default' => true,
) );

$display->createOption( array(
	'name'    => esc_html__( 'Display Year', 'eduma' ),
	'id'      => 'blog_display_year',
	'type'    => 'checkbox',
	'desc'    => esc_html__( 'Display year on date of Blog.', 'eduma' ),
	'default' => false,
) );