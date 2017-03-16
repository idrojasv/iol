<?php
/*
Plugin Name: InstaBuilder 2.0
Plugin URI: http://www.instabuilder.com
Description: Quickly and Easily create beautiful, highly converting, marketing pages with huge functionality at the touch of a button. Compatible with Iphone, Ipad, Android, Blackberry, and other mobile devices.
Version: 2.1.7
Author: Suzanna Theresia
Author URI: http://www.instabuilder.com
*/

@ini_set('pcre.backtrack_limit', 500000);

define( 'IB2_URL', plugin_dir_url(__FILE__) );
define( 'IB2_PATH', plugin_dir_path(__FILE__) );
define( 'IB2_BASENAME', plugin_basename( __FILE__ ) );

define( 'IB2_INC', IB2_PATH . 'inc/');
define( 'IB2_ADMIN', IB2_PATH . 'admin/');

define( 'IB2_JS', IB2_URL . 'assets/js/' );
define( 'IB2_CSS', IB2_URL . 'assets/css/' );
define( 'IB2_IMG', IB2_URL . 'assets/img/' );

define( 'IB2_VERSION', '2.1.7' );
define( 'IB2_FONTS_VERSION', '1.12' );
define( 'IB2_DB_VERSION', '0.39' );
define( 'IB2_TMPL_VERSION', '0.27' );
define( 'IB2_TMPLS_VERSION', '0.12' );

if ( is_admin() ) {
	require_once( IB2_ADMIN . 'admin.php' );
} else {
	require_once( IB2_INC . 'functions.php' );
	require_once( IB2_INC . 'layout.php' );
}

require_once( IB2_INC . 'shortcodes.php' );

