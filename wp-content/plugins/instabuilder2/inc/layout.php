<?php
add_action('wp', 'ib2_layout_hooks', 2);
function ib2_layout_hooks() {
$current_theme = wp_get_theme();
	if ( $current_theme == 'Thesis' ) {
		add_action('template_redirect', 'ib2_layout_settings', 2);
		add_action('template_redirect', 'ib2_layout_template_alt', 3);
	} else {
		add_action('template_redirect', 'ib2_layout_settings', 99);
		add_filter('template_include', 'ib2_layout_template');
	}
}

add_action('template_redirect', 'ib2_variant_setup', 1);
function ib2_variant_setup() {
	global $wpdb, $post;
	if ( !is_singular() || empty($post) )
		return;
	
	$GLOBALS['ib2_variant'] = ib2_get_default_variant($post->ID, true);
	if ( is_ib2_admin() && isset($_GET['variant']) ) {
		if ( ib2_variant_exists($post->ID, $_GET['variant']) ) {
			$GLOBALS['ib2_variant'] = $_GET['variant'];
		}
		return true;
	}
	
	if ( isset($_COOKIE['__ib2pgvar_' . $post->ID]) && $_COOKIE['__ib2pgvar_' . $post->ID] != '' ) {
		$saved_variant = $_COOKIE['__ib2pgvar_' . $post->ID];
		// we need to make sure this variant is not paused or deleted...
		if ( ib2_variant_exists($post->ID, $saved_variant) && !ib2_variant_paused($post->ID, $saved_variant) ) {
			$GLOBALS['ib2_variant'] = $_COOKIE['__ib2pgvar_' . $post->ID];
			return true;
		}
	}
	
	$variants = ib2_get_variants($post->ID);
	if ( $variants && count($variants) > 1 ) {
		// Let's try the new calculation
		$link = array();
		foreach( $variants as $var ) {
			if ( $var->weight <= 0 ) continue;

		    $link[] = array('variant' => $var->variant, 'weight' => $var->weight);
		}

		$percent_arr = array();
		if ( count($link) > 0 ) {
			foreach( $link as $k => $_l ) {
				$percent_arr = array_merge($percent_arr, array_fill(0, $_l['weight'], $k));
			}
			$random_key = $percent_arr[mt_rand(0,count($percent_arr)-1)];
			$GLOBALS['ib2_variant'] = $link[$random_key]['variant'];
		}

		// $last_var = get_post_meta($post->ID, 'ib2_last_var', true);
	
		// $end_var = end($variants);
		
		// $full = 0;

		// foreach ( $variants as $var ) {
		// 	// This variant has no weight, then skip...
		// 	if ( $var->weight <= 0 ) continue;
			
		// 	// This variant's has no quota left... then skip
		// 	if ( $var->quota >= $var->weight ) {
		// 		$full += 1;
		// 		continue;
		// 	}

		// 	// This variant has been visited by the previous visitor...
		// 	if ( is_array($last_var) && in_array($var->variant, $last_var) ) continue;
			
		// 	// If we still survive up to this point, then this variant that will be displayed...
		// 	$GLOBALS['ib2_variant'] = $var->variant;
		// 	$newquota = $var->quota + 1;
			
		// 	// Update this variant quota usage...
		// 	$wpdb->update("{$wpdb->prefix}ib2_variants", array('quota' => $newquota), array('post_id' => $post->ID, 'variant' => $var->variant) );
		// 	break;
		// }

		// // If the all variants have no quota... 
		// if ( $full == count($variants) ) {
		// 	// reset quota for the next usage...
		// 	foreach ( $variants as $var ) {
		// 		$newquota = ( $var->variant == 'a' ) ? 1 : 0;
		// 		$wpdb->update("{$wpdb->prefix}ib2_variants", array('quota' => $newquota), array('post_id' => $post->ID, 'variant' => $var->variant) );
		// 	}
		// }
		
		// if ( isset($end_var->variant) && $end_var->variant == $GLOBALS['ib2_variant'] ) {
		// 	delete_post_meta($post->ID, 'ib2_last_var');
		// } else {
		// 	if ( !is_array($last_var) )
		// 		$last_var = array();
		
		// 	if ( !in_array($GLOBALS['ib2_variant'], $last_var) ) {
		// 		$last_var[] = $GLOBALS['ib2_variant'];
		// 	}
			
		// 	update_post_meta($post->ID, 'ib2_last_var', $last_var);
		// }
	}
	
	@setcookie('__ib2pgvar_' . $post->ID, $GLOBALS['ib2_variant'], time()+60*60*24*365, '/');
	if ( SITECOOKIEPATH != '/' )
		@setcookie('__ib2pgvar_' . $post->ID, $GLOBALS['ib2_variant'], time()+60*60*24*365, SITECOOKIEPATH);
}

