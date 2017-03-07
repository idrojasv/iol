<?php
@session_start();

$max_time_limit = ini_get( 'max_execution_time' );
if ( intval( $max_time_limit ) < 120 ) {
	if ( ! ini_get( 'safe_mode' ) ) {// PHP < 5.4
		set_time_limit( 0 );
	} else {
		$response = array(
			'step'    => 'start',
			'next'    => 'error',
			'return'  => false,
			'message' => esc_html__( 'Please enable PHP\'s safe mode.', 'thim-framework' ),
		);
		echo json_encode( $response );
		exit();
	}

	if ( set_time_limit( 0 ) ) {
		set_time_limit( 0 );
	}
}

if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
	define( 'WP_LOAD_IMPORTERS', true );
}

$type = $_REQUEST['type'];
/**
 * GET DEMODATA PACKAGE FROM REMOTE SERVER
 */
$demodata_file = TP_THEME_THIM_DIR . 'inc' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'demo-data.php';
if ( ! is_file( $demodata_file ) ) {
	$_SESSION['thimpress-demodata-dir'] = TP_THEME_THIM_DIR . 'inc/admin';
}

$demodata = isset( $_REQUEST['demodata'] ) ? $_REQUEST['demodata'] : '';

if ( $type == 'download' ) {
	ob_start();
	$demodata_file = TP_THEME_THIM_DIR . 'inc' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'demo-data.php';
	require $demodata_file;
	if ( isset( $demo_datas[ $demodata ] ) ) {
		$package_url = isset( $demo_datas[ $demodata ]['package_url'] ) ? $demo_datas[ $demodata ]['package_url'] : '';
		$data_dir    = isset( $demo_datas[ $demodata ]['data_dir'] ) ? $demo_datas[ $demodata ]['data_dir'] : '';
		if ( ! $package_url && $data_dir ) {
			if ( is_dir( $data_dir ) ) {
				WP_Filesystem();
				$upload_dir   = wp_upload_dir();
				$demodata_dir = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'tp-datademo' . DIRECTORY_SEPARATOR . $demodata;
				if ( is_dir( $demodata_dir ) ) {
					tp_deleteDirectory( $demodata_dir );
				}
				if ( ! wp_mkdir_p( $demodata_dir ) ) {
					$response = array(
						'step'    => 'download',
						'next'    => 'error',
						'return'  => false,
						'message' => sprintf( esc_html__( 'Cannot create directory "%"', 'thim-framework' ), $demodata_dir ),
					);
					echo json_encode( $response );
					exit();
				}
				$res_copy_dir = copy_dir( $data_dir, $demodata_dir );
				if ( copy_dir( $data_dir, $demodata_dir ) ) {
					$_SESSION['thimpress-demodata-dir'] = $demodata_dir;
				} else {
					$_SESSION['thimpress-demodata-dir'] = $data_dir;
				}
				// remove cache directory before run import
				$data_cache_dir = $_SESSION['thimpress-demodata-dir'] . '/data/cache';
				if ( is_dir( $data_cache_dir ) ) {
					tp_deleteDirectory( $data_cache_dir ); // create cache directory
				}

				$wooimport_file = $_SESSION['thimpress-demodata-dir'] . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'woocommerce' . DIRECTORY_SEPARATOR . 'setting.txt';
				$next_step      = is_file( $wooimport_file ) ? 'woo_setting' : 'core';
				$log            = ob_get_clean();
				$response       = array(
					'step'    => 'download',
					'next'    => $next_step,
					'return'  => true,
					'message' => esc_html__( 'Demo data is already downloaded', 'thim-framework' ),
					'log'     => $log,
				);
				echo json_encode( $response );
				exit();
			} else {
				$log      = ob_get_clean();
				$response = array(
					'step'    => 'download',
					'next'    => 'error',
					'return'  => false,
					'message' => esc_html__( 'Demo data directory not found', 'thim-framework' ),
					'log'     => $log,
				);
				echo json_encode( $response );
				exit();
			}
			exit();
		}
		$filename     = pathinfo( $package_url, PATHINFO_BASENAME );
		$file_content = file_get_contents( $package_url );
		if ( ! $file_content ) {
			$response = array(
				'step'    => 'download',
				'next'    => 'error',
				'return'  => false,
				'message' => sprintf( esc_html__( 'Cannot get demo data package from %', 'thim-framework' ), $package_url )
			);
			echo json_encode( $response );
			exit();
		}

		$upload_dir                                = wp_upload_dir();
		$demodata_dir                              = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'tp-datademo' . DIRECTORY_SEPARATOR . $demodata;
		$_SESSION['thimpress-demodata-dir']        = $demodata_dir;
		$_SESSION['thimpress-demodata-file']       = $filename;
		$_SESSION['thimpress-demodata-downloaded'] = false;

		if ( ! wp_mkdir_p( $demodata_dir ) ) {
			$response = array(
				'step'    => 'download',
				'next'    => 'error',
				'return'  => false,
				'message' => sprintf( esc_html__( 'Cannot create directory "%"', 'thim-framework' ), $demodata_dir ),
			);
			echo json_encode( $response );
			exit();
		}

		if ( ! file_put_contents( $demodata_dir . DIRECTORY_SEPARATOR . $filename, $file_content ) ) {
			$response = array(
				'step'    => 'download',
				'next'    => 'error',
				'return'  => false,
				'message' => sprintf( esc_html__( 'Cannot store file "%s"', 'thim-framework' ), $demodata_dir . DIRECTORY_SEPARATOR . $filename ),
			);
			echo json_encode( $response );
			exit();
		}
		$log                                       = ob_end_clean();
		$_SESSION['thimpress-demodata-downloaded'] = true;
		$response                                  = array(
			'step'    => 'download',
			'next'    => 'extract',
			'return'  => true,
			'message' => sprintf( esc_html__( 'Download demo data package success "%s"', 'thim-framework' ), $demodata_dir . DIRECTORY_SEPARATOR . $filename ),
			'log'     => $log,
		);
		echo json_encode( $response );
		exit();
	}

} elseif ( $type == 'extract' ) {
	ob_start();
	$file_zip  = preg_replace( '/[\\/]/i', DIRECTORY_SEPARATOR, $_SESSION['thimpress-demodata-dir'] . DIRECTORY_SEPARATOR . $_SESSION['thimpress-demodata-file'] );
	$unzip_dir = dirname( $file_zip );
	WP_Filesystem();
	$unzipfile = unzip_file( $file_zip, $unzip_dir );
	if ( $unzipfile === true ) {
		$wooimport_file = $unzip_dir . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'woocommerce' . DIRECTORY_SEPARATOR . 'setting.txt';
		$next_step      = is_file( $wooimport_file ) ? 'woo_setting' : 'core';
		$log            = ob_get_clean();
		$response       = array(
			'step'    => 'extract',
			'next'    => $next_step,
			'return'  => true,
			'message' => sprintf( esc_html__( 'Extract demo data package to "%s" success', 'thim-framework' ), $unzip_dir ),
			'log'     => $log,
		);
		echo json_encode( $response );
		exit();
	} else {
		$log      = ob_get_clean();
		$response = array(
			'step'    => 'extract',
			'next'    => 'error',
			'return'  => false,
			'message' => esc_html__( 'There was an error unzipping demo data file.', 'thim-framework' ),
			'log'     => $log,
		);
		echo json_encode( $response );
		exit();
	}
}