register_activation_hook( __FILE__, 'ib2_install');
function ib2_install() {
	global $wpdb;
	
	$db_version = get_option('ib2_db_version');
	$db_version = ( $db_version === FALSE ) ? 0 : $db_version;
	if ( version_compare($db_version, IB2_DB_VERSION, '<') ) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		$table = "{$wpdb->prefix}ib2_templates";
		$sql = "CREATE TABLE {$table} (
				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL,
				`screenshot` varchar(255) NOT NULL,
				`type` varchar(100) NOT NULL,
				`subtype` varchar(100) NOT NULL,
				`mode` varchar(20) NOT NULL,
				`tags` varchar(255) NOT NULL,
				`metadata` longtext NOT NULL,
				`version` tinyint(2) NULL DEFAULT 0,
				`set` varchar(50) NOT NULL,
				`created` datetime NOT NULL,
				UNIQUE KEY id (ID),
				KEY name_index (name),
				KEY type_index (type),
				KEY subtype_index (subtype),
				KEY tags_index (tags),
				KEY set_index (`set`),
				KEY nt_index (name, type)
    		) DEFAULT CHARSET=utf8;";
			
		$table = "{$wpdb->prefix}ib2_templatesets";
		$sql .= "CREATE TABLE {$table} (
				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
				`name` varchar(50) NOT NULL,
				`title` varchar(255) NOT NULL,
				`optin` varchar(20) NOT NULL,
				`sales` varchar(20) NOT NULL,
				`launch` varchar(20) NOT NULL,
				`webinar` varchar(20) NOT NULL,
				UNIQUE KEY id (ID)
    		) DEFAULT CHARSET=utf8;";
			
		$table = "{$wpdb->prefix}ib2_pages";
		$sql .= "CREATE TABLE {$table} (
				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
				`post_id` bigint(20) NOT NULL,
				`group_id` bigint(20) NOT NULL,
				`name` varchar(255) NOT NULL,
				`status` varchar(100) NOT NULL,
				`created` datetime NOT NULL,
				UNIQUE KEY id (ID),
				KEY post_id_index (post_id),
				KEY group_id_index (group_id),
				KEY status_index (status)
    		) DEFAULT CHARSET=utf8;";
			
		$table = "{$wpdb->prefix}ib2_groups";
		$sql .= "CREATE TABLE {$table} (
				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL,
				`funnel_type` varchar(50) NOT NULL,
				`is_funnel` tinyint(2) DEFAULT 0,
				`created` datetime NOT NULL,
				UNIQUE KEY id (ID),
				KEY funnel_type_index (funnel_type),
				KEY is_funnel_index (is_funnel)
    		) DEFAULT CHARSET=utf8;";
    		
		$table = "{$wpdb->prefix}ib2_variants";
		$sql .= "CREATE TABLE {$table} (
				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
				`post_id` bigint(20) NOT NULL,
				`variant` varchar(10) NOT NULL,
				`weight` int(11) NOT NULL,
				`quota` int(11) NOT NULL,
				`status` varchar(20) NOT NULL,
				UNIQUE KEY id (ID),
				KEY post_id_index (post_id),
				KEY variant_index (variant),
				KEY status_index (status)
    		) DEFAULT CHARSET=utf8;";
			
		$table = "{$wpdb->prefix}ib2_hits";
		$sql .= "CREATE TABLE {$table} (
				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
				`post_id` bigint(20) NOT NULL,
				`visitorid` varchar(100) NOT NULL,
				`variant` varchar(10) NOT NULL,
				`url` varchar(255) NOT NULL,
				`ipaddress` varchar(50) NOT NULL,
				`referer` varchar(255) NOT NULL,
				`browser` varchar(50) NOT NULL,
				`location` varchar(100) NOT NULL,
				`subid` varchar(100) NOT NULL,
				`date` datetime NOT NULL,
				UNIQUE KEY id (ID),
				KEY post_id_index (post_id),
				KEY visitorid_index (visitorid),
				KEY variant_index (variant),
				KEY pidv_index (post_id, variant),
				KEY pidvv_index (post_id, visitorid, variant)
    		) DEFAULT CHARSET=utf8;";
    		
    	$table = "{$wpdb->prefix}ib2_conversions";
		$sql .= "CREATE TABLE {$table} (
				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
				`post_id` bigint(20) NOT NULL,
				`visitorid` varchar(100) NOT NULL,
				`variant` varchar(10) NOT NULL,
				`revenue` decimal(5,2) NOT NULL,
				`subid` varchar(100) NOT NULL,
				`date` datetime NOT NULL,
				UNIQUE KEY id (ID),
				KEY post_id_index (post_id),
				KEY visitorid_index (visitorid),
				KEY variant_index (variant),
				KEY pidv_index (post_id, variant),
				KEY pidvv_index (post_id, visitorid, variant)
    		) DEFAULT CHARSET=utf8;";
			
		$table = "{$wpdb->prefix}ib2_quizzes";
		$sql .= "CREATE TABLE {$table} (
				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
				`post_id` bigint(20) NOT NULL,
				`variant` varchar(10) NOT NULL,
				`created` datetime NOT NULL,
				UNIQUE KEY id (ID)
    		) DEFAULT CHARSET=utf8;";
			
		$table = "{$wpdb->prefix}ib2_quiz_questions";
		$sql .= "CREATE TABLE {$table} (
				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
				`post_id` bigint(20) NOT NULL,
				`order` int(11) NOT NULL,
				`variant` varchar(10) NOT NULL,
				`question` varchar(255) NOT NULL,
				`a1` varchar(255) NOT NULL,
				`a2` varchar(255) NOT NULL,
				`a3` varchar(255) NOT NULL,
				`a4` varchar(255) NOT NULL,
				`a5` varchar(255) NOT NULL,
				UNIQUE KEY id (ID)
    		) DEFAULT CHARSET=utf8;";
    		
    	$table = "{$wpdb->prefix}ib2_quiz_stats";
		$sql .= "CREATE TABLE {$table} (
				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
				`post_id` bigint(20) NOT NULL,
				`variant` varchar(10) NOT NULL,
				`question` int(11) NOT NULL,
				`answer` varchar(15) NOT NULL,
				UNIQUE KEY id (ID)
    		) DEFAULT CHARSET=utf8;";
    		
    	$table = "{$wpdb->prefix}ib2_facebook";
		$sql .= "CREATE TABLE {$table} (
				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
				`post_id` bigint(20) NOT NULL,
				`fb_id` varchar(100) NOT NULL,
				UNIQUE KEY id (ID)
    		) DEFAULT CHARSET=utf8;";
    		
    	$table = "{$wpdb->prefix}ib2_funnel";
		$sql .= "CREATE TABLE {$table} (
				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
				`post_id` bigint(20) NOT NULL,
				`group_id` bigint(20) NOT NULL,
				`page_type` varchar(100) NOT NULL,
				`template_id` int(11) NOT NULL,
				UNIQUE KEY id (ID),
				KEY post_id_index (post_id),
				KEY group_id_index (group_id),
				KEY page_type_index (page_type),
				KEY template_id_index (template_id)
    		) DEFAULT CHARSET=utf8;";
    		
    	$table = "{$wpdb->prefix}ib2_histories";
		$sql .= "CREATE TABLE {$table} (
				`ID` bigint(20) NOT NULL AUTO_INCREMENT,
				`post_id` bigint(20) NOT NULL,
				`variant` varchar(10) NOT NULL,
				`content` longtext NOT NULL,
				`date` int(11) NOT NULL,
				UNIQUE KEY id (ID),
				KEY post_id_index (post_id),
				KEY variant_index (variant),
				KEY pidv_index (post_id, variant)
    		) DEFAULT CHARSET=utf8;";
    		
		dbDelta($sql);

		// Update DB Version...
		update_option('ib2_db_version', IB2_DB_VERSION);
	}
	
	// INSTALL/UPDATE DEFAULT TEMPLATES
	$t_version = get_option('ib2_tmpl_version');
	$t_version = ( $t_version === FALSE ) ? 0 : $t_version;
	$license_key = ib2_license_key();
	if ( version_compare($t_version, IB2_TMPL_VERSION, '<') && !empty($license_key) ) {
		if ( file_exists(IB2_PATH . 'templates/templates_data.txt') ) {
			$_ts = @file_get_contents(IB2_PATH . 'templates/templates_data.txt');
			if ( $_ts ) {
				$templates = maybe_unserialize($_ts);
				foreach ( $templates as $template ) {
					$data = array(
						'name' => $template['name'],
						'created' => date_i18n("Y-m-d H:i:s"),
						'screenshot' => $template['screenshot'],
						'type' => $template['type'],
						'tags' => $template['tags'],
						'version' => $template['version'],
						'subtype' => $template['subtype'],
						'mode' => 'default',
						'set' => $template['set'],
					);
					
					if ( $t = ib2_get_templatebyname($template['name']) ) {
						unset($data['name'], $data['created']);
						if ( version_compare($t->version, $template['version'], '<') ) {
							// update the metadata
							$file = IB2_PATH . 'templates/' . $t->name . '.txt';
							if ( !file_exists($file) ) // try on another folder
								$file = WP_CONTENT_DIR . '/ib2-templates/' . $t->name . '.txt';

							if ( !file_exists($file) ) // try on another folder
								$file = IB2_PATH . 'templates/sub/' . $t->name . '.txt';
							
							if ( file_exists($file) ) {
								$content = @file_get_contents($file);
								$data['metadata'] = $content;
							}
						}
						$wpdb->update("{$wpdb->prefix}ib2_templates", $data, array('ID' => $t->ID));
					} else {
						$wpdb->insert("{$wpdb->prefix}ib2_templates", $data);
					}
				}
			}
		}

		// Clean up duplicate templates
		$sql = "SELECT name, ID, type, COUNT(*) c FROM `{$wpdb->prefix}ib2_templates` GROUP BY name HAVING c > 1";
		$result = $wpdb->get_results($sql);
		if ( $result ) {
			foreach ( $result as $res ) {
				$id = (int) $res->ID;
				$name = stripslashes($res->name);
				$type = $res->type;
				$entries = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}ib2_templates` WHERE `name` = '{$name}' AND `type` = '{$type}'");
				if ( $entries && count($entries) > 1 ) {
					$wpdb->query("DELETE FROM `{$wpdb->prefix}ib2_templates` WHERE `name` = '{$name}' AND `type` = '{$type}' AND `ID` <> {$id}");
				}
			}
		}
	
		// Update TMPL Version...
		update_option('ib2_tmpl_version', IB2_TMPL_VERSION);
	}	

	$ts_version = get_option('ib2_tmpls_version');
	$ts_version = ( $ts_version === FALSE ) ? 0 : $ts_version;
	if ( version_compare($ts_version, IB2_TMPLS_VERSION, '<') && !empty($license_key) ) {
		if ( file_exists(IB2_PATH . 'templates/templatesets_data.txt') ) {
			$_ts = @file_get_contents(IB2_PATH . 'templates/templatesets_data.txt');
			if ( $_ts ) {
				$sets = maybe_unserialize($_ts);
				foreach ( $sets as $set ) {
					$name = stripslashes($set['name']);
					$chk = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}ib2_templatesets` WHERE `name` = '{$name}'");
					$data = array(
							'launch' => $set['launch'],
							'optin' => $set['optin'],
							'sales' => $set['sales'],
							'webinar' => $set['webinar'],
							'title' => stripslashes($set['title']),
						);
						
					if ( $chk > 0 ) {
						$wpdb->update("{$wpdb->prefix}ib2_templatesets", $data, array('name' => $name));
					} else {
						$data['name'] = $name;
						$wpdb->insert("{$wpdb->prefix}ib2_templatesets", $data);
					}
				}
			}
		}
		// Update TMPLS Version...
		update_option('ib2_tmpls_version', IB2_TMPLS_VERSION);
	}	
}

