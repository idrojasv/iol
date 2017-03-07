<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

define( 'TP_RECOMMEND_MEMORY_LIMIT', apply_filters( 'TP_RECOMMEND_MEMORY_LIMIT', 128 ) );
define( 'TP_RECOMMEND_EXECUTION_TIME', - 1 );
define( 'TP_RECOMMEND_PHP_VERSION', apply_filters( 'TP_RECOMMEND_PHP_VERSION', '5.4.0' ) );

/**
 * Import Demo page content
 */
function tp_page_content() {
	/**
	 * include file list of demo data
	 */
	$demo_data_file_path = TP_THEME_THIM_DIR . 'inc' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'demo-data.php';
	$demo_data_dir_path  = TP_THEME_THIM_DIR . 'inc' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'data';
	if ( is_file( $demo_data_file_path ) ) {
		require $demo_data_file_path;
	} else {
		// create demo data
		$demo_datas = array();
	}
	$demo_data_file = TP_THEME_THIM_DIR . 'inc' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'demo-data.php';
	if ( is_file( $demo_data_file ) ) {
		require TP_THEME_THIM_DIR . 'inc' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'demo-data.php';
	}

	wp_enqueue_style( 'tp-import', TP_FRAMEWORK_LIBS_URI . 'import/css/backend.css', array(), TP_FRAMEWORK_VERSION );
	wp_enqueue_script( 'wp-pointer' );
	wp_enqueue_script( 'tp-import', TP_FRAMEWORK_LIBS_URI . 'import/js/tp-import.js', array(
		'jquery',
		'wp-pointer'
	), TP_FRAMEWORK_VERSION, true );
	wp_enqueue_style( 'wp-pointer' );

	$translation_string = array(
		'welcome' => esc_html__( 'Welcome to Demo Importer!', 'thim-framework' ),
		'content' => esc_html__( 'In order to Import a demo, please follow the instruction from messages on the top of this screen and reload it then.', 'thim-framework' ),
	);

	$notification_string = array(
		'err' => esc_html__( 'Sorry to let you know that your server system can not work properly with Demo Importer due to some configs. Please request some support.', 'thim-framework' )
	);

	wp_localize_script( 'tp-import', 'tp_pointer', $translation_string );
	wp_localize_script( 'tp-import', 'tp_notifications', $notification_string );

	$memory_limit       = ini_get( 'memory_limit' );
	$max_execution_time = ini_get( 'max_execution_time' );

	$all_ini_config = ini_get_all();
	?>
	<script>
		console.info(<?php echo json_encode( $all_ini_config ); ?>);
	</script>
	<?php
	$arr_memory_limit       = $all_ini_config['memory_limit'];
	$arr_max_execution_time = $all_ini_config['max_execution_time'];

	if ( array_key_exists( 'local_value', $arr_memory_limit ) ) {
		$memory_limit = $arr_memory_limit['local_value'];
	}

	if ( array_key_exists( 'local_value', $arr_max_execution_time ) ) {
		$max_execution_time = $arr_max_execution_time['local_value'];
	}

	$is_ok = true;
	if ( intval( $memory_limit ) < TP_RECOMMEND_MEMORY_LIMIT || intval( $max_execution_time ) < TP_RECOMMEND_EXECUTION_TIME || version_compare( phpversion(), TP_RECOMMEND_PHP_VERSION ) < 0 ) {
		$is_ok = false;
	}

	?>

	<?php add_thickbox(); ?>
	<div class="wrap">
		<h1><?php esc_html_e( 'ThimPress Demo Importer', 'tp' ); ?></h1>

		<?php $count_dismiss_warring_overwritten = intval( get_option( 'tp_importer_warring_overwritten', 0 ) );
		if ( $count_dismiss_warring_overwritten < 2 ) {
			?>
			<div class="update-nag tp_notification">
				<p>
					<?php _e( '<strong>Warning:</strong> If you have already used this feature before and you want to try it again, your content may be duplicated. Please consider resetting your database back to defaults with <a href="https://wordpress.org/plugins/wordpress-reset/">this plugin</a>.', 'thim-framework' ); ?>
				</p>
			</div>
			<?php
		}

		/**
		 * PHP version
		 */
		if ( version_compare( phpversion(), TP_RECOMMEND_PHP_VERSION ) < 0 ) { ?>
			<div class="error tp_notification">
				<p>
					<?php
					printf(
						__( '<strong>Important:</strong> We found out your system is using PHP version %1$s and it can cause the importer. Please consider upgrading to version %2$s or higher.', 'thim-framework' ),
						phpversion(),
						TP_RECOMMEND_PHP_VERSION
					);
					?>
				</p>
			</div>
			<?php
		}

		/**
		 * Memory limit and Maximum execution time
		 */
		if ( ! $is_ok ) : ?>
			<?php if ( intval( $memory_limit ) < TP_RECOMMEND_MEMORY_LIMIT ) { ?>
				<div class="error tp_notification">
					<div class="memory_limit">
						<p>
							<?php
							printf(
								__( '<strong>Important:</strong> The Importer requires memory limit of your system >= %1$sMB. Please follow <a href="%2$s" target="_blank">these guidelines</a> to improve it.', 'thim-framework' ),
								TP_RECOMMEND_MEMORY_LIMIT,
								'//thimpress.com/?p=52957'
							);
							?>
						</p>
					</div>
				</div>
				<?php
			} ?>
		<?php endif; ?>

		<div class="thim-demo-browser theme-browser rendered">
			<div class="themes">
				<?php
				$attr_button_import = '';
				if ( ! $is_ok ) {
					$attr_button_import = 'data-disabled="true" data-title="' . esc_html__( 'You need to upgrade your system follow to the above messages.', 'thim-framework' ) . '"';
				}
				?>
				<?php
				$index = 0;
				foreach ( $demo_datas as $key => $item ) {
					$demo_url = '';
					if ( key_exists( 'demo_url', $item ) ) {
						$demo_url = $item['demo_url'];
					}

					?>
					<div class="theme" aria-describedby="<?php echo esc_attr( $key ); ?>">
						<div class="theme-screenshot">
							<img src="<?php echo esc_url( $item['thumbnail_url'] ); ?>" alt="">
						</div>

						<h2 class="theme-name" id="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $item['title'] ); ?></h2>
						<div class="theme-actions">
							<button class="button button-primary tp-btn-import" data-pointer="<?php echo esc_attr( 'wp-pointer-' . $index ); ?>" data-site="<?php echo esc_attr( $key ); ?>" <?php echo $attr_button_import; ?>><?php esc_html_e( 'Import', 'thim-framework' ); ?></button>

							<?php if ( $demo_url != '' ) { ?>
								<a class="button button-secondary" href="<?php echo esc_url( $demo_url ); ?>" target="_blank"><?php esc_html_e( 'Demo', 'thim-framework' ); ?></a>
							<?php } else { ?>
								<a class="button button-secondary" href="#" disabled="disabled"><?php esc_html_e( 'Demo', 'thim-framework' ); ?></a>
							<?php } ?>
						</div>

					</div>
					<?php
					$index ++;
				}
				?>
			</div>
			<br class="clear"></div>
	</div>

	<section class="tp-popup">
		<div class="container">
			<div class="wrapper-content">
				<button type="button" class="notice-dismiss tp-close-import-popup">
					<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'thim-framework' ); ?></span>
				</button>

				<h1><?php esc_html_e( 'Importing', 'tp' ); ?></h1>

				<div class="row">
					<div class="tp_progress_import">
						<p class="note"><?php esc_html_e( 'The import process can take about 10 minutes. Enjoy a cup of coffee while you wait for importing :)', 'thim-framework' ); ?></p>
						<div class="meter">
							<span style="width:0"></span>
							<p></p>
						</div>
					</div>

					<div class="tp_progress_error_message">
						<div class="tp-error">
							<h4><?php esc_html_e( 'Import failed!', 'thim-framework' ); ?></h4>
							<div class="content text_note tp_notification"></div>
						</div>
						<div class="log update-nag tp_notification">
							<h4><?php esc_html_e( 'Log', 'thim-framework' ); ?></h4>
							<div class="content text_note"></div>
						</div>
						<a class="button button-primary tp-support" href="//thimpress.com/forums/" target="_blank"><?php esc_html_e( 'Get support', 'thim-framework' ); ?></a>
						<a class="button button-secondary tp-visit-dashboard" href="<?php echo esc_url( get_admin_url() ); ?>"><?php esc_html_e( 'Dashboard', 'thim-framework' ); ?></a>
					</div>

					<div class="tp-complete">
						<h3 class=""><?php esc_html_e( 'Importing is successful!', 'tp' ); ?></h3>
						<div class="content-message"></div>
						<a class="button button-primary tp-visit-site" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'Visit site', 'thim-framework' ); ?></a>
						<a class="button button-secondary tp-visit-dashboard" href="<?php echo esc_url( get_admin_url() ); ?>"><?php esc_html_e( 'Dashboard', 'thim-framework' ); ?></a>
					</div>
					<br class="clear">
				</div>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Process front page displays settings importing
 *
 * @param $post_id
 * @param $key
 * @param $value
 */