if ( ! is_file( $demodata_file ) ) {
	$_SESSION['thimpress-demodata-dir'] = TP_THEME_THIM_DIR . 'inc/admin';
}

// check cache directory
$data_cache_dir = $_SESSION['thimpress-demodata-dir'] . '/data/cache';
if ( ! is_dir( $data_cache_dir ) ) {
	wp_mkdir_p( $data_cache_dir ); // create cache directory 
}

define( 'THEME_NAME', 'thim' );

define( 'POST_COUNT', $_SESSION['thimpress-demodata-dir'] . '/data/cache/count.txt' );
define( 'POST_COUNT_TEST', $_SESSION['thimpress-demodata-dir'] . '/data/cache/count_test.txt' );
define( 'MENU_MAPPING', $_SESSION['thimpress-demodata-dir'] . '/data/cache/menus.txt' );
define( 'MENU_ITEM_ORPHANS', $_SESSION['thimpress-demodata-dir'] . '/data/cache/menu_item_orphans.txt' );
define( 'PROCESS_TERM', $_SESSION['thimpress-demodata-dir'] . '/data/cache/process_term.txt' );
define( 'PROCESS_POSTS', $_SESSION['thimpress-demodata-dir'] . '/data/cache/process_posts.txt' );
define( 'MENU_MISSING', $_SESSION['thimpress-demodata-dir'] . '/data/cache/menu_missing.txt' );
define( 'URL_REMAP', $_SESSION['thimpress-demodata-dir'] . '/data/cache/url_remap.txt' );
define( 'POST_ORPHANS', $_SESSION['thimpress-demodata-dir'] . '/data/cache/post_orphans.txt' );
define( 'FEATURE_IMAGES', $_SESSION['thimpress-demodata-dir'] . '/data/cache/feature_images.txt' );
define( 'REV_IMPORT', $_SESSION['thimpress-demodata-dir'] . '/data/cache/rev.txt' );
define( 'EXIST_REV_IMPORT', $_SESSION['thimpress-demodata-dir'] . '/data/revslider/' );