add_action('plugins_loaded', 'ib2_update_db_check');
function ib2_update_db_check() {
	ib2_install();
}
function ib2_get_templates( $args = array() ) {
	global $wpdb;
	$defaults = array(
		'keyword' => '',
		'type' => 'all',
		'subtype' => '',
		'filter' => 'rand',
		'sort' => 'ASC',
		'start' => 0,
		'limit' => 0,
		'mode' => 'all'
	);
	$args = wp_parse_args( $args, $defaults );
	extract($args, EXTR_SKIP);
	
	$sql = "SELECT * FROM {$wpdb->prefix}ib2_templates WHERE 1=1";
	if ( $type != 'all' && $type != '' )
		$sql .= " AND `type` = '{$type}'";
	
	if ( !empty($subtype) ) {
		$sql .= " AND `subtype` = '{$subtype}'";
	}
	
	if ( $mode == 'default' || $mode == 'custom' )
		$sql .= " AND `mode` = '{$mode}'";
	
	if ( !empty($keyword) ) {
		$keyword = sanitize_text_field($keyword);
		$sql .= " AND (`name` LIKE '%{$keyword}%' OR `tags` LIKE '%{$keyword}%' OR `set` LIKE '%{$keyword}%')";
	}
	
	if ( $filter == 'rand' ) {
		$sql .= " ORDER BY RAND()";
	} else {
		if ( $filter == 'id' ) $filter = 'ID';
		if ( $filter == 'date' ) $filter = 'created';
		$sql .= " ORDER BY `{$filter}` {$sort}";
	}
	
	
	if ( $limit > 0 ) {
		$sql .= " LIMIT {$start},{$limit}";
	}
	
	$results = $wpdb->get_results($sql);
	if ( !$results ) return false;
	
	return $results;	
}

