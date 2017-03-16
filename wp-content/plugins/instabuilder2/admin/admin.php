<?php
// init admin-related files...
require_once( IB2_ADMIN . 'editor.php' );
require_once( IB2_ADMIN . 'metabox.php' );
add_action('admin_enqueue_scripts', 'ib2_post_enqueue');
function ib2_post_enqueue( $hook ) {
    if( 'post.php' != $hook && 'post-new.php' != $hook ) return;
    wp_enqueue_script('jquery');
	wp_enqueue_style('ib2-meta', IB2_CSS . 'admin-meta.css');
	add_thickbox();
}

add_action('admin_enqueue_scripts', 'ib2_admin_scripts');
function ib2_admin_scripts( $hook ) {
	$panels = array(
		'toplevel_page_ib2-dashboard',
		'toplevel_page_ib2-settings',
		'instabuilder-2-0_page_ib2-pages', 
		'instabuilder-2-0_page_ib2-settings',
		'instabuilder-2-0_page_ib2-funnel'
	);
	
	if ( !in_array($hook, $panels) ) return;
	// load js for admin page
	wp_register_script('jqplot', IB2_JS . 'jqplot/jquery.jqplot.min.js', array('jquery'), '1.0.8', false);
	wp_register_script('jqplot-dateAxisRenderer', IB2_JS . 'jqplot/plugins/jqplot.dateAxisRenderer.min.js', array('jqplot'), '1.0.8', false);
	wp_register_script('jqplot-logAxisRenderer', IB2_JS . 'jqplot/plugins/jqplot.logAxisRenderer.min.js', array('jqplot', 'jqplot-dateAxisRenderer'), '1.0.8', false);
	wp_register_script('jqplot-canvasTextRenderer', IB2_JS . 'jqplot/plugins/jqplot.canvasTextRenderer.min.js', array('jqplot', 'jqplot-logAxisRenderer'), '1.0.8', false);
	wp_register_script('jqplot-canvasAxisTickRenderer', IB2_JS . 'jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js', array('jqplot', 'jqplot-canvasTextRenderer'), '1.0.8', false);
	wp_register_script('jqplot-highlighter', IB2_JS . 'jqplot/plugins/jqplot.highlighter.min.js', array('jqplot', 'jqplot-canvasAxisTickRenderer'), '1.0.8', false);
	
	wp_register_script('bootstrap', IB2_JS . 'bootstrap.min.js', array('jquery'), '3.2.0', true);
	wp_enqueue_script('ib2-admin', IB2_JS . 'admin.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'bootstrap', 'jqplot-highlighter'), false, true);
	wp_enqueue_style('ib2-bootstrap', IB2_CSS . 'ib2-bootstrap.css');
	wp_enqueue_style('font-awesome', IB2_CSS . 'font-awesome.min.css');
	wp_enqueue_style('jqplot', IB2_JS . 'jqplot/jquery.jqplot.min.css');
	wp_enqueue_style('jquery-ui', '//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css');
	wp_enqueue_style('ib2-admin', IB2_CSS . 'admin.css');
}

add_action('admin_menu', 'ib2_add_admin');
function ib2_add_admin() {
	$slug = ( ib2_chkdsk() ) ? 'ib2-dashboard' : 'ib2-settings';
	add_menu_page('InstaBuilder 2.0', 'InstaBuilder 2.0', 'manage_options', $slug, 'ib2_admin');
	
	if ( ib2_chkdsk() ) {
		add_submenu_page('ib2-dashboard', 'Dashboard', 'Dashboard', 'manage_options', 'ib2-dashboard', 'ib2_admin');
		add_submenu_page('ib2-dashboard', 'Settings', 'Settings', 'manage_options', 'ib2-settings', 'ib2_admin');
	}
}

function ib2_admin() {
	echo '<div class="wrap">' . "\n";
		echo "\t" . '<div id="ib2-admin" class="ib2-bootstrap">' . "\n";
		
		$page = isset($_GET['page']) ? $_GET['page'] : 'ib2-dashboard'; 
		switch ( $page ) {
			case 'ib2-pages':
				ib2_pages();
				break;
				
			case 'ib2-settings':
				ib2_settings();
				break;
				
			case 'ib2-dashboard':
			default:
				ib2_dashboard();
				break;
		}
		
		echo "\t" . '</div>' . "\n";
	echo '</div>' . "\n";
}

function ib2_dashboard() {
	global $wpdb;
	
	ib2_admin_header();
	if ( isset($_GET['mode']) && $_GET['mode'] == 'stats' ) {
		if ( isset($_GET['variant']) )
			require_once 'page-stats-details.php';
		else
			require_once 'page-stats.php';
	} else {
		require_once 'pages.php';
	}
	ib2_admin_footer();
}

function ib2_settings() {
	global $wpdb;
	
	ib2_admin_header();
	require_once 'settings.php';
	ib2_admin_footer();
}

function ib2_admin_header() {
?>
	<nav class="navbar navbar-inverse" role="navigation">
  		<div class="container-fluid">
    		<div class="navbar-header">
      			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#ib2-admin-navigation-collapse">
        			<span class="sr-only">Toggle navigation</span>
        			<span class="icon-bar"></span>
        			<span class="icon-bar"></span>
        			<span class="icon-bar"></span>
      			</button>
      			<a class="navbar-brand" href="http://instabuilder.com/" target="_blank"><img src="<?php echo IB2_IMG; ?>ib2-logo.png" border="0" /></a>
    		</div>

    		<!-- Collect the nav links, forms, and other content for toggling -->
    		<div class="collapse navbar-collapse" id="ib2-admin-navigation-collapse">
				<ul class="nav navbar-nav">
					<li><a href="http://instabuilder.com/v2.0/member/tutorials/" target="_blank"><i class="fa fa-video-camera"></i> Training Videos</a></li>
					<li><a href="http://instabuilder.com/v2.0/legal/faq.html" target="_blank"><i class="fa fa-question"></i> F.A.Q</a></li>
					<!-- <li><a href="http://marketplace.instabuilder.com" target="_blank"><i class="fa fa-shopping-cart"></i> Marketplace</a></li> -->
					<li><a href="http://asksuzannatheresia.com" target="_blank"><i class="fa fa-support"></i> Support</a></li>
				</ul>
				<p class="navbar-text navbar-right">v<?php echo IB2_VERSION; ?></p>
    		</div><!-- /.navbar-collapse -->
  		</div><!-- /.container-fluid -->
	</nav>
	
	<div id="ib2-admin-content" class="row-fluid">
		
<?php
}

function ib2_admin_footer() {
?>
		</div><!-- ib2-admin-content -->
<?php
}
	
add_action('admin_init', 'ib2_options_save');
function ib2_options_save() {
	if ( isset($_POST['action']) && $_POST['action'] == 'ib2_options_save' ) {
		global $wpdb;
		
		$_data = $_POST['ib2post'];
		$data = array_map('sanitize_text_field', $_data);
		
		update_option('ib2_options', $data);
		
		// Check if there's new template zip file
		if ( isset($_FILES['upload-zip-template']['name']) && !empty($_FILES['upload-zip-template']['name']) ) {
			$upload = wp_handle_upload($_FILES['upload-zip-template'], array('test_form' => false));
			if ( is_wp_error($upload) ) {
				wp_die("ERROR: " . $upload->get_error_message());
			}
			$zip_file = $upload['file']; 
			
			WP_Filesystem();
			$destination_path = IB2_PATH . 'cache/';
			$unzipfile = unzip_file($zip_file, $destination_path);
   			$ds = DIRECTORY_SEPARATOR;
			
			if ( $unzipfile ) {
				@unlink($zip_file);
				$check = ib2_getfile_list($destination_path);
				if ( empty($check) )
					wp_die("ERROR: The zip you uploaded is not a valid IB 2.0 template zip file.");
				
				$folder_path = $destination_path;
				if ( !file_exists($folder_path . 'templates-data.txt') ) {
					foreach ( $check as $chk ) {
						if ( is_dir($chk) ) {
							
							$folder_path = $chk . $ds;
							break;
						}
					}
				}
				
				// let's check again
				if ( !file_exists($folder_path . 'templates-data.txt') ) {
					wp_die("ERROR: The zip you uploaded is not a valid IB 2.0 template zip file.");
				}
				
				$templates_path = $folder_path . 'templates/';
				$images_path = $folder_path . 'images/';
				$screenshots_path = $folder_path . 'screenshots/';
				$alt_screenshots_path = ABSPATH . 'wp-content' . $ds . 'uploads' . $ds . 'ib2screenshots';
				$alt_templates_path = WP_CONTENT_DIR . $ds . 'ib2-templates';
				$overrides = array( 'test_form' => false );
				
				if ( !file_exists($alt_screenshots_path) ) {
					wp_mkdir_p($alt_screenshots_path);
				}

				if ( !file_exists($alt_templates_path) ) {
					wp_mkdir_p($alt_templates_path);
				}
				
				// Import template txts
				if ( $new_templates = ib2_getfile_list($templates_path, array('txt')) ) {
					foreach ( $new_templates as $temp_file ) {
						$temp_file = realpath($temp_file);
						if ( !file_exists($temp_file) ) continue;
						
						$new_name = basename($temp_file);
						$new_file = IB2_PATH . 'templates' . $ds . $new_name;
						$alt_new_file = $alt_templates_path . $ds . $new_name;
						if ( !@rename($temp_file, $new_file) ) {
							@copy($temp_file, $new_file);
							@unlink($temp_file);
						}

						if ( file_exists($new_file) ) {
							@copy($new_file, $alt_new_file);
						}
					}
					ib2_delete_dir(realpath($templates_path));
				}
				
				// Import template images
				if ( $new_images = ib2_getfile_list($images_path, array('jpg', 'gif', 'jpeg', 'png', 'bmp')) ) {
					foreach ( $new_images as $temp_file ) {
						$temp_file = realpath($temp_file);
						if ( !file_exists($temp_file) ) continue;
						
						$new_name = basename($temp_file);
						$new_file = IB2_PATH . 'assets' . $ds . 'img' . $ds . 'templates' . $ds . $new_name;
						if ( !@rename($temp_file, $new_file) ) {
							$result = @copy($temp_file, $new_file);
							@unlink($temp_file);
						}
					}
					ib2_delete_dir(realpath($images_path));
				}
				
				// Import template screenshots
				if ( $new_screenshots = ib2_getfile_list($screenshots_path, array('jpg', 'gif', 'jpeg', 'png', 'bmp')) ) {
					foreach ( $new_screenshots as $temp_file ) {
						$temp_file = realpath($temp_file);
						if ( !file_exists($temp_file) ) continue;
						
						$new_name = basename($temp_file);
						$new_file = IB2_PATH . 'assets' . $ds . 'img' . $ds . 'templates' . $ds . 'screenshots' . $ds . $new_name;
						$alt_screenshots_path = realpath($alt_screenshots_path);
						if ( file_exists($alt_screenshots_path) && is_writable($alt_screenshots_path) ) {
							$alt_file = $alt_screenshots_path . $ds . $new_name;
							if ( !@rename($temp_file, $alt_file) ) {
								@copy($temp_file, $alt_file);
							}
						}
						if ( !@rename($temp_file, $new_file) ) {
							@copy($temp_file, $new_file);
							
							@unlink($temp_file);
						}
					}
					ib2_delete_dir(realpath($screenshots_path));
				}
				
				// Migrate new template data to database...
				$template_file = $folder_path . 'templates-data.txt';
				if ( file_exists($template_file) ) {
					$_ts = @file_get_contents($template_file);
					if ( $_ts && $_ts != '' ) {
						$templates = maybe_unserialize($_ts);
						foreach ( $templates as $template ) {
							$data = array(
								'name' => stripslashes($template['name']),
								'created' => date_i18n("Y-m-d H:i:s"),
								'screenshot' => stripslashes($template['screenshot']),
								'type' => stripslashes($template['type']),
								'tags' => stripslashes($template['tags']),
								'version' => stripslashes($template['version']),
								'subtype' => stripslashes($template['subtype']),
								'mode' => 'default',
								'set' => stripslashes($template['set']),
							);
							
							if ( $t = ib2_get_templatebyname($template['name']) ) {
								unset($data['name'], $data['created']);
								if ( version_compare($t->version, $template['version'], '<') ) {
									// update the metadata
									$file = IB2_PATH . 'templates/' . $t->name . '.txt';
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

					$templateset_file = $folder_path . 'templatesets-data.txt';
					if ( file_exists($templateset_file) ) {
						$_ts = @file_get_contents($templateset_file);
						if ( $_ts ) {
							$sets = maybe_unserialize($_ts);
							foreach ( $sets as $set ) {
								$name = stripslashes($set['name']);
								$chk = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}ib2_templatesets` WHERE `name` = '{$name}'");
								$data = array(
										'launch' => stripslashes($set['launch']),
										'optin' => stripslashes($set['optin']),
										'sales' => stripslashes($set['sales']),
										'webinar' => stripslashes($set['webinar']),
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
					
					@unlink($template_file);
				}
				//remove the dir
				ib2_delete_dir($folder_path);
			} else {
				wp_die("ERROR: Failed to import templates. Please make sure the zip file is not corrupted, and try again.");  
			}
		}
		
		$url = admin_url('admin.php?page=ib2-settings&saved=true');
		wp_redirect($url);
		exit;
	}
}

function ib2_delete_dir( $dir_path ) {
    if ( !is_dir($dir_path) ) {
        return false;
    }
	
    if ( substr($dir_path, strlen($dir_path) - 1, 1) != '/' ) {
        $dir_path .= '/';
    }
    $files = glob($dir_path . '*', GLOB_MARK);
    foreach ( $files as $file ) {
        if ( is_dir($file) ) {
            ib2_delete_dir($file);
        } else {
            @unlink($file);
        }
    }
    @rmdir($dir_path);
}

function ib2_getfile_list( $path, $exts = array() ) {
	$files = array();
	if ( is_dir($path) ) :
		if ( $tmp_dir = opendir($path) ) :
			while ( ( $file = readdir($tmp_dir) ) !== false ) :
				if ( $file == '.' || $file == '..' ) continue;
				
				// check for extension
				$parts = explode(".", $file);
				$ext = strtolower(end($parts));
				
				if ( !empty($exts) && !in_array($ext, $exts) ) continue;
				
				$files[] = $path . $file;
			endwhile;
		endif;
	endif;
	
	return $files;	
}

function ib2_pages_query( $args = array() ) {
	global $wpdb;
	$defaults = array(
		'keyword' => '',
		'status' => '',
		'group_id' => -1,
		'sort' => 'created',
		'order' => 'DESC',
		'start' => 0,
		'limit' => 0
	);
	$args = wp_parse_args( $args, $defaults );
	extract($args, EXTR_SKIP);
	
	$sql = "SELECT * FROM `{$wpdb->prefix}ib2_pages` WHERE 1=1";
	if ( !empty($keyword) ) {
		$keyword = sanitize_text_field($keyword);
		$sql .= " AND `name` LIKE '%{$keyword}%'";
	}
	
	if ( !empty($status) )
		$sql .= " AND `status` = '{$status}'";
	else
		$sql .= " AND `status` <> 'trash'";
	
	if ( $group_id != -1 ) {
		if ( is_array($group_id) ) {
			$group_id = implode(",", $group_id);
			$sql .= " AND `group_id` IN ({$group_id})";
		} else {
			$sql .= " AND `group_id` = {$group_id}";
		}
	}
	
	$sql .= " ORDER BY `{$sort}` {$order}";
	if ( !empty($limit) )
		$sql .= " LIMIT {$start},{$limit}";
	
	if ( !$results = $wpdb->get_results($sql) )
		return false;
	
	return $results;
}

function ib2_groups_query( $args = array() ) {
	global $wpdb;
	$defaults = array(
		'keyword' => '',
		'sort' => 'created',
		'order' => 'DESC',
		'is_funnel' => 0,
		'start' => 0,
		'limit' => 0
	);
	$args = wp_parse_args( $args, $defaults );
	extract($args, EXTR_SKIP);
	
	$sub_query = "SELECT COUNT(ID) FROM `{$wpdb->prefix}ib2_pages` WHERE `group_id` = g.ID";
	$sql = "SELECT g.*, ({$sub_query}) AS totalpages FROM `{$wpdb->prefix}ib2_groups` g WHERE 1=1 AND `is_funnel` = {$is_funnel}";
	if ( !empty($keyword) ) {
		$keyword = sanitize_text_field($keyword);
		$sql .= " AND `name` LIKE '%{$keyword}%'";
	}

	$sql .= " ORDER BY `{$sort}` {$order}";
	if ( !empty($limit) )
		$sql .= " LIMIT {$start},{$limit}";
	
	if ( !$results = $wpdb->get_results($sql) )
		return false;
	
	return $results;
}

function ib2_create_group( $name = '', $is_funnel = 0, $funnel_type = '' ) {
	global $wpdb;
	
	if ( $name == '' ) return 'empty';
	$name = sanitize_text_field($name);
	
	if ( ib2_group_exists(0, $name) )
		return 'exists';
	
	$data = array( 'name' => $name, 'created' => date_i18n("Y-m-d H:i:s") );
	if ( $is_funnel == 1 && !empty($funnel_type) ) {
		$data['is_funnel'] = 1;
		$data['funnel_type'] = sanitize_text_field($funnel_type);
	}
	
	$wpdb->insert( "{$wpdb->prefix}ib2_groups", $data );
	
	return $wpdb->insert_id;
}

function ib2_get_group( $group_id ) {
	global $wpdb;
	
	$group_id = (int) $group_id;
	if ( $group = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}ib2_groups` WHERE `ID` = %d", $group_id)) ) {
		return $group;
	}
	
	return false;
}

function ib2_countries() {
	$countries = array(
		'US'=>'United States',
		'DZ'=>'Algeria',
		'AO'=>'Angola',
		'BJ'=>'Benin',
		'BW'=>'Botswana',
		'BF'=>'Burkina Faso',
		'BI'=>'Burundi',
		'CM'=>'Cameroon',
		'CV'=>'Cape Verde',
		'CF'=>'Central African Republic',
		'TD'=>'Chad',
		'KM'=>'Comoros',
		'CD'=>'Congo [DRC]',
		'CG'=>'Congo [Republic]',
		'DJ'=>'Djibouti',
		'EG'=>'Egypt',
		'GQ'=>'Equatorial Guinea',
		'ER'=>'Eritrea',
		'ET'=>'Ethiopia',
		'GA'=>'Gabon',
		'GM'=>'Gambia',
		'GH'=>'Ghana',
		'GN'=>'Guinea',
		'GW'=>'Guinea-Bissau',
		'CI'=>'Ivory Coast',
		'KE'=>'Kenya',
		'LS'=>'Lesotho',
		'LR'=>'Liberia',
		'LY'=>'Libya',
		'MG'=>'Madagascar',
		'MW'=>'Malawi',
		'ML'=>'Mali',
		'MR'=>'Mauritania',
		'MU'=>'Mauritius',
		'YT'=>'Mayotte',
		'MA'=>'Morocco',
		'MZ'=>'Mozambique',
		'NA'=>'Namibia',
		'NE'=>'Niger',
		'NG'=>'Nigeria',
		'RW'=>'Rwanda',
		'RE'=>'Réunion',
		'SH'=>'Saint Helena',
		'SN'=>'Senegal',
		'SC'=>'Seychelles',
		'SL'=>'Sierra Leone',
		'SO'=>'Somalia',
		'ZA'=>'South Africa',
		'SD'=>'Sudan',
		'SZ'=>'Swaziland',
		'ST'=>'São Tomé and Príncipe',
		'TZ'=>'Tanzania',
		'TG'=>'Togo',
		'TN'=>'Tunisia',
		'UG'=>'Uganda',
		'EH'=>'Western Sahara',
		'ZM'=>'Zambia',
		'ZW'=>'Zimbabwe',
		'AQ'=>'Antarctica',
		'BV'=>'Bouvet Island',
		'TF'=>'French Southern Territories',
		'HM'=>'Heard Island and McDonald Island',
		'GS'=>'South Georgia and the South Sandwich Islands',
		'AF'=>'Afghanistan',
		'AM'=>'Armenia',
		'AZ'=>'Azerbaijan',
		'BH'=>'Bahrain',
		'BD'=>'Bangladesh',
		'BT'=>'Bhutan',
		'IO'=>'British Indian Ocean Territory',
		'BN'=>'Brunei',
		'KH'=>'Cambodia',
		'CN'=>'China',
		'CX'=>'Christmas Island',
		'CC'=>'Cocos [Keeling] Islands',
		'GE'=>'Georgia',
		'HK'=>'Hong Kong',
		'IN'=>'India',
		'ID'=>'Indonesia',
		'IR'=>'Iran',
		'IQ'=>'Iraq',
		'IL'=>'Israel',
		'JP'=>'Japan',
		'JO'=>'Jordan',
		'KZ'=>'Kazakhstan',
		'KW'=>'Kuwait',
		'KG'=>'Kyrgyzstan',
		'LA'=>'Laos',
		'LB'=>'Lebanon',
		'MO'=>'Macau',
		'MY'=>'Malaysia',
		'MV'=>'Maldives',
		'MN'=>'Mongolia',
		'MM'=>'Myanmar [Burma]',
		'NP'=>'Nepal',
		'KP'=>'North Korea',
		'OM'=>'Oman',
		'PK'=>'Pakistan',
		'PS'=>'Palestinian Territories',
		'PH'=>'Philippines',
		'QA'=>'Qatar',
		'SA'=>'Saudi Arabia',
		'SG'=>'Singapore',
		'KR'=>'South Korea',
		'LK'=>'Sri Lanka',
		'SY'=>'Syria',
		'TW'=>'Taiwan',
		'TJ'=>'Tajikistan',
		'TH'=>'Thailand',
		'TR'=>'Turkey',
		'TM'=>'Turkmenistan',
		'AE'=>'United Arab Emirates',
		'UZ'=>'Uzbekistan',
		'VN'=>'Vietnam',
		'YE'=>'Yemen',
		'AL'=>'Albania',
		'AD'=>'Andorra',
		'AT'=>'Austria',
		'BY'=>'Belarus',
		'BE'=>'Belgium',
		'BA'=>'Bosnia and Herzegovina',
		'BG'=>'Bulgaria',
		'HR'=>'Croatia',
		'CY'=>'Cyprus',
		'CZ'=>'Czech Republic',
		'DK'=>'Denmark',
		'EE'=>'Estonia',
		'FO'=>'Faroe Islands',
		'FI'=>'Finland',
		'FR'=>'France',
		'DE'=>'Germany',
		'GI'=>'Gibraltar',
		'GR'=>'Greece',
		'GG'=>'Guernsey',
		'HU'=>'Hungary',
		'IS'=>'Iceland',
		'IE'=>'Ireland',
		'IM'=>'Isle of Man',
		'IT'=>'Italy',
		'JE'=>'Jersey',
		'XK'=>'Kosovo',
		'LV'=>'Latvia',
		'LI'=>'Liechtenstein',
		'LT'=>'Lithuania',
		'LU'=>'Luxembourg',
		'MK'=>'Macedonia',
		'MT'=>'Malta',
		'MD'=>'Moldova',
		'MC'=>'Monaco',
		'ME'=>'Montenegro',
		'NL'=>'Netherlands',
		'NO'=>'Norway',
		'PL'=>'Poland',
		'PT'=>'Portugal',
		'RO'=>'Romania',
		'RU'=>'Russia',
		'SM'=>'San Marino',
		'RS'=>'Serbia',
		'CS'=>'Serbia and Montenegro',
		'SK'=>'Slovakia',
		'SI'=>'Slovenia',
		'ES'=>'Spain',
		'SJ'=>'Svalbard and Jan Mayen',
		'SE'=>'Sweden',
		'CH'=>'Switzerland',
		'UA'=>'Ukraine',
		'GB'=>'United Kingdom',
		'VA'=>'Vatican City',
		'AX'=>'Åland Islands',
		'AI'=>'Anguilla',
		'AG'=>'Antigua and Barbuda',
		'AW'=>'Aruba',
		'BS'=>'Bahamas',
		'BB'=>'Barbados',
		'BZ'=>'Belize',
		'BM'=>'Bermuda',
		'BQ'=>'Bonaire, Saint Eustatius and Saba',
		'VG'=>'British Virgin Islands',
		'CA'=>'Canada',
		'KY'=>'Cayman Islands',
		'CR'=>'Costa Rica',
		'CU'=>'Cuba',
		'CW'=>'Curacao',
		'DM'=>'Dominica',
		'DO'=>'Dominican Republic',
		'SV'=>'El Salvador',
		'GL'=>'Greenland',
		'GD'=>'Grenada',
		'GP'=>'Guadeloupe',
		'GT'=>'Guatemala',
		'HT'=>'Haiti',
		'HN'=>'Honduras',
		'JM'=>'Jamaica',
		'MQ'=>'Martinique',
		'MX'=>'Mexico',
		'MS'=>'Montserrat',
		'AN'=>'Netherlands Antilles',
		'NI'=>'Nicaragua',
		'PA'=>'Panama',
		'PR'=>'Puerto Rico',
		'BL'=>'Saint Barthélemy',
		'KN'=>'Saint Kitts and Nevis',
		'LC'=>'Saint Lucia',
		'MF'=>'Saint Martin',
		'PM'=>'Saint Pierre and Miquelon',
		'VC'=>'Saint Vincent and the Grenadines',
		'SX'=>'Sint Maarten',
		'TT'=>'Trinidad and Tobago',
		'TC'=>'Turks and Caicos Islands',
		'VI'=>'U.S. Virgin Islands',
		'AR'=>'Argentina',
		'BO'=>'Bolivia',
		'BR'=>'Brazil',
		'CL'=>'Chile',
		'CO'=>'Colombia',
		'EC'=>'Ecuador',
		'FK'=>'Falkland Islands',
		'GF'=>'French Guiana',
		'GY'=>'Guyana',
		'PY'=>'Paraguay',
		'PE'=>'Peru',
		'SR'=>'Suriname',
		'UY'=>'Uruguay',
		'VE'=>'Venezuela',
		'AS'=>'American Samoa',
		'AU'=>'Australia',
		'CK'=>'Cook Islands',
		'TL'=>'East Timor',
		'FJ'=>'Fiji',
		'PF'=>'French Polynesia',
		'GU'=>'Guam',
		'KI'=>'Kiribati',
		'MH'=>'Marshall Islands',
		'FM'=>'Micronesia',
		'NR'=>'Nauru',
		'NC'=>'New Caledonia',
		'NZ'=>'New Zealand',
		'NU'=>'Niue',
		'NF'=>'Norfolk Island',
		'MP'=>'Northern Mariana Islands',
		'PW'=>'Palau',
		'PG'=>'Papua New Guinea',
		'PN'=>'Pitcairn Islands',
		'WS'=>'Samoa',
		'SB'=>'Solomon Islands',
		'TK'=>'Tokelau',
		'TO'=>'Tonga',
		'TV'=>'Tuvalu',
		'UM'=>'U.S. Minor Outlying Islands',
		'VU'=>'Vanuatu',
		'WF'=>'Wallis and Futuna'
	);
	ksort($countries);
	return $countries;
}

add_action('admin_head', 'ib2_newpage_url');
function ib2_newpage_url() {
	global $post;
	if ( !(isset($_GET['page']) && $_GET['page'] == (('ib2-dashboard' || 'ib2-funnel')) ) && !isset($post->ID) )
		return;
		
	$name = ( isset($_GET['page']) && $_GET['page'] == 'ib2-funnel' ) ? 'ib2-funnel' : 'ib2-dashboard';
?>
<script type="text/javascript">
	var ib2pageurl = '<?php echo admin_url('admin.php?page=' . $name); ?>',
	ib2editorurl = '<?php echo admin_url('post.php?ib2editor=true'); ?>',
	ib2previewid = <?php echo ib2_get_preview_id(); ?>;
</script>
<?php
}

function ib2_get_preview_id() {
	$posts = get_posts('order=ASC');
	$preview_id = 1;
	$_cached_id = get_transient('_ib2_preview_id');
	if ( $_cached_id ) {
		$preview_id = $_cached_id;
	} else if ( $posts ) {
		foreach ( $posts as $post ) {
			$preview_id = $post->ID;
			break;
		}
		set_transient('_ib2_preview_id', $preview_id, 3600);
	}
	return $preview_id;
}

function ib2_get_pagestats( $post_id = 0, $range = 'all', $subid = '' ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	//$pgviews_sql = "SELECT COUNT(visitorid) FROM `{$wpdb->prefix}ib2_hits` WHERE `post_id` = p.post_id AND `variant` = v.variant";
	$visitrs_sql = "SELECT COUNT(visitorid) FROM `{$wpdb->prefix}ib2_hits` WHERE `post_id` = p.post_id AND `variant` = v.variant";
	$convrsn_sql = "SELECT COUNT(visitorid) FROM `{$wpdb->prefix}ib2_conversions` WHERE `post_id` = p.post_id AND `variant` = v.variant";
	$uvisitrs_sql = "SELECT COUNT(DISTINCT visitorid) FROM `{$wpdb->prefix}ib2_hits` WHERE `post_id` = p.post_id AND `variant` = v.variant";
	
	if ( !empty($subid) ) {
		//$pgviews_sql .= " AND `date` BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$uvisitrs_sql .= " AND `date` BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$visitrs_sql .= " AND `date` BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$convrsn_sql .= " AND `date` BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
	}
	
	if ( $range == 'today' ) {
		//$pgviews_sql .= " AND `date` BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$uvisitrs_sql .= " AND `date` BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$visitrs_sql .= " AND `date` BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$convrsn_sql .= " AND `date` BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
	}
	
	if ( $range == 'yesterday' ) {
		//$pgviews_sql .= " AND `date` BETWEEN date_add(date_sub(curdate(), interval 1 day), interval 1 second) AND CURRENT_DATE";
		$uvisitrs_sql .= " AND `date` BETWEEN date_add(date_sub(curdate(), interval 1 day), interval 1 second) AND CURRENT_DATE";
		$visitrs_sql .= " AND `date` BETWEEN date_add(date_sub(curdate(), interval 1 day), interval 1 second) AND CURRENT_DATE";
		$convrsn_sql .= " AND `date` BETWEEN date_add(date_sub(curdate(), interval 1 day), interval 1 second) AND CURRENT_DATE";
	}
	
	if ( $range == '7 days' ) {
		//$pgviews_sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 7 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$uvisitrs_sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 7 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$visitrs_sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 7 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$convrsn_sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 7 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
	}
	
	if ( $range == '14 days' ) {
		//$pgviews_sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 14 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$uvisitrs_sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 14 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$visitrs_sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 14 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$convrsn_sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 14 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
	}
	
	if ( $range == '30 days' ) {
		//$pgviews_sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 30 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$uvisitrs_sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 30 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$visitrs_sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 30 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
		$convrsn_sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 30 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
	}
	
	if ( is_array($range) && count($range) == 2 ) {
		$start_date = strtotime($range[0]);
		$end_date = strtotime($range[1]);
		$start = date_i18n("Y-m-d H:i:s", $start_date);
		$end = date_i18n("Y-m-d H:i:s", $end_date);
		
		//$pgviews_sql .= " AND `date` BETWEEN '{$start}' AND '{$end}'";
		$visitrs_sql .= " AND `date` BETWEEN '{$start}' AND '{$end}'";
		$convrsn_sql .= " AND `date` BETWEEN '{$start}' AND '{$end}'";
	}
	
	$sql = "SELECT p.*, v.variant, v.weight,
				({$uvisitrs_sql}) AS uvisitors,
				({$visitrs_sql}) AS visitors,
				({$convrsn_sql}) AS conversions
			FROM `{$wpdb->prefix}ib2_pages` p
			LEFT JOIN `{$wpdb->prefix}ib2_variants` v ON (p.post_id = v.post_id)
			WHERE p.post_id = %d";

	
		
	$results = $wpdb->get_results($wpdb->prepare($sql, $post_id));
	return $results;
}

function ib2_get_trafficstats( $post_id = 0, $range = 'today', $type = 'referer', $variant = '', $limit = 0 ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	
	// Traffic sources
	$sql = "SELECT `{$type}`, COUNT(visitorid) AS visitors
		FROM `{$wpdb->prefix}ib2_hits`
		WHERE `post_id` = {$post_id}";
	
	if ( !empty($variant) )
		$sql .= " AND `variant` = '{$variant}'";
		
	if ( $range == 'today' ) {
		$sql .= " AND `date` BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
	}
	
	if ( $range == 'yesterday' ) {
		$sql .= " AND `date` BETWEEN date_add(date_sub(curdate(), interval 1 day), interval 1 second) AND CURRENT_DATE";
	}
	
	if ( $range == '7 days' ) {
		$sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 7 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
	}
	
	if ( $range == '14 days' ) {
		$sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 14 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
	}
	
	if ( $range == '30 days' ) {
		$sql .= " AND `date` BETWEEN (CURRENT_DATE - INTERVAL 30 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)";
	}
	
	if ( is_array($range) && count($range) == 2 ) {
		$start_date = strtotime($range[0]);
		$end_date = strtotime($range[1]);
		$start = date_i18n("Y-m-d H:i:s", $start_date);
		$end = date_i18n("Y-m-d H:i:s", $end_date);
		
		$sql .= " AND `date` BETWEEN '{$start}' AND '{$end}'";
	}
	$sql .= " GROUP BY `{$type}`";
		
	if ( $limit > 0 )
		$sql .= " LIMIT 0,{$limit}";
	
	$results = $wpdb->get_results($sql);
	if ( !$results ) return false;
	
	$query = "SELECT SUM(visitors) AS total_visitors FROM ({$sql}) src";
	$total = $wpdb->get_row($query);
	if ( !$total ) $total_visitors = 0; else $total_visitors = $total->total_visitors;
	
	return array('results' => $results, 'total_visitors' => $total_visitors);
}

add_action('admin_init', 'ib2_integrasi_kunci', 2);
function ib2_integrasi_kunci() {
	if ( isset($_POST['ib2action']) && $_POST['ib2action'] == 'buka_kunci' ) {
		if ( !function_exists('curl_init') )
			wp_die("ERROR: PHP cURL is not installed. IB 2.0 needs PHP cURL to run. Please contact your server admin/support to request the PHP cURL extension.");
		
		$secret_key = 'QpQPzzmxx4xxReo2bB1eP9qKP1lsGyl7';
		$kata_kunci = trim($_POST['ib2_kata_kunci']);
		$md5_hash = md5($kata_kunci . $secret_key);
		$bridge_url = "http://instabuilder.com/v2.0/!/instamember_api/license/{$kata_kunci}";
		$hostname = @gethostbyaddr($_SERVER['SERVER_ADDR']);
		$data = array(
			'domain' => $_SERVER['HTTP_HOST'],
			'userip' => $_SERVER['REMOTE_ADDR'],
			'servip' => ( $hostname ) ? $hostname : $_SERVER['SERVER_ADDR']
		);
		
		$muatan_lokale = ( $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1' ) ? TRUE : FALSE;
		if ( empty($kata_kunci) ) $muatan_lokale = FALSE;
		$result = wp_remote_post($bridge_url, array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( $secret_key . ':' . $md5_hash )
				),
				'body' => $data,
				'cookies' => array()
		    )
		);

		$use_curl = false;
		if ( is_wp_error($result) ) {
			$error_message = $result->get_error_message();
			$use_curl = true;
		} else {
			$response = json_decode($result['body']);
		}
		
		$license_data = false;
		if ( $use_curl && function_exists('curl_init') ) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $bridge_url);
			curl_setopt($ch, CURLOPT_USERPWD, "{$secret_key}:{$md5_hash}");
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 90);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			$result = curl_exec($ch);
			$response = json_decode($result);
		}
		
		if ( (isset($response->status) && $response->status == 'Success') ) {
			// the license status is active...
			// but we still need to check the license data...
			
			$data = array(
				'content' => $kata_kunci,
			);
			
			// grab license data
			$call = wp_remote_get($bridge_url . '/data', array(
					'timeout' => 45,
					'httpversion' => '1.1',
					'headers' => array(
						'Authorization' => 'Basic ' . base64_encode( $secret_key . ':' . $md5_hash )
					)
			    )
			);
			
			$page = ( isset($_POST['ib2pro']) && $_POST['ib2pro'] == 'yes' ) ? 'ib2-funnel' : 'ib2-settings';
			if ( is_wp_error($call) ) {
				wp_die("ERROR: " . $call->get_error_message() . '. Please try again later.');
			} else {
				$license = json_decode($call['body']);
				$activated = ib2_chkdsk();
				$pro_license = false;
				$pro_installed = ( function_exists('ib2f_checker') ) ? true : false;
				$pro_activated = ( function_exists('ib2f_checker') ) ? ib2f_checker() : false;
				
				$product_ids = array(3,9,11,12);
				$pro_ids = array(4,13,15);
				
				// Fix Problem with PRO licence activation...
				if ( in_array($license->data->product_id, $pro_ids) )
					$pro_license = true;
				
				if ( stristr('pro', strtolower($license->data->product_title)) )
					$pro_license = true;
				
				if ( $page == 'ib2-funnel' && $pro_installed && $pro_license ) {
					update_option('ib2f_checker', $data);
					@header( 'Location: ' . admin_url('admin.php?page=' . $page));
					exit;
				} else {
					if ( $activated ) {
						@header( 'Location: ' . admin_url('admin.php?page=' . $page));
						exit;
					}
					
					if ( !$pro_license ) {
						$is_developer = false;
						if ( in_array($license->data->product_id, $product_ids) )
							$is_developer = true;
						
						if ( !$is_developer && stristr('developer', strtolower($license->data->product_title)) )
							$is_developer = true;
						
						if ( $is_developer ) {
							$data['type'] = 'developer';
						}
						
						update_option('ib2_kunci', $data);
						$msg = '&activation=true';
					} else {
						$msg = '&activation=failed';
					}
					@header( 'Location: ' . admin_url('admin.php?page=' . $page). $msg);
				}
			}
		} else {
			$msg = '&activation=failed';
			@header( 'Location: ' . admin_url('admin.php?page=' . $page). $msg);
		}
		exit;
	}	
}

function ib2_chkdsk() {
	$kunci = get_option('ib2_kunci');
	if ( is_array($kunci) && !empty($kunci['content']) ) {
		return stripslashes($kunci['content']);
	}
	
	return false;
}

function ib2_cr( $t ) { 
    return @($t[1]/$t[0]); 
}

function ib2_zscore( $c, $t ) {
    $z = ib2_cr($t) - ib2_cr($c);
    $s = @((ib2_cr($t) * (1 - ib2_cr($t))) / $t[0] + (ib2_cr($c) * (1 - ib2_cr($c))) / $c[0]);
    return @($z/sqrt($s));
}

function ib2_cumnormdist( $x ) {
  $b1 =  0.319381530;
  $b2 = -0.356563782;
  $b3 =  1.781477937;
  $b4 = -1.821255978;
  $b5 =  1.330274429;
  $p  =  0.2316419;
  $c  =  0.39894228;

  if ( $x >= 0.0 ) {
      $t = 1.0 / ( 1.0 + $p * $x );
      return @(1.0 - $c * exp( -$x * $x / 2.0 ) * $t * ( $t *( $t * ( $t * ( $t * $b5 + $b4 ) + $b3 ) + $b2 ) + $b1 ));
  }
  else {
      $t = 1.0 / ( 1.0 - $p * $x );
      return @( $c * exp( -$x * $x / 2.0 ) * $t * ( $t *( $t * ( $t * ( $t * $b5 + $b4 ) + $b3 ) + $b2 ) + $b1 ));
    }
}

function ib2_ssize( $conv ) {
    $a = 3.84145882689; 
    $res = array();
    $bs = array(0.0625, 0.0225, 0.0025);
    foreach ( $bs as $b ) {
        $res[] = (int) @( ( 1 - $conv ) * $a / ($b * $conv) );
    }
    return $res;
}

function ib2_total_pages( $status = 'all' ) {
	global $wpdb;
	
	$sql = "SELECT COUNT(*) FROM `{$wpdb->prefix}ib2_pages`";
	if ( $status != '' && $status != 'all' )
		$sql .= " WHERE `status` = '{$status}'";
	else
		$sql .= " WHERE `status` <> 'trash'";
	
	return $wpdb->get_var($sql);	
}

function ib2_total_visits() {
	global $wpdb;
	
	$total = get_transient('ib2_total_visits');
	if ( !$total || $total === FALSE ) {
		$sql = "SELECT COUNT(visitorid) FROM `{$wpdb->prefix}ib2_hits`";
		$total = number_format($wpdb->get_var($sql));
		set_transient('ib2_total_visits', $total, 900);
	}
	return $total;
}

function ib2_total_unique_visitors() {
	global $wpdb;
	
	$total = get_transient('ib2_total_unique_visitors');
	if ( !$total || $total === FALSE ) {
		$sql = "SELECT COUNT(DISTINCT visitorid) FROM `{$wpdb->prefix}ib2_hits`";
		$total = number_format($wpdb->get_var($sql));
		set_transient('ib2_total_unique_visitors', $total, 900);
	}
	return $total;
}

function ib2_biweekly_visitors() {
	global $wpdb;
	
	$sql = "SELECT DATE(date) As v_date,
			COUNT(DISTINCT visitorid) AS visitors
			FROM `{$wpdb->prefix}ib2_hits`
			WHERE `date` BETWEEN (CURRENT_DATE - INTERVAL 14 DAY) AND (CURRENT_DATE + INTERVAL 1 DAY - INTERVAL 1 SECOND)
			GROUP BY DATE(date)";
			
	return $wpdb->get_results($sql);
}

function ib2_new_page() {
	$template_id = $_GET['template_id'];
	$group_id = ( isset($_GET['group_id']) ) ? $_GET['group_id'] : 0;
	
	$title = 'New IB 2.0 Landing Page - ' . date_i18n("Y-m-d H:i:s");
	if ( $template_id != 'scratch' ) {
		if ( $temp = ib2_get_template($template_id) ) {
			$title = esc_attr($temp->name);
		}
	}
	
	if ( $group_id > 0 ) {
		if ( $group = ib2_get_group( $group_id ) ) {
			$title = '[' . esc_attr($group->name) . '] ' . $title;
		}
	}
	
	// Create post object
	$page = array(
		'post_title'    => $title,
	  	'post_content'  => '',
	  	'post_status'   => 'draft',
	  	'post_author'   => get_current_user_id(),
	  	'post_type' 	=> 'page'
	);

	// Insert the post into the database
	$post_id = wp_insert_post($page);
	
	if ( $group_id > 0 ) {
		ib2_page_entry($post_id, $group_id);
	}
	
	$editor_url = admin_url('post.php?post=' . $post_id . '&action=edit&ib2editor=true');
	if ( $template_id != 'scratch' )
		$editor_url .= '&template_id=' . $template_id;
	
	wp_redirect($editor_url);
	exit;
}

function ib2_clone_page( $post_id, $group_id = 0 ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	$group_id = (int) $group_id;
	$post = get_post($post_id);
	
	if ( !$post )
		wp_die("ERROR: There's no post to duplicate.");
	
	$new_post = array(
		'menu_order' => $post->menu_order,
		'comment_status' => $post->comment_status,
		'ping_status' => $post->ping_status,
		'post_author' => get_current_user_id(),
		'post_content' => $post->post_content,
		'post_mime_type' => $post->post_mime_type,
		'post_parent' => $post->post_parent,
		'post_password' => $post->post_password,
		'post_status' => $post->post_status,
		'post_title' => '[COPY]' . $post->post_title . ' - ' . date_i18n("Y-m-d H:i:s"),
		'post_type' => $post->post_type,
	);
	
	$new_post_id = wp_insert_post($new_post);
	
	if ( $post->post_status == 'publish' || $post->post_status == 'future' ) {
		$new_slug = wp_unique_post_slug($post->post_name, $new_post_id, $post->post_status, $post->post_type, $post->post_parent);
	
		$new_post = array();
		$new_post['ID'] = $new_post_id;
		$new_post['post_name'] = $new_slug;

		// Update the post into the database
		wp_update_post($new_post);
	}
	
	// Process Taxonomies
	if ( isset($wpdb->terms) ) {
		wp_set_object_terms( $new_post_id, NULL, 'category' );

		$post_taxonomies = get_object_taxonomies($post->post_type);
		if ( $post_taxonomies ) {
			foreach ( $post_taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms($post->ID, $taxonomy, array( 'orderby' => 'term_order' ));
				$terms = array();
				for ( $i = 0; $i < count($post_terms); $i++ ) {
					$terms[] = $post_terms[$i]->slug;
				}
				wp_set_object_terms($new_post_id, $terms, $taxonomy);
			}
		}
	}
	
	// Process Post Meta
	$post_meta_keys = get_post_custom_keys($post->ID);
	if ( !empty($post_meta_keys) ) {
		foreach ( $post_meta_keys as $meta_key ) {
			$meta_values = get_post_custom_values($meta_key, $post->ID);
			foreach ( $meta_values as $meta_value ) {
				$meta_value = maybe_unserialize($meta_value);
				update_post_meta($new_post_id, $meta_key, $meta_value);
			}
		}
	}
	
	// Duplicate entry in IB 2.0 database...
	if ( ib2_page_exists($post->ID) ) {
		
		// register post/page as IB 2.0 page
		@ib2_page_entry($new_post_id, $group_id);
		
		if ( $group = ib2_get_group($group_id) ) {
			if ( $group->is_funnel == 1 ) {
				if ( $funnel = ib2f_funnel_data($post->ID, $group_id) ) {
					$wpdb->insert("{$wpdb->prefix}ib2_funnel", array('post_id' => $new_post_id, 'group_id' => $group_id, 'page_type' => $funnel->page_type, 'template_id' => $funnel->template_id));
				}
			}
		}
		
		// register variants...
		if ( $variants = ib2_get_variants($post->ID) ) {
			foreach ( $variants as $var ) {
				$args = array(
					'post_id' => $new_post_id,
					'variant' => $var->variant,
					'weight' => $var->weight,
					'status' => $var->status,
					'quota' => 0
				);
				
				@ib2_variant_entry($args);
			}
		}
	}
	
	return $new_post_id;
}

function ib2_clone_group( $group_id = 0 ) {
	$group_id = (int) $group_id;
	if ( empty($group_id) )
		return false;
	
	if ( $gr = ib2_get_group($group_id) ) {
		$group_name = ib2_check_group_name('[COPY] ' . esc_attr($gr->name));
		$new_group_id = ib2_create_group($group_name);
		
		if ( $pages = ib2_pages_query('group_id=' . $group_id) ) {
			foreach ( $pages as $page ) {
				@ib2_clone_page($page->post_id, $new_group_id);
			}
		}
		return $new_group_id;
	}
	return false;	
}

function ib2_check_group_name( $name ) {
	$new_name = $name;
	if ( ib2_group_exists(0, $new_name) ) {
		$i = 2;
		do {
			$_new_name = $new_name . ' ' . $i;
			$i++;
		} while( ib2_group_exists(0, $_new_name) );
		$new_name = $_new_name;
	}
	return $new_name;
}

function ib2_group_exists( $group_id = 0, $name = '' ) {
	global $wpdb;
	
	$group_id = (int) $group_id;
	if ( empty($group_id) && empty($name) )
		return false;
	
	$field = 'ID';
	if ( is_numeric($name) ) {
		$group_id = $name;
		$name = '';	
	}
	
	if ( !empty($name) ) {
		$field = 'name';
		$value = $name;
	}
	
	if ( $group_id > 0 ) {
		$field = 'ID';
		$value = $group_id;
	}
	
	$chk = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}ib2_groups` WHERE `{$field}` = %s", $value));
	if ( $chk > 0 )
		return true;
	
	return false;
}

function ib2_group_add_page( $post_id, $group_id = 0 ) {
	if ( empty($group_id) ) {
		wp_die("ERROR: Please select a group.");
	}
	
	@ib2_page_entry($post_id, $group_id);
}

function ib2_group_remove_page( $post_id ) {
	@ib2_page_entry($post_id, 0);
}

function ib2_delete_group( $group_id ) {
	global $wpdb;
	
	$group_id = (int) $group_id;
	if ( empty($group_id) )
		return false;
	
	if ( $pages = ib2_pages_query('group_id=' . $group_id) ) {
		foreach ( $pages as $page ) {
			@ib2_delete_page($page->post_id);
		}
	}
	
	$wpdb->delete("{$wpdb->prefix}ib2_groups", array('ID' => $group_id));
	
	return true;
}

function ib2_delete_groups() {
	if ( isset($_POST['group']) && count($_POST['group']) > 0 ) {
		foreach ( $_POST['group'] as $group_id ) {
			@ib2_delete_group($group_id);
		}
		return true;
	}
	return false;
}
 
add_action('trashed_post', 'ib2_trash_page');
function ib2_trash_page( $post_id ) {
	global $wpdb;
	$wpdb->update("{$wpdb->prefix}ib2_pages", array('status' => 'trash'), array('post_id' => $post_id));
}

add_action('untrashed_post', 'ib2_untrash_page');
function ib2_untrash_page( $post_id ) {
	global $wpdb;
	$post = get_post($post_id);
	if ( !$post ) return;
	$wpdb->update("{$wpdb->prefix}ib2_pages", array('status' => $post->post_status), array('post_id' => $post_id));
}

function ib2_publish_page( $post_id ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	$wp_post = array(
			'ID' => $post_id,
			'post_status' => 'publish'
		);
		
	$result = wp_update_post($wp_post, true);
	
	if ( is_wp_error($result) )
		wp_die("ERROR: " . $result->get_error_message());
	
	$wpdb->update("{$wpdb->prefix}ib2_pages", array('status' => 'publish'), array('post_id' => $post_id));
	
	return true;
}

function ib2_unpublish_page( $post_id ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	$wp_post = array(
			'ID' => $post_id,
			'post_status' => 'draft'
		);
		
	$result = wp_update_post($wp_post, true);
	
	if ( is_wp_error($result) )
		wp_die("ERROR: " . $result->get_error_message());
	
	$wpdb->update("{$wpdb->prefix}ib2_pages", array('status' => 'draft'), array('post_id' => $post_id));
	
	return true;
}

function ib2_delete_pages() {
	if ( isset($_POST['page']) && count($_POST['page']) > 0 ) {
		foreach ( $_POST['page'] as $post_id ) {
			@ib2_delete_page($post_id);
		}
		return true;
	}
	return false;
}

add_action('deleted_post', 'ib2_sync_delete_page');
function ib2_sync_delete_page( $post_id ) {
	ib2_delete_page($post_id, false);
}

function ib2_delete_page( $post_id, $wp_delete = true ) {
	if ( !ib2_page_exists($post_id) )
		return false;
	
	global $wpdb;
	
	// delete from quiz stats
	$wpdb->delete("{$wpdb->prefix}ib2_quiz_stats", array('post_id' => $post_id));
	
	// delete from quiz questions
	$wpdb->delete("{$wpdb->prefix}ib2_quiz_questions", array('post_id' => $post_id));
	
	// delete from quiz entry
	$wpdb->delete("{$wpdb->prefix}ib2_quizzes", array('post_id' => $post_id));
	
	// delete from conversions
	$wpdb->delete("{$wpdb->prefix}ib2_conversions", array('post_id' => $post_id));
	
	// delete from hits
	$wpdb->delete("{$wpdb->prefix}ib2_hits", array('post_id' => $post_id));
	
	// delete from variants
	$wpdb->delete("{$wpdb->prefix}ib2_variants", array('post_id' => $post_id));
	
	// delete from page
	$wpdb->delete("{$wpdb->prefix}ib2_pages", array('post_id' => $post_id));
	
	// delete from page
	$wpdb->delete("{$wpdb->prefix}ib2_funnel", array('post_id' => $post_id));
	
	if ( $wp_delete ) {
		wp_delete_post($post_id, true);
	}
	
	return true;
}

function ib2_templatesets_js( $varname = 'sets' ) {
	$ts = ib2_get_templatesets('optin');
	if ( $ts ) {
		echo "var optin = '';\n";
		foreach ( $ts as $t ) {
			echo 'optin += \'<option value="' . ib2_esc($t->name) . '">' . ib2_esc($t->title) . '</option>\'' . "\n";
		}
		echo "sets.optin = optin;\n\n";
	}

	$ts = ib2_get_templatesets('sales');
	if ( $ts ) {
		echo "var sales = '';\n";
		foreach ( $ts as $t ) {
			echo 'sales += \'<option value="' . ib2_esc($t->name) . '">' . ib2_esc($t->title) . '</option>\'' . "\n";
		}
		echo "sets.sales = sales;\n\n";
	}
	
	$ts = ib2_get_templatesets('webinar');
	if ( $ts ) {
		echo "var webinar = '';\n";
		foreach ( $ts as $t ) {
			echo 'webinar += \'<option value="' . ib2_esc($t->name) . '">' . ib2_esc($t->title) . '</option>\'' . "\n";
		}
		echo "sets.webinar = webinar;\n\n";
	}
	
	$ts = ib2_get_templatesets('launch');
	if ( $ts ) {
		echo "var launch = '';\n";
		foreach ( $ts as $t ) {
			echo 'launch += \'<option value="' . ib2_esc($t->name) . '">' . ib2_esc($t->title) . '</option>\'' . "\n";
		}
		echo "sets.launch = launch;\n\n";
	}
}

function ib2_get_templatesets( $type = 'optin' ) {
	global $wpdb;
	$result = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}ib2_templatesets` WHERE `{$type}` = 'yes' ORDER BY `name` ASC");
	return $result;
}

add_action('wp_ajax_ib2_templateset_data', 'ib2_ajax_templateset_data');
function ib2_ajax_templateset_data() {
	if ( isset($_REQUEST['page_type']) ) {
		global $wpdb;
		
		$page_type = stripslashes($_REQUEST['page_type']);
		$sets = stripslashes($_REQUEST['sets']);
		$_pg = $page_type;
		$subtype = '';
		
		if ( $page_type == 'upsell' || $page_type == 'downsell' ) {
			$page_type = 'sales';
			$subtype = 'otosales';
		}
		
		if ( $page_type == 'webinar' ) $subtype = 'webinarsignup';
		if ( $page_type == 'webinar_thanks' ) {
			$page_type = 'webinar';
			$subtype = 'webinarthanks';
		}
		if ( $page_type == 'confirmation' || $page_type == 'thank_you' || $page_type == 'download' ) {
			$page_type = 'others';
			if ( $_pg == 'thank_you' ) $subtype = 'thankyou'; else $subtype = $_pg;
		}
		
		if ( $page_type == 'launch_video' ) $page_type = 'launch';
		
		$sql = "SELECT * FROM `{$wpdb->prefix}ib2_templates` WHERE `set` = '{$sets}' AND `type` = '{$page_type}'";
		
		if ( !empty($subtype) )
			$sql .= " AND `subtype` = '{$subtype}'";
		else if ( empty($subtype) && $page_type == 'sales' )
			$sql .= " AND `subtype` <> 'otosales'";
		
		$row = $wpdb->get_row($sql);
		if ( !$row ) {
			$response = array( 'success' => false );
			wp_send_json_error($response);
		}
		
		$plugin_url = IB2_URL;
		$response = array(
			'success' => true,
			'img' => str_replace("{%IB2_PLUGIN_URL%}", $plugin_url, stripslashes($row->screenshot)),
			'template_id' => $row->ID
		);
		
		wp_send_json($response);
	}
	die();
}

function ib2_change_template() {
	global $wpdb;
	
	$template_id = (int) $_GET['template_id'];
	$variant = $_GET['variant'];
	$post_id = (int) $_GET['post_id'];
	
	$meta = get_post_meta($post_id, 'ib2_settings', true);
	if ( !is_array($meta) )
		$meta = array();
	
	if ( !$template = ib2_get_template($template_id) )
		wp_die("ERROR: Cannot find template.");
	
	if ( empty($template->metadata) ) {
		// Process template content from file
		$file = IB2_PATH . 'templates/' . $template->name . '.txt';
		if ( !file_exists($file) )
			$file = WP_CONTENT_DIR . '/ib2-templates/' . $template->name . '.txt';

		$content = '';
		if ( file_exists($file) ) {
			$content = @file_get_contents($file);
			$wpdb->update("{$wpdb->prefix}ib2_templates", array('metadata' => $content), array('ID' => $template->ID));
		}
		$data = maybe_unserialize($content);
	} else {
		$data = maybe_unserialize($template->metadata);
	}
	
	$meta['variation' . $variant] = $data['variationa'];
	
	update_post_meta($post_id, 'ib2_settings', $meta);
	
	$url = admin_url('post.php?post=' . $post_id . '&action=edit&ib2editor=true&variant=' . $variant);
	wp_redirect($url);
}

function ib2_process_gtw( $code = '' ) {
	if ( empty($code) )
		wp_die('ERROR: Invalid Return Code.');
	
	$options = get_option('ib2_options');
	if ( empty($options['gtw']) )
		wp_die('ERROR: Citrix Consumer Key Not Found.');
	
	$api_key = stripslashes($options['gtw']);
	$code = urldecode($code);
	
	$url = "https://api.citrixonline.com/oauth/access_token?grant_type=authorization_code&code={$code}&client_id={$api_key}";
	
	$result = wp_remote_get($url, array('timeout' => 300, 'httpversion' => '1.1', 'sslverify' => false));
	if ( is_wp_error($result) ) {
		wp_die('ERROR: ' . $result->get_error_message());
	}
	
	$body = ( isset($result['body']) ) ? json_decode($result['body']) : array();
	$body = (array) $body;
	$body['date'] = time();
	
	if ( isset($result['response']['code']) && $result['response']['code'] == 200 ) {
		update_option('ib2_gotowebinar', $body);
		wp_redirect(admin_url('admin.php?page=ib2-settings&gtw=connected'));
	} else {
		wp_redirect(admin_url('admin.php?page=ib2-settings&gtw=failed'));
	}
	
	exit;
}
	
function ib2_new_variant( $post_id, $mode, $template_id = 0, $oldvar = 'a' ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	$template_id = (int) $template_id;
	
	$letters = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,1,2,3,4,5,6,7,8,9';
	$letters = explode(",", $letters);
	$meta = get_post_meta($post_id, 'ib2_settings', true);
	if ( !is_array($meta) ) {
		$meta = array();
		$meta['enable'] = 'yes';
	}
	
	$nv = 'a';
	foreach ( $letters as $l ) {
		if ( !isset($meta['variation' . $l]) ) {
			$nv = $l;
			break;
		}
	}
	
	if ( $mode == 'scratch' ) {
		$meta['variation' . $nv] = array();
	} else if ( $mode == 'template' ) {
		$tid = $template_id;
		if ( $template = ib2_get_template($tid) ) {
			if ( empty($template->metadata) ) {
				// Process template content from file
				$file = IB2_PATH . 'templates/' . $template->name . '.txt';
				if ( !file_exists($file) )
					$file = WP_CONTENT_DIR . '/ib2-templates/' . $template->name . '.txt';

				$content = '';
				if ( file_exists($file) ) {
					$content = @file_get_contents($file);
					$wpdb->update("{$wpdb->prefix}ib2_templates", array('metadata' => $content), array('ID' => $template->ID));
				}
				$data = maybe_unserialize($content);
			} else {
				$data = maybe_unserialize($template->metadata);
			}
				
			if ( isset($data['variationa']) ) {
				$meta['variation' . $nv] = $data['variationa'];
			} else {
				$meta['variation' . $nv] = array();
			}
		} else {
			wp_die("ERROR: Cannot find template ID {$tid}.");
			//$meta['variation' . $nv] = array();
		}
	} else if ( $mode == 'duplicate' ) {
		$ov = $oldvar;
		if ( isset($meta['variation' . $ov]) ) {
			$meta['variation' . $nv] = $meta['variation' . $ov];
		} else {
			$meta['variation' . $nv] = array();
		}
	}
	
	// register the new variant into IB2.0 database
	ib2_variant_entry("post_id={$post_id}&variant={$nv}");
		
	update_post_meta($post_id, 'ib2_settings', $meta);
	
	return $nv;
}

function ib2_new_variant_ext() {
	$post_id = (int) $_GET['post_id'];
	$mode = $_GET['mode'];
	$template_id = ( isset($_GET['template_id']) ) ? (int) $_GET['template_id'] : 0;
	$page = $_GET['page'];
	$group_id = ( isset($_GET['group_id']) ) ? (int) $_GET['group_id'] : 0;
	
	$nv = ib2_new_variant($post_id, $mode, $template_id);
	if ( $page == 'ib2-funnel' ) 
		$url = admin_url('admin.php?page=' . $page . '&new_variant=true&mode=edit&group_id=' . $group_id . '&post_id=' . $post_id . '&variant=' . $nv);
	else
		$url = admin_url('admin.php?page=' . $page . '&new_variant=true&post_id=' . $post_id . '&variant=' . $nv);
	wp_redirect($url);
	exit;
}

function ib2_default_funnel_template() {
	global $wpdb;
	
	$post_id = (int) $_GET['post_id'];
	$group_id = (int) $_GET['group_id'];
	$template_id = (int) $_GET['template_id'];
	if ( $template = ib2_get_template($template_id) ) {
		if ( empty($template->metadata) ) {
			// Process template content from file
			$file = IB2_PATH . 'templates/' . $template->name . '.txt';
			if ( !file_exists($file) )
				$file = WP_CONTENT_DIR . '/ib2-templates/' . $template->name . '.txt';

			$content = '';
			if ( file_exists($file) ) {
				$content = @file_get_contents($file);
				$wpdb->update("{$wpdb->prefix}ib2_templates", array('metadata' => $content), array('ID' => $template->ID));
			}
			$data = maybe_unserialize($content);
		} else {
			$data = maybe_unserialize($template->metadata);
		}
		
		$meta = $data;
		$meta['enable'] = 'yes';
			
		update_post_meta($post_id, 'ib2_settings', $meta);
		$url = admin_url('admin.php?page=ib2-funnel&new_template=true&mode=edit&group_id=' . $group_id . '&post_id=' . $post_id . '&variant=a');
		wp_redirect($url);
	} else {
		wp_die("ERROR: Template does not exists. Please go back and choose another template.");
	}
	exit;
}

function ib2_funnel_export_html() {
	$group_id = (int) $_GET['group_id'];
	$group = ib2_get_group($group_id);
	$pages = ib2_pages_query('sort=ID&order=ASC&group_id=' . $group_id);
	$funnel_name = str_replace(" ", "-", ib2_esc($group->name));
	
	if ( !$pages )
		wp_die("ERROR: Page not found.");
	
	$timeout_seconds = 30;
	$newfiles = array();
	foreach ( $pages as $page ) {
		if ( $page->status != 'publish' ) continue;
		
		$page_type = ib2f_get_pagetype($page->post_id, $group_id);
		$url = esc_url_raw(add_query_arg( array('ib2mode' => 'save_html', 'export' => 'true', 'type' => $page_type), get_permalink($page->post_id)));
		$response = wp_remote_get($url, array( 'timeout' => $timeout_seconds, 'httpversion' => '1.1' ));
		
		if ( is_wp_error($response) ) {
			wp_die("ERROR: Cannot retrieve page content.");
			break;
		}
		
		$result = $response['body'];
		if ( $result == 'Unable to open file!' ) {
			$path = IB2_PATH . 'cache/';
			wp_die("ERROR: {$path} is not writable.");
			break;
		}
		$newfiles[] = $result;
	}
	
	if ( empty($newfiles) ) {
		wp_die("ERROR: Failed to export. Please check if all pages has been published.");
	}
	
	require_once(IB2_INC . 'wp-zip-generator/class-wp_zip_generator.php');
	
	//create the generator
	$zip_generator = new WP_Zip_Generator(array(
	    'name'                 => 'instabuilder-2-0-funnel',
	    'process_extensions'   => array('html'),
	    'exclude_files'        => array('.git', '.svn', '.DS_Store', '.gitignore', '.', '..', '.htaccess'),
	    'source_directory'     => IB2_PATH . 'cache/',
	    'zip_root_directory'   => "{$funnel_name}-funnel-pages",
	    'download_filename'    => "{$funnel_name}-funnel.zip",
	));
	
	//generate the zip file
	$zip_generator->generate();
	
	foreach ( $newfiles as $f ) {
		@unlink($f);
	}
	
	//download it to the client
	$zip_generator->send_download_headers();
	
	exit;
}

function ib2_remove_license() {
	$nonce = $_REQUEST['_ib2nonce'];
	if ( !wp_verify_nonce($nonce, 'ib2-license') )
    	wp_die('ERROR: Invalid security check code.');

    delete_option('ib2_kunci');
    
    $url = admin_url('admin.php?page=ib2-settings');
	wp_redirect($url);
	exit;
}

function ib2_remove_pro_license() {
	$nonce = $_REQUEST['_ib2prononce'];
	if ( !wp_verify_nonce($nonce, 'ib2-pro-license') )
    	wp_die('ERROR: Invalid security check code.');

    delete_option('ib2f_checker');
    
    $url = admin_url('admin.php?page=ib2-settings');
	wp_redirect($url);
	exit;
}

add_action('admin_init', 'ib2_save_actions');
function ib2_save_actions() {
	global $wpdb;
	if ( isset($_REQUEST['ib2action']) ) {
		$action = $_REQUEST['ib2action'];
		switch ( $action ) {
			case 'import_file':
				$post_id = intval($_POST['import_post_id']);
				$files = $_FILES["import"];
				$allowedExts = array("ib2");
				$temp = explode(".", $files["name"]);
				$extension = end($temp);
				if ( $files["error"] > 0 ) {
					wp_die("ERROR: Failed to upload file.");
				}
				
				if ( !in_array($extension, $allowedExts) ) {
					wp_die("ERROR: Extension not allowed. Please upload the correct IB 2.0 landing page file.");
				}
				
				if ( move_uploaded_file($files["tmp_name"], IB2_PATH . "cache/{$files['name']}") ) {
					$content = @file_get_contents(IB2_PATH . "cache/{$files['name']}");
					$meta = maybe_unserialize($content);
					
					@unlink(IB2_PATH . "cache/{$files['name']}");
					if ( is_array($meta) && isset($meta['enable']) ) {
						if ( empty($post_id) ) {
							// Create post object
							$page = array(
								'post_title'    => 'Imported Page - ' . date("Y-m-d H:i:s"),
							  	'post_content'  => '',
							  	'post_status'   => 'draft',
							  	'post_author'   => get_current_user_id(),
							  	'post_type' 	=> 'page'
							);
						
							// Insert the post into the database
							$post_id = wp_insert_post($page);
							ib2_page_entry($post_id);
						} else {
							// Delete old variants...
							$variants = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}ib2_variants` WHERE `post_id` = {$post_id}");
							if ( $variants ) {
								foreach ( $variants as $var ) {
									ib2_delete_variant_entry($post_id, $var->variant);
								}
							}
						}
						
						update_post_meta($post_id, 'ib2_settings', $meta);
						
						// register new variant
						foreach ( array_keys($meta) as $key ) {
							if ( stristr($key, 'variation') ) {
								$letter = str_replace('variation', '', $key);
								$variant = ib2_variant_entry("post_id={$post_id}&variant={$letter}");
							}
						}
						
						// redirect to editor
						$url = admin_url('post.php?post=' . $post_id . '&action=edit&ib2editor=true');
						wp_redirect($url);
					} else {
						wp_die("ERROR: Please upload the correct IB 2.0 file.");
					}
					exit;
				} else {
					wp_die("ERROR: Failed to import file.");
				}
				
				break;
				
			case 'export_file':
				$post_id = ( isset($_GET['post_id']) ) ? intval($_GET['post_id']) : 0;
				if ( empty($post_id) )
					wp_die("ERROR: Post ID Not Found.");
				
				$meta = get_post_meta($post_id, 'ib2_settings', true);
				$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
				if ( !$enable )
					wp_die("ERROR: This is NOT an IB 2.0 landing page.");
				
				$content = maybe_serialize($meta);
				
				header('Content-type: text/plain');
				header('Content-disposition: attachment; filename=ib20-export-' . $post_id . '-' . time() . '.ib2');
				
				echo $content;
				
				exit;
				break;
				
			case 'gtw_token':
				$code = ( isset($_GET['code']) ) ? $_GET['code'] : '';
				ib2_process_gtw($code);
				break;
				
			case 'remove_license':
				ib2_remove_license();
				break;
				
			case 'remove_pro_license':
				ib2_remove_pro_license();
				break;
				
			case 'ib2_create_group':
				$group = ib2_create_group($_POST['new_group_name']);
				$msg =( !is_numeric($group) ) ? '&group_error=' . $group : '&group_created=true';
				$url = admin_url('admin.php?page=ib2-dashboard') . $msg;
				wp_redirect($url);
				break;
				
			case 'funnel_html_export':
				ib2_funnel_export_html();
				break;
				
			case 'default_funnel_template':
				ib2_default_funnel_template();
				break;
				
			case 'new_variant_ext':
				ib2_new_variant_ext();
				break;
				
			case 'change_template':
				ib2_change_template();
				break;
				
			case 'newpage':
				ib2_new_page();
				break;
				
			case 'clone_page':
				$post_id = (int) $_GET['post_id'];
				$group_id = isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0;
				$new_id = ib2_clone_page($post_id, $group_id);
				$page = isset($_GET['page']) ? $_GET['page'] : 'ib2-dashboard';
				$mode = ( $page == 'ib2-funnel' ) ? 'edit' : 'group';
				if ( $group_id > 0 )
					$url = admin_url('admin.php?page=' . $page . '&mode=' . $mode . '&group_id=' . $group_id . '&new_post_id=' . $new_id);
				else
					$url = admin_url('admin.php?page=' . $page . '&new_post_id=' . $new_id);
				wp_redirect($url);
				break;
				
			case 'clone_group':
				$group_id = isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0;
				$page = isset($_GET['page']) ? $_GET['page'] : 'ib2-dashboard';
				$new_id = ib2_clone_group($group_id);
				if ( !$new_id )
					$url = admin_url('admin.php?page=' . $page);
				else
					$url = admin_url('admin.php?page=' . $page . '&mode=group&group_id=' . $new_id);
				wp_redirect($url);
				break;
				
			case 'addgroup':
				$post_id = (int) $_POST['the_post_id'];
				$group_id = (int) $_POST['the_group_id'];
				@ib2_group_add_page($post_id, $group_id);
				$url = admin_url('admin.php?page=ib2-dashboard&mode=group&group_id=' . $group_id . '&new_post_id=' . $post_id);
				wp_redirect($url);
				break;
				
			case 'delete_group':
				$group_id = isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0;
				$page = isset($_GET['page']) ? $_GET['page'] : 'ib2-dashboard';
				@ib2_delete_group($group_id);
				$url = admin_url('admin.php?page=' . $page . '&group_deleted=true');
				wp_redirect($url);
				break;
				
			case 'delete_groups':
				$result = ib2_delete_groups();
				$page = isset($_GET['page']) ? $_GET['page'] : 'ib2-dashboard';
				$msg = ( $result ) ? '&groups_deleted=true' : '';
				$url = admin_url('admin.php?page=' . $page . '' . $msg);
				wp_redirect($url);
				break;
				
			case 'unpublish_page':
				$post_id = (int) $_GET['post_id'];
				$group_id = isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0;
				
				$page = isset($_GET['page']) ? $_GET['page'] : 'ib2-dashboard';
				$mode = ( $page == 'ib2-funnel' ) ? 'edit' : 'group';
				@ib2_unpublish_page($post_id);
				if ( $group_id > 0 )
					$url = admin_url('admin.php?page=' . $page . '&mode=' . $mode . '&group_id=' . $group_id);
				else
					$url = admin_url('admin.php?page=ib2-dashboard');
				wp_redirect($url);
				break;
				
			case 'publish_page':
				$post_id = (int) $_GET['post_id'];
				$group_id = isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0;
				$page = isset($_GET['page']) ? $_GET['page'] : 'ib2-dashboard';
				$mode = ( $page == 'ib2-funnel' ) ? 'edit' : 'group';
				@ib2_publish_page($post_id);
				if ( $group_id > 0 )
					$url = admin_url('admin.php?page=' . $page . '&mode=' . $mode . '&group_id=' . $group_id);
				else
					$url = admin_url('admin.php?page=ib2-dashboard');
				wp_redirect($url);
				break;
				
			case 'reset_stats':
				$post_id = (int) $_GET['post_id'];
				
				$wpdb->delete("{$wpdb->prefix}ib2_conversions", array('post_id' => $post_id));
				$wpdb->delete("{$wpdb->prefix}ib2_hits", array('post_id' => $post_id));
				
				$return_url = admin_url('admin.php?page=ib2-dashboard&post_id=' . $post_id . '&mode=stats&reset=true');
				if ( isset($_GET['funnel_id']) )
					$return_url .= '&funnel_id=' . $_GET['funnel_id'];

				wp_redirect($return_url);
				break;
				
			case 'delete_page':
				$post_id = (int) $_GET['post_id'];
				$group_id = isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0;
				$page = isset($_GET['page']) ? $_GET['page'] : 'ib2-dashboard';
				$mode = ( $page == 'ib2-funnel' ) ? 'edit' : 'group';
				@ib2_delete_page($post_id);
				if ( $group_id > 0 )
					$url = admin_url('admin.php?page=' . $page . '&mode=' . $mode . '&group_id=' . $group_id . '&page_deleted=true');
				else
					$url = admin_url('admin.php?page=ib2-dashboard&page_deleted=true');
				wp_redirect($url);
				break;
				
			case 'delete_pages':
				$group_id = ( isset($_POST['group_id']) ) ? $_POST['group_id'] : 0;
				$page = isset($_GET['page']) ? $_GET['page'] : 'ib2-dashboard';
				$mode = ( $page == 'ib2-funnel' ) ? 'edit' : 'group';
				$result = ib2_delete_pages();
				$msg = ( $result ) ? '&pages_deleted=true' : '';
				if ( $group_id > 0 )
					$url = admin_url('admin.php?page=' . $page . '&mode=' . $mode . '&group_id=' . $group_id . $msg);
				else
					$url = admin_url('admin.php?page=ib2-dashboard' . $msg);
				wp_redirect($url);
				break;

			case 'remove_from_group':
				$post_id = (int) $_GET['post_id'];
				$group_id = isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0;
				$page = isset($_GET['page']) ? $_GET['page'] : 'ib2-dashboard';
				$mode = ( $page == 'ib2-funnel' ) ? 'edit' : 'group';
				@ib2_group_remove_page($post_id);
				if ( $group_id > 0 )
					$url = admin_url('admin.php?page=' . $page . '&mode=' . $mode . '&group_id=' . $group_id);
				else
					$url = admin_url('admin.php?page=ib2-dashboard');
				wp_redirect($url);
				break;
				
			case 'ib2_create_funnel':
			
				$group = ib2_create_group($_POST['new_funnel_name'], 1, $_POST['new_funnel_type']);
				$msg =( !is_numeric($group) ) ? '&funnel_error=' . $group : '&funnel_created=true';
				
				if ( is_numeric($group) && function_exists('ib2f_create_funnel') ) {
					// create funnel function here...
					ib2f_create_funnel($group, $_POST['new_funnel_type']);
					$url = admin_url('admin.php?page=ib2-funnel&mode=edit&group_id=' . $group) . $msg;
					wp_redirect($url);
					exit;
				}
				
				$url = admin_url('admin.php?page=ib2-funnel') . $msg;
				wp_redirect($url);
				break;
				
			case 'add_funnel_page':
			
				$group_id = $_POST['group_id'];
				$page_type = $_POST['new_page_type'];
				$parent_id = $_POST['parent_id'];
				$template_id = (int) $_POST['new_template'];
				$group = ib2_get_group($group_id);
				
				$post_id = 0;
				if ( function_exists('ib2f_new_page') )
					$post_id = ib2f_new_page($group_id, esc_attr($group->name), $page_type, $parent_id, '', $template_id);
				
				$variant = ib2_variant_entry("post_id={$post_id}&variant=a");
				
				if ( $template = ib2_get_template($template_id) ) {
					if ( empty($template->metadata) ) {
						// Process template content from file
						$file = IB2_PATH . 'templates/' . $template->name . '.txt';
						if ( !file_exists($file) )
							$file = WP_CONTENT_DIR . '/ib2-templates/' . $template->name . '.txt';
						
						$content = '';
						if ( file_exists($file) ) {
							$content = @file_get_contents($file);
							$wpdb->update("{$wpdb->prefix}ib2_templates", array('metadata' => $content), array('ID' => $template->ID));
						}
						$data = maybe_unserialize($content);
					} else {
						$data = maybe_unserialize($template->metadata);
					}
					
					$meta = $data;
					$meta['enable'] = 'yes';
						
					update_post_meta($post_id, 'ib2_settings', $meta);
				}
				
				$url = admin_url('admin.php?page=ib2-funnel&mode=edit&group_id=' . $group_id) . '&page_added=true';
				wp_redirect($url);
				
				break;
				
			default:
				return;
		}
		exit;
	}
}

// CHECK GATE CONFIG WHEN A POST/PAGE is about to delete...
add_action('before_delete_post', 'ib2_before_delete_post', 10, 1);
function ib2_before_delete_post( $post_id ) {
	$lock_meta = get_post_meta($post_id, 'ib2_welcome_gate', true);
	if ( is_array($lock_meta) && !empty($lock_meta['gate_id']) ) { 
		delete_post_meta($post_id, 'ib2_welcome_gate');
	}
	
	$gate_meta = get_post_meta($post->ID, 'ib2_gate_master', true);
	if ( is_array($gate_meta) && !empty($gate_meta['lock_id']) ) {
		$lock_id = (int) $gate_meta['lock_id'];
		delete_post_meta($lock_id, 'ib2_welcome_gate');
		delete_post_meta($post_id, 'ib2_gate_master');
	}
}