function ib2_layout_settings() {
	global $post;
	if ( !(is_single() || is_page()) || empty($post) )
		return;
	
	$license_key = ib2_license_key();
	if ( empty($license_key) )
		return;
	
	$meta = get_post_meta($post->ID, 'ib2_settings', true);
	$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
	if ( !$enable ) return;
	
	// remove unnecessary IB first version and IT hooks
	if ( has_action('wp_footer', 'opl_facebook_js') )
		remove_action('wp_footer', 'opl_facebook_js');
	if ( has_action('wp_head', 'intheme_editor_fonts') )
		remove_action('wp_head', 'intheme_editor_fonts');
	if ( has_action('wp_head', 'intheme_member_welcome_style') )
		remove_action('wp_head', 'intheme_member_welcome_style', 20);
	if ( has_action('wp_head', 'intheme_fbappid_var') )
		remove_action('wp_head', 'intheme_fbappid_var');
	if ( has_action('wp_head', 'intheme_global_custom_css') )
		remove_action('wp_head', 'intheme_global_custom_css', 100);
	if ( has_action('wp_head', 'external_dynamic_css') )
		remove_action('wp_head', 'external_dynamic_css', 98);
	if ( has_action('wp_head', 'intheme_note_hide') )
		remove_action('wp_head', 'intheme_note_hide');
	if ( has_action('wp_footer', 'opl_exit_redirect') )
		remove_action('wp_footer', 'opl_exit_redirect');
	if ( has_action('wp_footer', 'opl_widget_submit') )
		remove_action('wp_footer', 'opl_widget_submit');
	if ( has_action('wp_footer', 'opl_share_script') )
		remove_action('wp_footer', 'opl_share_script');
	if ( has_action('wp_footer', 'opl_regular_share') )
		remove_action('wp_footer', 'opl_regular_share');
	if ( has_action('wp_footer', 'opl_format_footernav') )
		remove_action('wp_footer', 'opl_format_footernav');
	if ( has_action('wp_footer', 'opl_smart_optin_cookie') )
		remove_action('wp_footer', 'opl_smart_optin_cookie');
	if ( has_action('wp_footer', 'opl_top_menu_pos') )
		remove_action('wp_footer', 'opl_top_menu_pos');
	if ( has_action('wp_footer', 'intheme_note_process') )
		remove_action('wp_footer', 'intheme_note_process');
	if ( has_action('wp_footer', 'opl_responsive_video_embed') )
		remove_action('wp_footer', 'opl_responsive_video_embed');
	if ( has_action('wp_footer', 'intheme_responsive_video_embed') )
		remove_action('wp_footer', 'intheme_responsive_video_embed');
	if ( has_action('wp_footer', 'intheme_child_menus') )
		remove_action('wp_footer', 'intheme_child_menus');
	if ( has_action('init', 'opl_head') )
		remove_action('init', 'opl_head');
	if ( has_action('wp_print_styles', 'opl_main_style') )
		remove_action('wp_print_styles', 'opl_main_style');
	if ( has_action('wp_enqueue_scripts', 'intheme_frontend_scripts') )
		remove_action('wp_enqueue_scripts', 'intheme_frontend_scripts');
		
	// Fix conflict with canvas
	remove_action( 'wp_head', 'woo_custom_styling' );
	remove_action( 'wp_head', 'woo_enqueue_custom_styling' );
	remove_action( 'woo_head','woo_slider', 10 );
	remove_action( 'woo_header_after','woo_nav', 10 );
	remove_action( 'woo_head', 'woo_conditionals', 10 );
	remove_action( 'wp_head', 'woo_author', 10 );
	remove_action( 'wp_head', 'woo_google_webfonts', 10 );	
	remove_action( 'wp_enqueue_scripts', 'woo_load_frontend_css', 20 );
	remove_action( 'wp_head', 'woo_load_site_width_css', 9 );
	remove_action( 'wp_head', 'woo_load_site_width_css', 10 );
	
	// Fix conflic with Weaver II theme CSS
	remove_action('wp_head', 'weaverii_wp_head');
	
	if ( isset($_GET['ib2mode']) ) {
		if ( $_GET['ib2mode'] == 'save_html' || $_GET['ib2mode'] == 'save_html_rich' ) {

			$folder = $post->ID . '_' . time();
			
			show_admin_bar( false );
			add_filter('wp_head', 'ib2_remove_adminbar_style', 99);
			
			ob_start();
	
			require_once(IB2_INC . 'template.php');
			
			$content = ob_get_contents();
			
			ob_end_clean();
			
			require_once(IB2_INC . 'simple_html_dom.php');
			
			$html = new simple_html_dom();
			$html->load($content);
			$html->set_callback('ib2_remove_wpadminbar');
			
			if ( $_GET['ib2mode'] == 'save_html_rich' ) {
				if ( !is_writable(IB2_PATH . 'cache/') ) {
					wp_die("ERROR: " . IB2_PATH . "cache/ is NOT writable.");
				}
				
				$ds = DIRECTORY_SEPARATOR;
				$folder_path = realpath(IB2_PATH . 'cache') .$ds. $folder;
				if ( !file_exists($folder_path) )
					wp_mkdir_p($folder_path);
				
				$junk_files = array();
				$new_files = array();
				
				// Find all images
				foreach( $html->find('img') as $element ) {
					$url = $element->src;
					
					if ( substr($url, 0, 11) == '/wp-content' )
						$url = get_bloginfo('wpurl') . $url;
					
					$filename = basename($url);
	
					$image_path = $folder_path .$ds. 'images';
					if ( !file_exists($image_path) )
						wp_mkdir_p($image_path);
					
					$junk_files[] = $image_path;
					
					$file_path = $image_path .$ds. $filename;
			
					ib2_download_file($url, $file_path);
					
					$new_files[] = $file_path;
					$junk_files[] = $file_path;
				}
				
				$html->set_callback('ib2_replace_image_url');
				
				$new_file = $folder_path .$ds. "index.html";
				$fh = fopen($new_file, "w") or die("Unable to open file!");
				
				$html = str_replace('url(&quot;/wp-content', 'url(&quot;' . get_bloginfo('wpurl') . '/wp-content', $html);
				$html = str_replace('url("/wp-content', 'url("' . get_bloginfo('wpurl') . '/wp-content', $html);
				
				fwrite($fh, $html);
				fclose($fh);
				
				$new_files[] = $new_file;
				$junk_files[] = $new_file;
				
				$zip_file = IB2_PATH . "cache/{$folder}.zip";
				
				$new_zip = ib2_create_zip($new_files, $zip_file, true);
				if ( !$new_zip ) {
					if ( !class_exists('ZipArchive') )
						wp_die('ERROR: PECL Zip is NOT installed in your server. Please contact your host support/admin.');
					else
						wp_die('ERROR: Failed to create Zip file.');
				}
				
				foreach ( $junk_files as $f ) {
					@unlink($f);
				}

				header( 'Content-type: application/zip' );
				header( sprintf( 'Content-Disposition: attachment; filename="%s"', $folder  . '.zip' ) );
				readfile( $zip_file );
				
				@unlink( $zip_file );
				
				exit;
	
			} else {
				if ( isset($_GET['export']) && $_GET['export'] == 'true' ) {
					$page_type = $_GET['type'];
					$filepath = IB2_PATH . "cache/ib20_{$post->ID}-{$page_type}.html";
					$myfile = fopen($filepath, "w") or die("Unable to open file!");
					fwrite($myfile, $html);
					fclose($myfile);
				
					echo $filepath;
				} else {
					header("Content-disposition: attachment; filename=ib20_{$post->ID}.html");
					header("Content-type: text/html");
					echo $html;
				}
			}
			exit;
		}
	}
}

function ib2_layout_template( $template ) {
	global $post;
	if ( !is_singular() || empty($post) )
		return $template;
	
	if ( post_password_required() )
		return $template;

	$license_key = ib2_license_key();
	if ( empty($license_key) )
		return $template;
		
	$meta = get_post_meta($post->ID, 'ib2_settings', true);
	$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
	if ( !$enable || !file_exists(IB2_INC . 'template.php') ) return $template;
	
	$template = IB2_INC . 'template.php'; // override default wp theme template with IB 2.0
	return $template;
}