define( 'MENU_CONFIG', $_SESSION['thimpress-demodata-dir'] . '/data/menus.txt' );
define( 'MENU_READING_CONFIG', $_SESSION['thimpress-demodata-dir'] . '/data/menu_reading.txt' );

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';
if ( file_exists( ABSPATH . 'wp-content/plugins/revslider/revslider_admin.php' ) && class_exists( 'UniteBaseAdminClassRev' ) ) {
	require_once( ABSPATH . 'wp-content/plugins/revslider/revslider_admin.php' );
}
$tp_importerError   = false;
$import_filepath    = $_SESSION['thimpress-demodata-dir'] . '/data/demodata.xml';
$import_settingpath = $_SESSION['thimpress-demodata-dir'] . '/data/setting.json';
$import_woo_setting = $_SESSION['thimpress-demodata-dir'] . '/data/woocommerce/setting.txt';
$widgets_json       = $_SESSION['thimpress-demodata-dir'] . '/data/widget/widget_data.json'; // widgets data file
//check if wp_importer, the base importer class is available, otherwise include it
if ( ! class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) ) {
		require_once( $class_wp_importer );
	} else {
		$tp_importerError = true;
	}
}

//check if the wp import class is available, this class handles the wordpress XML files. If not include it
//make sure to exclude the init function at the end of the file in kriesi_importer
if ( ! class_exists( 'WP_Import' ) ) {
	$class_wp_import = TP_FRAMEWORK_LIBS_DIR . 'import/inc/import/wordpress-importer.php';
	if ( file_exists( $class_wp_import ) ) {
		require_once( $class_wp_import );
	} else {
		$tp_importerError = true;
	}
}

