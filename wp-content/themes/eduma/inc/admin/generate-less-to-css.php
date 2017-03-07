<?php

/**
 * This class generates custom CSS into static CSS file in uploads folder
 * and enqueue it in the frontend
 *
 * CSS is generated only when theme options is saved (changed)
 * Works with LESS (for unlimited color schemes)
 *
 *
 */
require_once( TP_FRAMEWORK_LIBS_DIR . "less/lessc.inc.php" );
require_once( THIM_DIR . "inc/admin/theme-options-to-css.php" );

function thim_generate( $fileout, $type, $theme_option_variations ) {
	$css   = "";
	$regex = array(
		"`^([\t\s]+)`ism"                       => '',
		"`^\/\*(.+?)\*\/`ism"                   => "",
		"`([\n\A;]+)\/\*(.+?)\*\/`ism"          => "$1",
		"`([\n\A;\s]+)//(.+?)[\n\r]`ism"        => "$1\n",
		"`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism" => "",
		"/\n/i"                                 => ""
	);

	$compiler = new lessc;
	$compiler->setFormatter( 'compressed' );
	$css .= $compiler->compileFile( THIM_DIR . 'assets/less/theme-options.less' );

	$css .= thim_customcss();

	$css   = preg_replace( array_keys( $regex ), $regex, $css );
	$style = thim_file_get_contents( THIM_DIR . "inc/theme-info.txt" );
	// Determine whether Multisite support is enabled
	if ( is_multisite() ) {
		// Write Theme Info into style.css
		//$style .= $css;

		thim_file_put_contents( $fileout . $type, $style );
		// Write the rest to specific site style-ID.css
		$fileout = $fileout . '-' . get_current_blog_id();
		$style .= $css;
		thim_file_put_contents( $fileout . $type, $style );
	} else {
		// If this is not multisite, we write them all in style.css file
		$style .= $css;
		thim_file_put_contents( $fileout . $type, $style );
	}
}

function thim_collect_content_files( $folder, $file_type ) {
	$files        = scandir( $folder );
	$content_file = '';
	foreach ( $files as $file ) {
		if ( strpos( $file, $file_type ) !== false ) {
			$content_file .= thim_file_get_contents( $folder . $file );
		}
	}
	return $content_file;
}

function thim_generate_less_to_css( $less_folder, $params, $ignore_files ) {
	$files                = scandir( $less_folder );
	$css                  = '';
	$content_file_options = "";
	foreach ( $files as $file ) {
		if ( strpos( $file, 'less' ) !== false ) {
			if ( thim_exist_in_array( $ignore_files, $file ) == true )
				continue;
			$content_file_options = $params;
			$content_file_options .= thim_file_get_contents( $less_folder . $file );
			$compiler = new lessc;
			$compiler->setFormatter( 'compressed' );
			$css .= $compiler->compile( $content_file_options );
			$content_file_options = "";
		}
	}
	return $css;
}

function thim_exist_in_array( $array, $string ) {
	if ( count( $array ) > 0 ) {
		foreach ( $array as $item ) {
			if ( $item == $string ) {
				return true;
			}
		}
	}
	return false;
}