function ib2_layout_template_alt() {
	global $post;
	if ( !is_singular() || empty($post) )
		return;
	
	if ( post_password_required() )
		return $template;
	
	$license_key = ib2_license_key();
	if ( empty($license_key) )
		return;
		
	$meta = get_post_meta($post->ID, 'ib2_settings', true);
	$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
	if ( !$enable || !file_exists(IB2_INC . 'template.php') ) return $template;
	
	include_once ( IB2_INC . 'template.php' ); // override default wp theme template with IB 2.0

	exit;
}

function ib2_download_file( $url, $file_path ) {
	if ( !function_exists('curl_init') )
		wp_die("ERROR: cURL is NOT installed. Please contact your host support/admin to install cURL.");
		 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, false);
    curl_setopt($ch, CURLOPT_REFERER, get_bloginfo('url'));
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $result = curl_exec($ch);
	
    curl_close($ch);
	
	$fp = @fopen($file_path, 'w');
	
	if ( !$fp ) die('cannot save file');
	
	fwrite($fp, $result);
	fclose($fp);

}

function ib2_remove_adminbar_style() {
	echo '<style type="text/css" media="screen">
	html { margin-top: 0px !important; }
	* html body { margin-top: 0px !important; }
	</style>';
}

function ib2_remove_wpadminbar( $element ) {
	if ( $element->id == 'wpadminbar' )
		$element->outertext = '';
		
	if ( $element->id == 'admin-bar-css' )
		$element->outertext = '';
	
	if ( $element->id == 'dashicons-css' )
		$element->outertext = '';
		
	$admin_bar = includes_url('js/admin-bar.min.js') . '?ver=4.0';
	if ( $element->src == $admin_bar )
		$element->outertext = '';
}

function ib2_replace_image_url( $element ) {
	if ( $element->tag == 'img' ) {
		$src = $element->src;
		if ( substr($src, 0, 11) == '/wp-content' )
			$src = get_bloginfo('wpurl') . $src;
		$filename = basename($src);
		$element->src = 'images/' . $filename;
	}
	
	if ( $element->tag == 'link' ) {
		if ( isset($element->href) ) {
			$href = $element->href;
			if ( substr($href, 0, 11) == '/wp-content' ) {
				$element->href = get_bloginfo('wpurl') . $href;
			}
		}
	}
	
	if ( $element->tag == 'script' ) {
		if ( isset($element->src) ) {
			$src = $element->src;
			if ( substr($src, 0, 11) == '/wp-content' ) {
				$element->src = get_bloginfo('wpurl') . $src;
			}
		}
	}
}

function ib2_layout_framework() {
	global $post, $ib2_variant;
	
	if ( !isset($ib2_variant) ) {
		$ib2_variant = 'a';
		if ( is_ib2_admin() && isset($_GET['variant']) ) {
			$ib2_variant = $_GET['variant'];
		}
	}

	$options = get_option('ib2_options');
	$meta = get_post_meta($post->ID, 'ib2_settings', true);
	$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
	if ( !$enable ) return '';
	
	$variant = 'variation' . $ib2_variant;
	$data = ( isset($meta[$variant]) ) ? $meta[$variant] : array();
	$jvzoo_id = ( isset($_GET['aid']) ) ? $_GET['aid'] : '';
	
	// Clean up old history data ...
	if ( $post )
		delete_post_meta($post->ID, 'ib2_history');
		
	require_once( IB2_INC . 'simple_html_dom.php');
	
	$content = ( is_array($meta) && !empty($data['content']) ) ? stripslashes($data['content']) : '';
	$content = str_replace('{%IB2_PLUGIN_DIR%}', IB2_URL, $content);
	$content = str_replace('otopelay', 'autoplay', $content);
	$content = str_replace('ib2-video-responsive-class', 'embed-responsive embed-responsive-16by9', $content);
	$content = str_replace('{%sc_open%}', '[', $content);
	$content = str_replace('{%sc_close%}', ']', $content);
	$content = str_replace('{{{jvzooid}}}', $jvzoo_id, $content);
	$content = do_shortcode($content);

	$slider_content = ( is_array($meta) && !empty($data['slider_content']) ) ? stripslashes($data['slider_content']) : '';
	$slider_content = ( is_base64($slider_content) ) ? base64_decode($slider_content) : $slider_content;
	$slider_content = str_replace('{%IB2_PLUGIN_DIR%}', IB2_URL, $slider_content);
	$slider_content = str_replace('otopelay', 'autoplay', $slider_content);
	$slider_content = str_replace('ib2-video-responsive-class', 'embed-responsive embed-responsive-16by9', $slider_content);
	$slider_content = str_replace('{%sc_open%}', '[', $slider_content);
	$slider_content = str_replace('{%sc_close%}', ']', $slider_content);
	$slider_content = str_replace('{{{jvzooid}}}', $jvzoo_id, $slider_content);
	$slider_content = do_shortcode($slider_content);

	$gfonts_url = ib2_googlefonts_url();
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title('|', true, 'right'); ?></title>

<?php 
$meta_desc = ( is_array($meta) && isset($data['meta_desc']) ) ? esc_textarea($data['meta_desc']) : '';
if ( !empty($meta_desc) ) {
	echo '<meta name="description" content="' . str_replace(array("\r", "\n"), ' ', esc_attr($meta_desc)) . '">' . "\n";
}
$meta_keys = ( is_array($meta) && isset($data['meta_keys']) ) ? esc_attr($data['meta_keys']) : '';
if ( !empty($meta_keys) ) {
	echo '<meta name="keywords" content="' . str_replace(array("\r", "\n"), ' ', esc_attr($meta_keys)) . '">' . "\n";
}
$seoindex = ( is_array($data) && isset($data['noindex']) && $data['noindex'] == 'true' ) ? 'noindex' : 'index';
$seofollow = ( is_array($data) && isset($data['nofollow']) && $data['nofollow'] == 'true' ) ? 'nofollow' : 'follow';
$seorobots = array($seoindex, $seofollow);
if ( is_array($data) && isset($data['noodp']) && $data['noodp'] == 'true' )
	$seorobots[] = 'noodp';
if ( is_array($data) && isset($data['noydir']) && $data['noydir'] == 'true' )
	$seorobots[] = 'noydir';
if ( is_array($data) && isset($data['noarchive']) && $data['noarchive'] == 'true' )
	$seorobots[] = 'noarchive';
	
echo '<meta name="robots" content="' . implode(",", $seorobots) . '">' . "\n";

$favicon = ( isset($data['favicon']) ) ? esc_url(stripslashes($data['favicon'])) : '';

if ( !empty($favicon) ) : 
	echo "\n" . '<link rel="shortcut icon" href="' . $favicon . '">' ."\n";
endif;
?>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<link href="<?php echo $gfonts_url; ?>" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	var ib2ajaxurl = '<?php echo esc_url_raw(add_query_arg('ib2script', 'ajax', get_permalink($post->ID))); ?>';
</script>
<?php wp_head(); ?>

<?php
$background_video = ( isset($data['background_video']) ? esc_url(stripslashes($data['background_video'])) : '' );
$mute_pos = ( isset($data['background_video_mute']) && $data['background_video_mute'] == 1 ) ? 'left bottom' : 'left top';
if ( !empty($background_video) ) { ?>
<style type="text/css">
#entire_wrapper {
	position:relative;
	z-index: 99;
}
ul#ib2-bgvid-control {
	margin:0;
	padding:0;
	list-style:0;
	position:absolute;
	z-index:999;
	bottom:30px;
	right:30px;
}
ul#ib2-bgvid-control li {
	float:left;
	margin:0;
	padding:0;
	list-style-type: none;
}
ul#ib2-bgvid-control li a {
	display:block;
	width:32px;
	height:32px;
	text-decoration:none;	
}
li.ib2-ytube-play a {
	background:url('<?php echo IB2_IMG; ?>play.png') no-repeat;	
}
li.ib2-ytube-pause a {
	background:url('<?php echo IB2_IMG; ?>pause.png') no-repeat;	
}
li.ib2-ytube-volume-up a {
	background:url('<?php echo IB2_IMG; ?>volume-up.png') no-repeat;	
}
li.ib2-ytube-volume-down a {
	background:url('<?php echo IB2_IMG; ?>volume-down.png') no-repeat;	
}
li.ib2-ytube-mute a {
	background:url('<?php echo IB2_IMG; ?>speaker.png') <?php echo $mute_pos; ?> no-repeat;
}
</style>
<script type="text/javascript">
var ib2_videobg = 0, ytWidth, ytHeight, ytLoop = false, ytMute = true;
(function ($, window) {
	var mute = <?php echo ( isset($data['background_video_mute']) && $data['background_video_mute'] == 0 ) ? 'false' : 'true'; ?>,
	loop = <?php echo ( isset($data['background_video_loop']) && $data['background_video_loop'] == 0 ) ? 'false' : 'true'; ?>, 
	ratio = 0.5625;
	
	ytLoop = loop;
	ytMute = mute;
	ytWidth = $(window).width();
	ytHeight = Math.ceil(ytWidth / ratio);

	$('html,body').css({'width': '100%', 'height': '100%'});
	$('#entire_wrapper').css({ position: 'relative', 'z-index': 99 });
	
	window.playerResize = function() {
        var width = $(window).width(), playerWidth, height = $(window).height(), playerHeight;

        if ( width / ratio < height ) {
            playerWidth = Math.ceil(height * ratio);
            $('#ib2-video-background')
            	.width(playerWidth)
            	.height(height)
            	.css({
            		left: (width - playerWidth) / 2,
            		top: 0
            	});
        } else { 
            playerHeight = Math.ceil(width / ratio);
            $('#ib2-video-background')
            	.width(width)
            	.height(playerHeight)
            	.css({
            		left: 0,
            		top: (height - playerHeight) / 2
            	});
        }
    }
    
    // events
    $(window).on('resize', function() {
        playerResize();
    });
        
})(jQuery, window);
</script>