function ib2_get_template( $template_id ) {
	global $wpdb;
	$ID = (int) $template_id;
	$sql = "SELECT * FROM {$wpdb->prefix}ib2_templates";
	$sql .= " WHERE `ID` = %d";
	
	$template = wp_cache_get('template_' . $ID, 'ib2_templates');
	if ( !$template ) {
		$template = $wpdb->get_row($wpdb->prepare($sql, $ID));
		if ( !$template ) return false;
		
		wp_cache_add('template_' . $ID, $template, 'ib2_templates');
	}
	
	return $template;	
}

function ib2_get_templatebyname( $name ) {
	global $wpdb;
	$name = sanitize_text_field($name);
	$sql = "SELECT * FROM {$wpdb->prefix}ib2_templates";
	$sql .= " WHERE `name` = %s";
	
	$template = wp_cache_get('template_' . $name, 'ib2_templates');
	if ( !$template ) {
		$template = $wpdb->get_row($wpdb->prepare($sql, $name));
		if ( !$template ) return false;
		
		wp_cache_add('template_' . $name, $template, 'ib2_templates');
	}
	return $template;
}

function ib2_get_variants( $post_id ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}ib2_variants` WHERE `post_id` = %d ORDER BY `variant` ASC", $post_id));
	
	return ( ( $results ) ? $results : false );
}

function ib2_variant_exists( $post_id, $variant ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	$chk = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}ib2_variants` WHERE `post_id` = %d AND `variant` = %s", $post_id, $variant));
	
	return ( ( $chk > 0 ) ? true : false );
}