try {
	if ( $tp_importerError !== false ) {
		esc_html_e( 'The Auto importing script could not be loaded. please use the wordpress importer and import the XML file that is located in your themes folder manually.', 'thim-framework' );
	} else {
		if ( class_exists( 'WP_Import' ) ) {
			include_once( TP_FRAMEWORK_LIBS_DIR . 'import/inc/import/tp-import-class.php' );
		}

		if ( ! is_file( $import_filepath ) ) {
			$demo_data_dir = TP_THEME_THIM_DIR . 'inc' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $demodata . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
			$response      = array(
				'step'    => 'check_demo_data_files',
				'next'    => 'error',
				'return'  => false,
				'message' => esc_html__( 'The XML file containing the demo content is not available or could not be read in <pre>' . $demo_data_dir . '</pre><br/> You might want to try to set the file permission to chmod 777.<br/>If this doesn\'t work please use the wordpress importer and import the XML file (should be located in your themes folder: demodata.xml) manually <a href="/wp-admin/import.php">here.</a>', 'thim-framework' ),
				'logs'    => ent2ncr( $import_filepath ) . esc_html__( ' not found', 'thim-framework' ),
			);
			echo json_encode( $response );
			exit();
		} else {
			if ( ! isset( $custom_export ) ) {

				$wp_import = new ob_wp_import();
				$type      = $_REQUEST['type'];

				ob_start();
				switch ( trim( $type ) ) {
					case 'woo_setting':
						if ( is_file( $import_woo_setting ) ) {
							if ( ! $wp_import->import_woosetting( $import_woo_setting ) ) {
								ob_end_clean();
								echo ent2ncr( $return->get_error_message() );

								return;
							}
						}
						$log      = ob_get_clean();
						$response = array(
							'step'    => 'woo_setting',
							'next'    => 'widgets',
							'return'  => true,
							'message' => esc_html__( 'Import WooCommerce settings success', 'thim-framework' ),
							'logs'    => $log,
						);
						echo json_encode( $response );

						break;
					case 'core':
						$wp_import->fetch_attachments = true;
						if ( $wp_import->import( $import_filepath ) == 0 ) {
							$log      = ob_get_clean();
							$response = array(
								'step'    => 'core',
								'next'    => 'core',
								'return'  => true,
								'message' => '',
								'logs'    => $log,
							);
							echo json_encode( $response );

							return;
						}
						$log      = ob_get_clean();
						$response = array(
							'step'    => 'core',
							'next'    => 'setting',
							'return'  => true,
							'message' => esc_html__( 'Import Post, Pages, Terms, Custom Post Type, etc success', 'thim-framework' ),
							'logs'    => $log,
						);
						echo json_encode( $response );

						break;

					case 'menus':
						$next_step = is_dir( EXIST_REV_IMPORT ) ? 'slider' : 'done';
						if ( ! $wp_import->set_menus() ) {
							$log      = ob_get_clean();
							$response = array(
								'step'    => 'menus',
								'next'    => 'error',
								'return'  => false,
								'message' => esc_html__( 'Error on importing menus', 'thim-framework' ),
								'logs'    => $log,
							);
							echo json_encode( $response );
						} else {
							$log      = ob_get_clean();
							$response = array(
								'step'    => 'menus',
								'next'    => $next_step,
								'return'  => true,
								'message' => esc_html__( 'Import menus success', 'thim-framework' ),
								'logs'    => $log,
							);
							echo json_encode( $response );
						}
						break;

					case 'widgets':
						$widgets_json = $_SESSION['thimpress-demodata-dir'] . '/data/widget/widget_data.json'; // widgets data file

						$widgets_json = file_get_contents( $widgets_json );

						if ( ! $wp_import->import_widgets( $widgets_json ) ) {
							$log      = ob_get_clean();
							$response = array(
								'step'    => 'widgets',
								'next'    => 'error',
								'return'  => false,
								'message' => esc_html__( 'Error on importing widgets', 'thim-framework' ),
								'logs'    => $log,
							);
							echo json_encode( $response );
						} else {
							$log      = ob_get_clean();
							$response = array(
								'step'    => 'widgets',
								'next'    => 'core',
								'return'  => true,
								'message' => esc_html__( 'Importing widgets success', 'thim-framework' ),
								'logs'    => $log,
							);
							echo json_encode( $response );
						}
						break;
					case 'setting':
						if ( ! is_file( $import_settingpath ) ) {
							echo 'File not found "' . $import_settingpath . '"';
						}
						if ( ! $wp_import->set_options( $import_settingpath ) ) {
							$log      = ob_get_clean();
							$response = array(
								'step'    => 'setting',
								'next'    => 'error',
								'return'  => false,
								'message' => esc_html__( 'Error on import setting', 'thim-framework' ),
								'logs'    => $log,
							);
							echo json_encode( $response );
						} else {
							$wp_import->updateTaxCount();
							$log      = ob_get_clean();
							$response = array(
								'step'    => 'setting',
								'next'    => 'menus',
								'return'  => true,
								'message' => esc_html__( 'Import setting success', 'thim-framework' ),
								'logs'    => $log,
							);
							echo json_encode( $response );
						}
						break;
					case 'mapping':
						require_once( TP_FRAMEWORK_LIBS_DIR . 'import/inc/import/class-tp-import-mapping.php' );
						$tp_mapping = new TP_Import_Mapping();
						$tp_mapping->mapping();

						$response = array(
							'step'    => 'mapping',
							'next'    => 'done',
							'return'  => true,
							'message' => '',
							'logs'    => null,
						);
						echo json_encode( $response );
						break;
					case 'slider':
						$check_slider = $wp_import->import_revslider();
						if ( ! $check_slider ) {
							$log = ob_get_clean();
							if ( class_exists( 'RevSlider' ) ) {
								$response = array(
									'step'    => 'slider',
									'next'    => 'mapping',
									'return'  => false,
									'message' => esc_html__( '<p>Import finish without revolution sliders. You can import manual, please view <a href="http://thimpress.com/knowledge-base/import-revolution-sliders/#content" target="_blank">How to import revolution sliders</a>.</p>', 'thim-framework' ),
									'logs'    => $log,
								);
								echo json_encode( $response );
							} else {
								$response = array(
									'step'    => 'slider',
									'next'    => 'mapping',
									'return'  => false,
									'message' => esc_html__( 'Slide is not imported', 'thim-framework' ),
									'logs'    => $log,
								);
								echo json_encode( $response );
							}
						} elseif ( $check_slider == 1 ) {
							$log      = ob_get_clean();
							$response = array(
								'step'    => 'slider',
								'next'    => 'slider',
								'return'  => true,
								'message' => '',
								'logs'    => $log,
							);
							echo json_encode( $response );
						} else {
							$log      = ob_get_clean();
							$response = array(
								'step'    => 'slider',
								'next'    => 'mapping',
								'return'  => true,
								'message' => esc_html__( 'Import demo data success', 'thim-framework' ),
								'logs'    => $log,
							);
							echo json_encode( $response );
						}
						break;
				}
			}
		}
	}

} catch ( Exception $e ) {
	$response = array(
		'step'    => 'error',
		'next'    => 'error',
		'return'  => false,
		'message' => $e->getMessage(),
		'logs'    => ob_get_clean(),
	);
	echo json_encode( $response );
	exit();
}