<?php }

// load IB2 CSS 
if ( !empty($data['allcss']) ) {
	foreach ( $data['allcss'] as $k => $v ) {
		echo '<style type="text/css" id="' . $k . '" class="ib2-element-css">' . "\n";
		echo stripslashes($v) . "\n";
		echo '</style>' . "\n";
	}
}

// Paragraph CSS
$line_height = ( is_array($meta) && !empty($data['line_height']) ) ? $data['line_height'] : 1.4;
$white_space = ( is_array($meta) && !empty($data['white_space']) ) ? $data['white_space'] : 18;
$paragraph_css = '.ib2-section-content p { line-height: ' . $line_height . ' !important; margin-bottom: ' . $white_space . 'px !important; }';

echo '<style type="text/css">' . $paragraph_css . '</style>' . "\n";

// head scripts
if ( !empty($data['head_scripts']) ) {
	$scripts = trim(stripslashes(addslashes($data['head_scripts'])));
	echo $scripts;
}

?>
<style type="text/css">
@media only screen and (max-width: 767px) {
	body {
		background-size: auto auto !important;
	}	
}
</style>

<!--[if lt IE 9]>
	<script src="<?php echo IB2_JS; ?>html5shiv.js" type="text/javascript"></script>
	<script src="<?php echo IB2_JS; ?>respond.min.js" type="text/javascript"></script>
<![endif]-->

</head>

<Body>
<?php if ( !empty($options['fb_appid']) ) : ?>
	