function ib2_variant_paused( $post_id, $variant ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	$row = $wpdb->get_row($wpdb->prepare("SELECT `weight` FROM `{$wpdb->prefix}ib2_variants` WHERE `post_id` = %d AND `variant` = %s", $post_id, $variant));
	
	return ( ( $row->weight <= 0 ) ? true : false );
}

function ib2_get_default_variant( $post_id, $active = false ) {
	global $wpdb;
	
	$default = 'a';
	if ( !ib2_variant_exists($post_id, $default) ) {
		$post_id = (int) $post_id;
		
		$where = ( $active ) ? " AND `weight` > 0" : "";
		$row = $wpdb->get_row($wpdb->prepare("SELECT `variant` FROM `{$wpdb->prefix}ib2_variants` WHERE `post_id` = %d{$where} ORDER BY `ID` ASC LIMIT 1", $post_id));
		
		if ( !$row && $active ) {
			$row = $wpdb->get_row($wpdb->prepare("SELECT `variant` FROM `{$wpdb->prefix}ib2_variants` WHERE `post_id` = %d ORDER BY `ID` ASC LIMIT 1", $post_id));
		}
		
		if ( $row && isset($row->variant) )
			$default = $row->variant;
	}
	return $default;
} 


function ib2_normalfonts() {
	return array(
		'Arial' => 'Arial, "Helvetica Neue", Helvetica, sans-serif',
		'Arial Black' => '"Arial Black", "Arial Bold", Arial, sans-serif',
		'Arial Narrow' => '"Arial Narrow", Arial, "Helvetica Neue", Helvetica, sans-serif',
		'Comic Sans MS' => '"comic sans ms",sans-serif',
		'Courier New' => '"Courier New", Courier, Verdana, sans-serif',
		'Georgia' => 'Georgia, "Times New Roman", Times, serif',
		'Helvetica' => '"Helvetica Neue", Helvetica, sans-serif',
		'Impact' => 'Impact, Charcoal, sans-serif',
		'Lucida Grande' => '"Lucida Grande", "Lucida Sans Unicode", sans-serif',
		'Tahoma' => 'Tahoma, Geneva, sans-serif',
		'Times New Roman' => '"Times New Roman", Times, Georgia, serif',
		'Trebuchet MS' => '"Trebuchet MS", "Lucida Grande", "Lucida Sans", Arial, sans-serif',
		'Verdana' => 'Verdana, sans-serif'
	);
}

function ib2_googlefonts() {
	return array(
			'Allura' => 'cursive',
			'Architects Daughter' => 'cursive',
			'Arvo' => 'serif',
			'Bevan' => 'cursive',
			'Boogaloo' => 'cursive',
			'Bowlby One' => 'cursive',
			'Cabin' => 'sans-serif',
			'Cinzel' => 'serif',
			'Codystar' => 'cursive',
			'Covered By Your Grace' => 'cursive',
			'Crafty Girl' => 'cursive',
			'Dancing Script' => 'cursive',
			'Droid Sans' => 'sans-serif',
			'Droid Serif' => 'serif',
			'Exo' => 'sans-serif',
			'Ewert' => 'cursive',
			'Flavors' => 'cursive',
			'Finger Paint' => 'cursive',
			'Gloria Hallelujah' => 'cursive',
			'Henny Penny' => 'cursive',
			'Jacques Francois Shadow' => 'cursive',
			'Kaushan Script' => 'cursive',
			'Lato' => 'sans-serif',
			'Lobster' => 'cursive',
			'Monofett' => 'cursive',
			'Mountains of Christmas' => 'cursive',
			'Noto Sans' => 'sans-serif',
			'Nova Mono' => 'cursive',
			'Open Sans' => 'sans-serif',
			'Open Sans Condensed' => 'sans-serif',
			'Permanent Marker' => 'cursive',
			'PT Sans' => 'sans-serif',
			'PT Sans Narrow' => 'sans-serif',
			'PT Serif' => 'serif',
			'Rock Salt' => 'cursive',
			'Rokkitt' => 'serif',
			'Sansita One' => 'cursive',
			'Shadows Into Light' => 'cursive',
			'Sirin Stencil' => 'cursive',
			'Special Elite' => 'cursive',
			'Ubuntu' => 'sans-serif',
			'VT323' => 'cursive',
			'Vollkorn' => 'serif',
    	);
}

