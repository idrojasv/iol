<?php
$data = $titan->createThimCustomizerSection( array(
	'name'     => esc_html__( 'Import/Export', 'eduma' ),
	'desc'     => esc_html__( 'You can export then import settings from one theme to another conveniently without any problem.', 'eduma' ),
	'position' => 200,
	'id'       => 'import_export',
) );

$data->createOption( array(
	'name' => esc_html__( 'Import Settings', 'eduma' ),
	'id'   => 'import_setting',
	'type' => 'customize-import',
	'desc' => esc_html__( 'Click upload button then choose a JSON file (.json) from your computer to import settings to this theme.', 'eduma' ),
) );

$data->createOption( array(
	'name' => esc_html__( 'Export Settings', 'eduma' ),
	'id'   => 'export_setting',
	'type' => 'customize-export',
	'desc' => esc_html__( 'Simply click download button to export all your settings to a JSON file (.json).', 'eduma' ),
) );