<div id="fb-root"></div>
<script>
	window.fbAsyncInit = function() {
		FB.init({
			appId      : '<?php echo esc_attr($options['fb_appid']); ?>',
			cookie     : true,
			xfbml      : true,
			version    : 'v2.2'
		});
		
		<?php do_action('ib2_facebook_action'); ?>
	};

	(function(d, s, id){
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>
    
<?php endif;

// body scripts
if ( !empty($data['body_scripts']) ) {
	$scripts = trim(stripslashes(addslashes($data['body_scripts'])));
	echo $scripts;
}

if ( !empty($background_video) ) : ?>
<div id="ib2-videobg-container" style="overflow: hidden; position: fixed; z-index: 1; width: 100%; height: 100%">
	<div id="ib2-video-background" style="position: absolute"></div>
</div>
<div id="ib2-videobg-shield" style="width: 100%; height: 100%; z-index: 2; position: absolute; left: 0; top: 0;"></div>
<script>
	var yturl = '<?php echo $background_video; ?>';
	var videoID = yturl.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);
	var youtubeId = videoID[1];

	var tag = document.createElement('script');

	tag.src = "https://www.youtube.com/iframe_api";
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

	var player;
	function onYouTubeIframeAPIReady() {
		player = new YT.Player('ib2-video-background', {
			height: ytHeight,
			width: ytWidth,
			videoId: youtubeId,
			playerVars: {
				controls: 0,
				showinfo: 0,
				modestbranding: 1,
				wmode: 'transparent'
			},
			events: {
				'onReady': onPlayerReady,
				'onStateChange': onPlayerStateChange
			}
		});
	}

	function onPlayerReady( event ) {
		playerResize();
		if ( ytMute ) event.target.mute();
        event.target.seekTo(0);
		event.target.playVideo();
	}

	function onPlayerStateChange( event ) {
		if ( event.data === 0 &&  ytLoop ) { // video ended and repeat option is set true
            player.seekTo(0); // restart
        }
	}
</script>
<?php endif; ?>
<div id="entire_wrapper" class="container-fluid">
	
<?php if ( !empty($background_video) && isset($data['background_video_ctrl']) && $data['background_video_ctrl'] == 1 ) : ?>

<ul id="ib2-bgvid-control">
<li class="ib2-ytube-play" style="display:none"><a href="#" class="bgvid-play" alt="Play Video" title="Play Video"></a></li>
<li class="ib2-ytube-pause"><a href="#" class="bgvid-pause" alt="Pause Video" title="Pause Video"></a></li>
<li class="ib2-ytube-volume-up"><a href="#" class="bgvid-volume-up" alt="Increase Volume" title="Increase Volume"></a></li>
<li class="ib2-ytube-volume-down"><a href="#" class="bgvid-volume-down" alt="Decrease Volume" title="Decrease Volume"></a></li>
<li class="ib2-ytube-mute"><a href="#" class="bgvid-mute" data-mute="<?php echo ( isset($data['background_video_mute']) && $data['background_video_mute'] == 0 ) ? 'false' : 'true'; ?>"></a></li>
</ul>';
<div class="clearfix"></div>';
<?php endif; ?>

<?php echo $content; ?>

<?php wp_footer(); ?>

<?php
if ( !empty($slider_content) ) {
	echo '<div id="ib2-bottom-slider">' . $slider_content . '</div>';
}

// footer scripts
if ( !empty($data['footer_scripts']) ) {
	$scripts = trim(stripslashes(addslashes($data['footer_scripts'])));
	echo $scripts;
}

?>
</div>
</Body>
</html>
<?php
}

add_action('wp_enqueue_scripts', 'ib2_load_scripts');
function ib2_load_scripts() {
	global $post, $ib2_variant;
	if ( !is_singular() || empty($post) )
		return;
	
	$meta = get_post_meta($post->ID, 'ib2_settings', true);
	$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
	if ( !$enable ) return;

	$variant = 'variation' . $ib2_variant;
	$data = ( isset($meta[$variant]) ) ? $meta[$variant] : array();
	
	wp_register_script('bootstrap', IB2_JS . 'bootstrap.min.js', array('jquery'), '3.2.0', true);
	//wp_register_script('tubular', IB2_JS . 'jquery.tubular.1.0.js', array('jquery'), '1.0.1', false);
	wp_register_script('jquery-cookie', IB2_JS . 'jquery.cookie.js', array('jquery'), '1.4.1', false);
	wp_register_script('moment', IB2_JS . 'moment.js', array('jquery'), '2.8.3', true);
	wp_register_script('moment-data', IB2_JS . 'moment-timezone-with-data.min.js', array('moment'), '2.8.3', true);
	wp_register_script('final-countdown', IB2_JS . 'jquery.countdown.min.js', array('jquery', 'moment-data'), '2.0.4', true);
	wp_register_script('prettyCheckable', IB2_JS . 'prettyCheckable/dist/prettyCheckable.min.js', array('jquery'), false, true);
	
	if ( isset($data['circular']) && $data['circular'] == 1 )
		wp_enqueue_script('kineticJS', IB2_JS . 'kinetic-v5.1.0.min.js', array('jquery'), '5.1.0', false);
		
	wp_enqueue_script('instabuilder2', IB2_JS . 'instabuilder2.js', 
		array(
			'jquery', 
			'jquery-ui-core', 
			'jquery-ui-widget', 
			'jquery-ui-mouse',
			'jquery-effects-core',
			'jquery-effects-transfer',
			//'tubular',
			'bootstrap', 
			'jquery-cookie', 
			'final-countdown', 
			'prettyCheckable'
		), '1.0.0', true);
}

add_action('wp_print_styles', 'ib2_load_styles');
function ib2_load_styles() {
	global $post;
	if ( !is_singular() || empty($post) )
		return;
	
	$meta = get_post_meta($post->ID, 'ib2_settings', true);
	$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
	if ( !$enable ) return;
	
	wp_register_style('bootstrap', IB2_CSS . 'bootstrap.min.css');
	wp_register_style('font-awesome', IB2_CSS . 'font-awesome.min.css');
	wp_register_style('animate', IB2_CSS . 'animate.css');
	wp_register_style('prettyCheckable', IB2_JS . 'prettyCheckable/dist/prettyCheckable.css');
	wp_enqueue_style('instabuilder2', IB2_CSS . 'instabuilder2.css', array('bootstrap', 'font-awesome', 'animate', 'prettyCheckable'));
}

add_action('wp_head', 'ib2_load_css');
function ib2_load_css() {
	global $post, $ib2_variant;
	if ( !is_singular() || empty($post) )
		return;
	
	$meta = get_post_meta($post->ID, 'ib2_settings', true);
	$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
	if ( !$enable ) return;
	
	$variant = 'variation' . $ib2_variant;
	$data = ( isset($meta[$variant]) ) ? $meta[$variant] : array();

	$default_css  = 'body { font-family: "Open Sans", sans-serif; font-size: 14px; color:#333; }' . "\n";
	$default_css .= 'body a { color: #428bca; }' . "\n";
	$default_css .= 'body a:hover, body a:focus { color: #2a6496; }' . "\n";

	$body_css = ( is_array($meta) && !empty($data['css']) ) ? stripslashes($data['css']) : $default_css;
	$content_style = ( is_array($meta) && !empty($data['content_style']) ) ? str_replace('"', "'", stripslashes($data['content_style'])) : '';
	$content_style = str_replace('{%IB2_PLUGIN_DIR%}', IB2_URL, $content_style);
	
	$body_css .= "\n" . 'body { ' . $content_style . ' }' . "\n\n";
	$body_css = str_replace('#screen-container', 'body', $body_css);
	$body_css = str_replace('#main-editor', 'body', $body_css);
?>
<style type="text/css" id="ib2-main-css">
<?php echo $body_css; ?>
</style>
<?php
}