function tp_import_front_page_displays_settings( $post_id, $key, $value ) {
	if ( in_array( $key, array(
		'thim_page_for_posts',
		'thim_page_on_front'
	) ) ) {
		$meta_value = get_post_meta( $post_id, $key, true );
		if ( $meta_value && $meta_value == $value ) {
			if ( $key == 'thim_page_for_posts' ) {
				update_option( 'page_for_posts', $post_id );
			} else {
				update_option( 'page_on_front', $post_id );
			}
		}
		update_option( 'show_on_front', 'page' );

	}

}

add_action( 'import_post_meta', 'tp_import_front_page_displays_settings', 10, 3 );

/**
 * Process menu location settings importing
 *
 * @param $menu_id
 * @param $key
 * @param $value
 */
function tp_import_menu_location_settings( $menu_id, $key, $value ) {
	if ( ( strpos( $key, 'thim_object_in_location_' ) !== false ) ) {
		$menu_locations = get_theme_mod( 'nav_menu_locations' );
		if ( ! $menu_locations ) {
			$menu_locations = array();
		}
		$new_locations = array_merge( $menu_locations, array( $value => $menu_id ) );
		set_theme_mod( 'nav_menu_locations', $new_locations );
	}
}

add_action( 'import_menu_item_meta', 'tp_import_menu_location_settings', 10, 3 );