function ib2_googlefonts_url() {
	$proto = ( is_ssl() ) ? 'https://' : 'http://';
	$url = $proto . 'fonts.googleapis.com/css?family=';
	$font = array();
	$fonts = ib2_googlefonts();
	if ( $fonts ) {
		foreach ( $fonts as $k => $v ) {
			$size = '';
			if ( $k == 'Open Sans' )
				$size = ':400,400italic,600,600italic,700,700italic,800,800italic';
			else if ( $k == 'Lato' )
				$size = ':300,300italic,400,400italic,700,700italic,900,900italic';
			else if ( $k == 'Noto Sans' || $k == 'Droid Serif' || $k == 'PT Sans' )
				$size = ':400,400italic,700,700italic';
			else if ( $k == 'Cinzel' )
				$size = ':400,700,900';
			else if ( $k == 'Rokkitt' || $k == 'Droid Sans' || $k == 'PT Sans Narrow' )
				$size = ':400,700';
			
			$font[] = str_replace(" ", "+", $k) . $size;
		}
		if ( count($font) > 0 )
			$url .= implode("|", $font);
	}
	
	return $url;
}

function ib2_clean_histories() {
	global $wpdb;
	
	$options = get_option('ib2_options');
	$days = ( isset($options['ib2historyage']) ) ? (int) $options['ib2historyage'] : 7;

	if ( $days > 0 ) {
		$str = ( $days > 1 ) ? "{$days} days ago" : "{$days} day ago";
		$olddate = strtotime($str, time());
		$rst = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}ib2_histories` WHERE `date` <= {$olddate}");
		if ( $rst ) {
			foreach ( $rst as $row ) {
				$wpdb->delete("{$wpdb->prefix}ib2_histories", array('ID' => $row->ID));
			}
		}
	}
}

add_action('wp', 'ib2_cron');
function ib2_cron() {
	if ( !wp_next_scheduled('ib2_cron_event') ) {
		wp_schedule_event(time(), 'twicedaily', 'ib2_cron_event');
	}
}
add_action('ib2_cron_event', 'ib2_clean_histories');

register_deactivation_hook(__FILE__, 'ib2_cron_deactivated');
function ib2_cron_deactivated() {
	wp_clear_scheduled_hook('ib2_cron_event');
}

function ib2_string_enc( $string, $key ) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
	$j = 0;
	$hash = '';
    for ( $i = 0; $i < $strLen; $i++ ) {
        $ordStr = ord(substr($string,$i,1));
        if ( $j == $keyLen ) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
    }
    return $hash;
}

function ib2_string_dec( $string, $key ) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
	$j = 0;
	$hash = '';
    for ($i = 0; $i < $strLen; $i+=2) {
        $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
        if ( $j == $keyLen ) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= chr($ordStr - $ordKey);
    }
    return $hash;
}

if ( !function_exists('is_base64') ) :
function is_base64( $str ) {
    return (bool)preg_match('`^[a-zA-Z0-9+/]+={0,2}$`', $str);
}
endif;

function ib2_esc( $string ) {
	return stripslashes(esc_attr($string));
}

function is_ib2_admin() {
	if ( is_user_logged_in() && current_user_can('manage_options') ) return true;

	return false;
}

function ib2_dump( $dump, $return = false ) {
	if ( $return )
		return '<pre>' . print_r($dump, true) . '</pre>';
	else
		echo '<pre>' . print_r($dump, true) . '</pre>';
}

require_once( IB2_INC . 'update-checker/plugin-update-checker.php' );
$ib2_update = PucFactory::buildUpdateChecker(
	'http://instabuilder.com/ib2-update-server/?action=get_metadata&slug=instabuilder2',
	__FILE__,
	'instabuilder2'
);
$ib2_update->addQueryArgFilter('ib2_filter_update_checks');
function ib2_filter_update_checks( $queryArgs ) {
    $license_key = ib2_license_key();
    if ( !empty($license_key) ) {
        $queryArgs['license_key'] = $license_key;
    }
    return $queryArgs;
}
add_filter("puc_manual_check_message-{$ib2_update->slug}", 'ib2_update_message', 10, 2);
function ib2_update_message( $message, $status ) {
	if ( $status == 'update_available' )
		$message = 'A new version of InstaBuilder 2.0 is available.';
	else if ( $status == 'no_update' )
		$message = 'InstaBuilder 2.0 is up to date.';
	return $message;
}