add_action('template_redirect', 'ib2_js_vars_cookies', 2);
function ib2_js_vars_cookies() {
	global $post, $ib2_variant;
	if ( !is_singular() || empty($post) )
		return;
	
	$meta = get_post_meta($post->ID, 'ib2_settings', true);
	$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
	if ( !$enable ) return;
	
	$options = get_option('ib2_options');
	$variant = 'variation' . $ib2_variant;
	$data = ( isset($meta[$variant]) ) ? $meta[$variant] : array();
	
	$popup = 0;
	$attbar = 0;
	$poptime = '';
	$popup_id = '';
	$slider = 0;
	$slider_close = ( is_array($meta) && isset($data['slider_close']) ) ? $data['slider_close'] : 0;
	
	if ( isset($data['attention_bar']) && $data['attention_bar'] == 1 ) {
		$atttime = $data['attention_bar_time'];
		switch ( $atttime ) {
			case 'pageload':
				$attbar = 1;
				break;
				
			case 'firsttime':
				if ( !isset($_COOKIE['ib2_ft_attbar_' . $post->ID]) ) {
					$attbar = 1;
					@setcookie('ib2_ft_attbar_' . $post->ID, 1, time()+60*60*24*365, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				break;
				
			case 'daily':
				if ( !isset($_COOKIE['ib2_dl_attbar_' . $post->ID]) ) {
					$attbar = 1;
					@setcookie('ib2_dl_attbar_' . $post->ID, 1, time()+60*60*24, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				break;
			case 'session':
				if ( !isset($_COOKIE['ib2_ss_attbar_' . $post->ID]) ) {
					$attbar = 1;
					@setcookie('ib2_ss_attbar_' . $post->ID, 1, 0, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				break;
		}
	}
	
	if ( isset($data['popup']) && $data['popup'] == 1 ) {
		$poptime = $data['popup_time'];
		$popup_id = stripslashes(esc_attr($data['popup_id']));
		switch ( $poptime ) {
			case 'unfocus':
			case 'pageload':
				$popup = 1;
				break;
				
			case 'firsttime':
				if ( !isset($_COOKIE['ib2_ft_pop_' . $post->ID]) ) {
					$popup = 1;
					@setcookie('ib2_ft_pop_' . $post->ID, 1, time()+60*60*24*365, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				break;
				
			case 'daily':
				if ( !isset($_COOKIE['ib2_dl_pop_' . $post->ID]) ) {
					$popup = 1;
					@setcookie('ib2_dl_pop_' . $post->ID, 1, time()+60*60*24, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				break;
				
			case 'weekly':
				if ( !isset($_COOKIE['ib2_wl_pop_' . $post->ID]) ) {
					$popup = 1;
					@setcookie('ib2_wl_pop_' . $post->ID, 1, time()+60*60*24*6, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				break;
				
			case 'monthly':
				if ( !isset($_COOKIE['ib2_ml_pop_' . $post->ID]) ) {
					$popup = 1;
					@setcookie('ib2_ml_pop_' . $post->ID, 1, time()+60*60*24*29, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				break;
				
			case 'session':
				if ( !isset($_COOKIE['ib2_ss_pop_' . $post->ID]) ) {
					$popup = 1;
					@setcookie('ib2_ss_pop_' . $post->ID, 1, 0, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				break;
		}
	}

	if ( isset($data['slider']) && $data['slider'] == 1 ) {
		$slidertime = $data['slider_time'];
		switch ( $slidertime ) {
			case 'pageload':
				$slider = 1;
				break;
				
			case 'firsttime':
				if ( !isset($_COOKIE['ib2_ft_slide_' . $post->ID]) ) {
					$slider = 1;
					@setcookie('ib2_ft_slide_' . $post->ID, 1, time()+60*60*24*365, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				break;
				
			case 'daily':
				if ( !isset($_COOKIE['ib2_dl_slide_' . $post->ID]) ) {
					$slider = 1;
					@setcookie('ib2_dl_slide_' . $post->ID, 1, time()+60*60*24, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				break;
				
			case 'weekly':
				if ( !isset($_COOKIE['ib2_wl_slide_' . $post->ID]) ) {
					$slider = 1;
					@setcookie('ib2_wl_slide_' . $post->ID, 1, time()+60*60*24*6, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				break;
				
			case 'monthly':
				if ( !isset($_COOKIE['ib2_ml_slide_' . $post->ID]) ) {
					$slider = 1;
					@setcookie('ib2_ml_slide_' . $post->ID, 1, time()+60*60*24*29, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				break;
				
			case 'session':
				if ( !isset($_COOKIE['ib2_ss_slide_' . $post->ID]) ) {
					$slider = 1;
					@setcookie('ib2_ss_slide_' . $post->ID, 1, 0, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				break;
		}
	}
}

add_action('wp_head', 'ib2_js_vars', 11);
function ib2_js_vars() {
	global $post, $ib2_variant;
	if ( !is_singular() || empty($post) )
		return;
	
	$meta = get_post_meta($post->ID, 'ib2_settings', true);
	$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
	if ( !$enable ) return;
	
	$options = get_option('ib2_options');
	$variant = 'variation' . $ib2_variant;
	$data = ( isset($meta[$variant]) ) ? $meta[$variant] : array();
	
	$popup = 0;
	$attbar = 0;
	$poptime = '';
	$popup_id = '';
	$slider = 0;
	$slider_close = ( is_array($meta) && isset($data['slider_close']) ) ? $data['slider_close'] : 0;
	
	if ( isset($data['attention_bar']) && $data['attention_bar'] == 1 ) {
		$atttime = $data['attention_bar_time'];
		switch ( $atttime ) {
			case 'pageload':
				$attbar = 1;
				break;
				
			case 'firsttime':
				if ( !isset($_COOKIE['ib2_ft_attbar_' . $post->ID]) ) {
					$attbar = 1;
				}
				break;
				
			case 'daily':
				if ( !isset($_COOKIE['ib2_dl_attbar_' . $post->ID]) ) {
					$attbar = 1;
				}
				break;
			case 'session':
				if ( !isset($_COOKIE['ib2_ss_attbar_' . $post->ID]) ) {
					$attbar = 1;
				}
				break;
		}
	}
	
	if ( isset($data['popup']) && $data['popup'] == 1 ) {
		$poptime = $data['popup_time'];
		$popup_id = stripslashes(esc_attr($data['popup_id']));
		switch ( $poptime ) {
			case 'unfocus':
			case 'pageload':
				$popup = 1;
				break;
				
			case 'firsttime':
				if ( !isset($_COOKIE['ib2_ft_pop_' . $post->ID]) ) {
					$popup = 1;
				}
				break;
				
			case 'daily':
				if ( !isset($_COOKIE['ib2_dl_pop_' . $post->ID]) ) {
					$popup = 1;
				}
				break;
				
			case 'weekly':
				if ( !isset($_COOKIE['ib2_wl_pop_' . $post->ID]) ) {
					$popup = 1;
				}
				break;
				
			case 'monthly':
				if ( !isset($_COOKIE['ib2_ml_pop_' . $post->ID]) ) {
					$popup = 1;
				}
				break;
				
			case 'session':
				if ( !isset($_COOKIE['ib2_ss_pop_' . $post->ID]) ) {
					$popup = 1;
				}
				break;
		}
	}

	if ( isset($data['slider']) && $data['slider'] == 1 ) {
		$slidertime = $data['slider_time'];
		switch ( $slidertime ) {
			case 'pageload':
				$slider = 1;
				break;
				
			case 'firsttime':
				if ( !isset($_COOKIE['ib2_ft_slide_' . $post->ID]) ) {
					$slider = 1;
				}
				break;
				
			case 'daily':
				if ( !isset($_COOKIE['ib2_dl_slide_' . $post->ID]) ) {
					$slider = 1;
				}
				break;
				
			case 'weekly':
				if ( !isset($_COOKIE['ib2_wl_slide_' . $post->ID]) ) {
					$slider = 1;
				}
				break;
				
			case 'monthly':
				if ( !isset($_COOKIE['ib2_ml_slide_' . $post->ID]) ) {
					$slider = 1;
				}
				break;
				
			case 'session':
				if ( !isset($_COOKIE['ib2_ss_slide_' . $post->ID]) ) {
					$slider = 1;
				}
				break;
		}
	}
?>
<script type="text/javascript">
var ib2_popup = <?php echo $popup; ?>,
ib2_poptime = '<?php echo $poptime; ?>', 
ib2_popid = '<?php echo $popup_id; ?>',
ib2_slider = <?php echo $slider; ?>,
ib2_slider_close = <?php echo $slider_close; ?>,
ib2_attbar = <?php echo $attbar; ?>,
post_id = <?php echo $post->ID; ?>,
webinar_url = '<?php echo esc_url(add_query_arg('ib2mode', 'webinar_signup', get_permalink($post->ID))); ?>',
powered_by = '<?php echo ( isset($options['enable_powered']) && $options['enable_powered'] == 1 ? 'yes' : 'no' ); ?>',
powered_by_link = '<?php echo ( !empty($options['ib2affurl']) ? esc_url($options['ib2affurl']) : '' ); ?>',
powered_img = '<?php echo IB2_IMG ?>sprites/instabuilder2-poweredby.png';

jQuery(document).ready(function($){
	if ( ib2_attbar == 0 ) {
		jQuery('.ib2-notification-bar').remove();
	}
});
</script>
<?php
}

add_action('wp_footer', 'ib2_disable_right_click');
function ib2_disable_right_click() {
	global $post, $ib2_variant;
	if ( !is_singular() || empty($post) )
		return;
		
	if ( !isset($ib2_variant) )
		$ib2_variant = 'a';
		
	$meta = get_post_meta($post->ID, 'ib2_settings', true);
	$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
	if ( !$enable ) return;
	
	$variant = 'variation' . $ib2_variant;
	$data = ( isset($meta[$variant]) ) ? $meta[$variant] : array();
	
	$right_click = ( isset($data['right_click']) ) ? $data['right_click'] : 0;
	$msg = ( isset($data['right_click_msg']) ) ? esc_textarea($data['right_click_msg']) : '';
	$img = ( isset($data['right_click_img']) ) ? $data['right_click_img'] : 0;
	
	if ( $right_click != 1 )
		return;
	
	if ( !empty($msg) ) :
		$msg = stripslashes($msg);
		$msg = addslashes($msg);
		
		$remove = array("\n", "\r\n", "\r");
		$msg = str_replace($remove, "+", strip_tags(trim($msg)));
		$msg = str_replace("++", "<%enter%>", $msg);
		$msg = str_replace("<%enter%>", '\n', $msg);
	endif;
	
?>
<script type="text/javascript">
jQuery(document).ready(function($){
	var selector = <?php echo ( $img ? '"img"' : 'document'); ?>,
	msg = '<?php echo $msg; ?>';
	
	$(selector).on("contextmenu", function(){
		if ( msg != '' ) {
			alert(msg);
		}
		return false;
    }); 
});
</script>
<?php
}

add_action('wp_footer', 'ib2_exit_splash');
function ib2_exit_splash() {
	global $post, $ib2_variant;
	if ( !is_singular() || empty($post) )
		return;
		
	if ( !isset($ib2_variant) )
		$ib2_variant = 'a';
		
	$meta = get_post_meta($post->ID, 'ib2_settings', true);
	$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
	if ( !$enable ) return;
	
	$variant = 'variation' . $ib2_variant;
	$data = ( isset($meta[$variant]) ) ? $meta[$variant] : array();
	
	$exit_splash = ( isset($data['exit_splash']) ) ? $data['exit_splash'] : 0;
	$msg = ( isset($data['exit_msg']) ) ? esc_textarea($data['exit_msg']) : '';
	$url = ( isset($data['exit_url']) ) ? esc_url($data['exit_url']) : '';
	
	if ( $exit_splash != 1 )
		return;
	
	if ( $msg == '' )
		return;
	
	$exitmsg = stripslashes($msg);
	$exitmsg = addslashes($exitmsg);
	
	if ( $exitmsg != '' ) {
		$remove = array("\n", "\r\n", "\r");
		$exit_msg = str_replace($remove, "+", strip_tags(trim($exitmsg)));
		$exit_msg = str_replace("++", "<%enter%>", $exit_msg);
		$exit_msg = str_replace("<%enter%>", '\n', $exit_msg);
	} else {
		$exit_msg = '';
	}
	
?>
<script type="text/javascript">
var ib2PreventExit = false;
var ctrlKeyIsDown = false;
(function($) {
	function ib2DisplayExitPage() {
		var exitMsg = '<?php echo $exit_msg; ?>';
		var exitURL = '<?php echo $url; ?>';
		var exitPage = '';
		
		if ( ib2PreventExit == false ) {
			window.scrollTo(0,0);
			if ( jQuery.browser.mozilla ) {
				if ( parseInt(jQuery.browser.version) >= 27 ) {
					setTimeout(function(){
						window.alert(exitMsg);
					}, 100);
				} else if( parseInt(jQuery.browser.version) >= 2 ) {
					window.alert(exitMsg);
				}
			}
			
			exitPage = '<div id="ib2-exit-splash" align="center">';
			exitPage += '<iframe src="' + exitURL + '" align="middle" frameborder="0" marginheight="0" marginwidth="0" width="100%" height="100%"></iframe>';
			exitPage += '</div>';
			
			ib2PreventExit = true;
			
			jQuery('body').html('');
			jQuery('html').css('overflow', 'hidden');
			jQuery('body').css({
				'margin': '0',
				'width': '100%',
				'height': '100%',
				'overflow': 'hidden'
			});
			jQuery('body').append(exitPage);
			jQuery('#ib2-exit-splash').css({
				'background-color': '#FFFFFF',
				'position': 'fixed',
				'z-index': '9999',
				'width':'100%',
				'height':'100%',
				'top': '0',
				'left': '0',
				'display':'block'
			});
			
			jQuery('iframe').css({
				'display' : 'block',
				'width' : '100%',
				'height': '100%',
				'border' : 'none',
			});
			
			return exitMsg;
		}
	}

	$("a").each(function() {
		var obj = $(this);
		if ( obj.attr('target') != '_blank' ) {
			obj.bind("click", function(){
				ib2PreventExit = true;
    		});
		}
	});

	$("form").each(function() {
		var obj = $(this);
		obj.submit(function(){
			ib2PreventExit = true;
		});
	});
	
	$('.ib2-facebook-subscribe').click(function(){
		ib2PreventExit = true;
	});
	
	$(document).keypress(function(e){
		if ( e.keyCode == 116 )
			ib2PreventExit = true;
	});
	
	window.onbeforeunload = ib2DisplayExitPage;
})(jQuery);
</script>

<?php
}

add_action('ib2_facebook_action', 'ib2_facebook_connect');
function ib2_facebook_connect() {
?>

	(function($) {
		$('.ib2-facebook-subscribe').each(function(){
			$(this).click(function(e){
				var button = $(this);
				FB.getLoginStatus(function(response) {
					if ( response.status == 'connected' ) {
						ib2_facebook_subscribe(button);
					} else {
						ib2_facebook_login(button);
					}
	
					e.preventDefault();
				});
			});
		});
		
		var ib2_facebook_login = function( element ) {
			FB.login(function(response) {
				if ( response.authResponse ) {
					if ( response.status == 'connected' ) {
						ib2_facebook_subscribe(element);
					}
				}
			}, {scope: 'email,public_profile,manage_pages,publish_pages'});
		};
		
		var ib2_facebook_subscribe = function( element ) {
			var id = element.parent().attr('id'), parentID = id.replace('-fb', '');
			FB.api('/me', {fields: 'first_name,email'}, function(response) {
				var opl_name  = response.first_name;
				var opl_email = response.email;
				$('#' + parentID).find('.ib2-validate-email').val(opl_email);
				if ( $('#' + parentID).find('input[type=text]').length ) {
					$('#' + parentID).find('input[type=text]').each(function(){
						var dis = $(this);
						if ( !dis.hasClass('ib2-validate-email') ) {
							dis.val(opl_name);
							return false;
						}
					});
				}
				setTimeout(function(){
					$('#' + parentID).find('form').submit();
				}, 2000);
			});
		};
	})(jQuery);

<?php
}

add_action('template_redirect', 'ib2_facebook_page_render', 1);
function ib2_facebook_page_render() {
	if ( isset($_GET['ib2mode']) && $_GET['ib2mode'] == 'fbpage' && isset($_REQUEST['signed_request']) ) {
		global $wpdb;
		
		$options = get_option('ib2_options');
		$signed_request = $_REQUEST['signed_request'];
		$secret = ( isset($options['fb_secret']) ) ? trim(stripslashes($options['fb_secret'])) : '';
		
		list($encoded_sig, $payload) = explode('.', $signed_request, 2);

		// decode the data
  		$sig = base64_url_decode($encoded_sig);
  		$data = json_decode(base64_url_decode($payload), true);
		
	  	// confirm the signature
	  	/*
		$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
	  	if ( $sig !== $expected_sig ) {
			$output  = 'Bad Signed JSON signature!';
			$output .= '<br>';
			$output .= 'Received: '.$sig;
			$output .= '<br>';
			$output .= 'Expected: '.$expected_sig;
			wp_die($output);
		}
		*/
		
		if ( !isset($data['algorithm']) )
			wp_die('ERROR: Bad Signed JSON signature!');
		
		if ( !isset($data['page']['id']) )
			wp_die('ERROR: Cannot found Facebook page ID.');
		
		$fb_id = $data['page']['id'];
		if ( !$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}ib2_facebook` WHERE `fb_id` = %s", $fb_id)) ) {
			wp_die('ERROR: Cannot found landing page associated with this Facebook page.');
		}

		$url = esc_url(add_query_arg('ib2facebook', 'true', get_permalink($row->post_id)));
		wp_redirect($url);
		exit;
	}
}

if ( !function_exists('base64_url_decode') ) :
	function base64_url_decode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
	}
endif;

function ib2_create_zip( $files = array(), $destination = '', $overwrite = false ) {
	if ( file_exists($destination) && !$overwrite) { return false; }
	$valid_files = array();
	if ( is_array($files) ) {
		foreach ( $files as $file ) {
			//make sure the file exists
			if ( file_exists($file) ) {
				$valid_files[] = $file;
			}
		}
	}
	
	// if we have good files...
	if ( count($valid_files) && class_exists('ZipArchive') ) {
		// create the archive
		$zip = new ZipArchive();
		if ( $zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true ) {
			return false;
		}
		// add the files
		$ds = DIRECTORY_SEPARATOR;
		foreach ( $valid_files as $file ) {
			$filename = str_replace(realpath(IB2_PATH . 'cache') . $ds, '', $file);
			$zip->addFile($file, $filename);
		}
		
		$zip->close();
		
		// check to make sure the file exists
		return file_exists($destination);
	} else {
		return false;
	}
}