function tp_admin_notice_goto_import_demo() {
	$page_slug = isset( $_GET['page'] ) ? $_GET['page'] : '';

	if ( $page_slug == 'thim-import-demo' ) {
		return;
	}
	?>

	<div id="tp-goto-import-page" class="updated settings-error notice is-dismissible">
		<p><strong>
				<?php
				printf(
					__( '<span><em><a href="%s">Demo Importer</a></em> is ready to use. <em style="color: red; cursor: pointer" class="tp_import_ignore">Ignore</em> notice if you have already used this feature before.</span>', 'thim-framework' ),
					esc_url( admin_url( 'tools.php?page=thim-import-demo' ) )
				);
				?>
			</strong></p>
	</div>
	<?php
}

add_action( 'admin_notices', 'tp_admin_notice_goto_import_demo' );

function tp_deleteDirectory( $dir ) {
	if ( ! file_exists( $dir ) ) {
		return true;
	}

	if ( ! is_dir( $dir ) ) {
		return unlink( $dir );
	}

	foreach ( scandir( $dir ) as $item ) {
		if ( $item == '.' || $item == '..' ) {
			continue;
		}

		if ( ! tp_deleteDirectory( $dir . DIRECTORY_SEPARATOR . $item ) ) {
			return false;
		}

	}

	return rmdir( $dir );
}