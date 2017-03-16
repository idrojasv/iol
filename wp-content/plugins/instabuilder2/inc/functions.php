<?php
add_action('template_redirect', 'ib2_ajax_handler', 3);
function ib2_ajax_handler() {
	if ( isset($_GET['ib2script']) && $_GET['ib2script'] == 'ajax' && isset($_REQUEST['action']) ) {
		$action = $_REQUEST['action'];
		do_action('ib2_ajax_' . $action);
		die();
	}
}

add_action('ib2_ajax_quiz_answer', 'ib2_ajax_quiz_answer_handler');
function ib2_ajax_quiz_answer_handler() {
	// Send back the response
	$response = array(
		'success' => true,
	);
		
	wp_send_json($response);
}

add_action('ib2_ajax_update_menu', 'ib2_ajax_update_menu_handler');
function ib2_ajax_update_menu_handler() {
	$menu = $_REQUEST['menu'];
	$id = $_REQUEST['menu_id'];
	$class = $_REQUEST['style'];
	
	$html = wp_nav_menu( array( 'menu' => $menu, 'container' => 'ul', 'menu_class' => 'ib2-navi ib2-updated-navi ib2-navi-' . $class, 'menu_id' => $id . '-nav', 'fallback_cb' => '', 'echo' => false) );
	
	// Send back the response
	$response = array(
		'success' => true,
		'output' => $html
	);
		
	wp_send_json($response);
}

add_filter('template_include', 'ib2_video_player_template', 99);
function ib2_video_player_template( $template ) {
	global $post;
	if ( !is_singular() || empty($post) )
		return $template;
	
	if ( isset($_GET['mode']) && $_GET['mode'] == 'ib2player' ) 
		$template = IB2_INC . 'player.php';
	
	return $template;
}

add_action('template_redirect', 'ib2_launch_gate_handler', 6);
function ib2_launch_gate_handler() {
	global $post, $ib2_variant;
	
	$license_key = ib2_license_key();
	if ( empty($license_key) )
		return;

	if ( is_404() ) return; 
	
	if ( is_ib2_admin() || $post->post_status != 'publish' ) return;
	
	if ( isset($_GET['ib2uc']) && isset($_COOKIE['__ib2_gated_' . $post->ID]) ) {
		$enc_code = $_GET['ib2uc'];
		$thanks_id = $post->ID;
		$gate_id = ib2_string_dec($enc_code, 'instabuilder2');
		$meta = get_post_meta($gate_id, 'ib2_settings', true);
		$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
		if ( !$enable ) return;
		
		$gate_type = ( isset($meta['gate_type']) ) ? $meta['gate_type'] : 'welcome';
		if ( $gate_type != 'launch' ) return;
		
		if ( !isset($_COOKIE['__ib2_unlock_' . $thanks_id]) ) {
			@setcookie('__ib2_unlock_' . $thanks_id, 1, time()+60*60*24*365, SITECOOKIEPATH);
			if ( COOKIEPATH != SITECOOKIEPATH )
				@setcookie('__ib2_unlock_' . $thanks_id, 1, time()+60*60*24*365, COOKIEPATH);
		}
		
		unset($_COOKIE['__ib2_gated_' . $post->ID]);
		@setcookie('__ib2_gated_' . $post->ID, '', time()-3600, COOKIEPATH);
		
		wp_redirect(get_permalink($thanks_id));
		exit;
	}
}

add_action('template_redirect', 'ib2_welcome_gate_handler', 7);
function ib2_welcome_gate_handler() {
	global $post, $ib2_variant;
	
	$license_key = ib2_license_key();
	if ( empty($license_key) )
		return;

	if ( is_404() ) return; 
	
	if ( is_ib2_admin() || $post->post_status != 'publish' ) return;
	
	if ( !isset($ib2_variant) )
		$ib2_variant = 'a';
	
	$variant = 'variation' . $ib2_variant;
	// check if this page is the locked page...
	$lock_meta = get_post_meta($post->ID, 'ib2_welcome_gate', true);
	if ( is_array($lock_meta) && !empty($lock_meta['gate_id']) ) {
		$gate_id = (int) $lock_meta['gate_id'];
		
		$meta = get_post_meta($gate_id, 'ib2_settings', true);
		$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
		if ( !$enable ) return;
	
		$data = ( isset($meta[$variant]) ) ? $meta[$variant] : array();
		
		$is_locked = 0;
		$thanks_id = 0;
		if ( isset($meta['gate_type']) ) {
			if ( isset($meta['welcome_gate']) && $meta['welcome_gate'] == 1 ) {
				$is_locked = 1;
			}
			$gate_type = $meta['gate_type'];
			$thanks_id = (int) $meta['gate_thanks_id'];
		} else {
			if ( $variants = ib2_get_variants($gate_id) ) {
				foreach ( $variants as $val ) {
					$var = 'variation' . $val->variant;
					$data = ( isset($meta[$var]) ) ? $meta[$var] : array();
					if ( isset($data['welcome_gate']) && $data['welcome_gate'] == 1 ) {
						$is_locked = 1;
						break;
					}
				}
			}
			$gate_type = ( isset($data['gate_type']) ) ? $data['gate_type'] : 'welcome';
		}
		
		if ( !$is_locked ) return;
		
		$gate_url = get_permalink($gate_id);
		if ( $gate_type == 'welcome' ) {
			if ( !isset($_COOKIE['__ib2_unlock_' . $post->ID]) ) {
				@setcookie('__ib2_gated_' . $gate_id, 1, 0, COOKIEPATH);
				wp_redirect($gate_url);
				exit;
			}
		} else if ( $gate_type == 'launch' ) {
			if ( !isset($_COOKIE['__ib2_unlock_' . $thanks_id]) && $post->ID != $thanks_id ) {
				@setcookie('__ib2_gated_' . $thanks_id, 1, time()+60*60*24*365, COOKIEPATH);
				wp_redirect($gate_url);
				exit;
			}
		}
	}
	
	// check if this page is the gate keeper...
	$gate_meta = get_post_meta($post->ID, 'ib2_gate_master', true);
	if ( is_array($gate_meta) && isset($_COOKIE['__ib2_gated_' . $post->ID]) ) {
		$meta = get_post_meta($post->ID, 'ib2_settings', true);
		$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
		if ( !$enable ) return;

		$gate_type = ( isset($gate_meta['type']) ) ? $gate_meta['type'] : 'welcome';
		// Back Compat
		if ( $gate_type == 'welcome' && isset($gate_meta['lock_id']) && !isset($gate_meta['lock_ids']) ) {
			$lock_id = (int) $gate_meta['lock_id'];
			if ( !isset($_COOKIE['__ib2_unlock_' . $lock_id]) ) {
				@setcookie('__ib2_unlock_' . $lock_id, 1, time()+60*60*24*365, SITECOOKIEPATH);
				if ( COOKIEPATH != SITECOOKIEPATH )
					@setcookie('__ib2_unlock_' . $lock_id, 1, time()+60*60*24*365, COOKIEPATH);
			}
		}
		
		if ( $gate_type == 'welcome' && isset($gate_meta['lock_ids']) ) {
			$lock_ids = $gate_meta['lock_ids'];
			if ( !is_array($lock_ids) ) $lock_ids = array($lock_ids);
			foreach ( $lock_ids as $lock_id ) {
				if ( !isset($_COOKIE['__ib2_unlock_' . $lock_id]) ) {
					@setcookie('__ib2_unlock_' . $lock_id, 1, time()+60*60*24*365, SITECOOKIEPATH);
					if ( COOKIEPATH != SITECOOKIEPATH )
						@setcookie('__ib2_unlock_' . $lock_id, 1, time()+60*60*24*365, COOKIEPATH);
				}
			}
		}
	}
}

add_action('template_redirect', 'ib2_webinar_signup', 6);
function ib2_webinar_signup() {
	global $post;
	if ( isset($_GET['ib2mode']) && $_GET['ib2mode'] == 'webinar_signup' && isset($_REQUEST['_webinar_key']) ) {
		$options = get_option('ib2_options');
		$webinar_key = $_REQUEST['_webinar_key'];
		$first_name = '';
		$last_name = '';
		$email = '';
		
		$is_webjam = false;
		if ( substr($webinar_key, 0, 3) == 'wj-' ) {
			$is_webjam = true;
		}

		if ( $is_webjam ) {
			$webjam_key = ( isset($options['webinarjam']) ? trim($options['webinarjam']) : '');
			if ( empty($webjam_key) )
				wp_die('ERROR: IB 2.0 and WebinarJam hasn\'t been connected.');
		} else {
			$gtw_token = get_option('ib2_gotowebinar');
			if ( empty($gtw_token) )
				wp_die('ERROR: IB 2.0 and GoToWebinar hasn\'t been connected.');
		
			$access_token = stripslashes($gtw_token['access_token']);
			$organizer_key = stripslashes($gtw_token['organizer_key']);
		}
			
		unset($_REQUEST['ib2submit']);
		// Build Fields: Phase #1
		foreach ( $_REQUEST as $k => $v ) {
			if ( stristr('mail', $k) ) $email = $v;
			if ( stristr('name', $k) ) $first_name = $v;
			
			if ( stristr('first', $k) ) $first_name = $v;
			if ( stristr('last', $k) ) $last_name = $v;
		}
		
		// still couldn't find the email address
		if ( $email == '' ) {
			foreach ( $_REQUEST as $k => $v ) {
				if ( filter_var($v, FILTER_VALIDATE_EMAIL) || sanitize_email($v) ) {
  					$email = $v;
				}
			}
		}
		
		// email still empty
		if ( $email == '' ) {
			wp_die("ERROR: Cannot found a valid email address.");
		}
		
		// first name still empty
		if ( $first_name == '' ) {
			$parts = explode('@', $email);
			$first_name = $parts[0];
		}
		
		// last name is empty
		if ( $last_name == '' ) {
			$parts = explode(' ', $first_name);
			if ( count($parts) > 1 ) {
				$first_name = $parts[0];
				$last_name = $parts[1];
			}
			
			// still empty
			if ( $last_name == '' ) {
				$parts = explode('.', $first_name);
				if ( count($parts) > 1 ) {
					$first_name = $parts[0];
					$last_name = $parts[1];
				}
			}
			
			// what!? still empty
			if ( $last_name == '' ) {
				$parts = explode('_', $first_name);
				if ( count($parts) > 1 ) {
					$first_name = $parts[0];
					$last_name = $parts[1];
				}
			}
			
			// last resort
			if ( $last_name == '' ) {
				$last_name = $first_name;
			}
		}
		
		// Let's reg to GTW or Webinar Jam
		if ( $is_webjam ) {
			$error = false;
			if ( !function_exists('curl_init') ) {
				wp_die('ERROR: cURL extension is NOT installed in your server. Please contact your hosting support.');
			}

			$webjam_url = 'https://app.webinarjam.com/api/v2/register';
			$cert = IB2_PATH . 'inc/certs/cacert.pem';
			$webinar_id = str_replace('wj-', '', $webinar_key);
			$fields = "api_key={$webjam_key}&webinar_id={$webinar_id}&name={$first_name}&email={$email}&schedule=0";
			$error = false;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $webjam_url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_CAINFO, getcwd() . "/cacert.pem");
			curl_setopt($ch, CURLOPT_TIMEOUT, 90);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

			$result = curl_exec($ch);

			if ( curl_errno($ch) ) {
				curl_close($ch);

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $webjam_url);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_TIMEOUT, 90);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

				$result = curl_exec($ch);

				if ( curl_errno($ch) ) {
				    $error = true;
				}
			}

			if ( $error && empty($_REQUEST['_orig_action_url']) ) {
				wp_die('ERROR: Failed to register to WebinarJam. Reason: ' . curl_error($ch));
			}

			$response = json_decode($result);
		
			if ( $response->status != 'success' && empty($_REQUEST['_orig_action_url']) ) {
				wp_die('ERROR: ' . $response->message);
			}
		} else {
			$data = array(
						'firstName' => $first_name,
						'lastName' => $last_name,
						'email' => $email
					);
			$api_url = "https://api.citrixonline.com/G2W/rest/organizers/{$organizer_key}/webinars/{$webinar_key}/registrants";
			$result = wp_remote_post($api_url, array(
					'method' => 'POST',
					'timeout' => 90,
					'redirection' => 5,
					'httpversion' => '1.1',
					'sslverify' => false,
					'headers' => array(
						'Authorization' => 'OAuth oauth_token=' . $access_token
					),
					'body' => json_encode($data),
					'cookies' => array()
			    )
			);
		}
			
		if ( empty($_REQUEST['_orig_action_url']) ) {
			if ( !$is_webjam ) {
				if ( is_wp_error($result) ) {
					wp_die('ERROR: ' . $result->get_error_message());
				}
				
				if ( $result['response']['code'] == 409 ) {
					wp_die('ERROR: You already registered.');
				}
			}

			if ( $is_webjam && isset($response->user->thank_you_url) ) {
				$redirect_url = ( !empty($_REQUEST['_webinar_redirect']) ) ? $_REQUEST['_webinar_redirect'] : $response->user->thank_you_url;
			} else {
				$redirect_url = ( isset($_REQUEST['_webinar_redirect']) ) ? $_REQUEST['_webinar_redirect'] : get_permalink($post->ID);
			}
			wp_redirect($redirect_url);
			exit;
		} else {
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
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Processing ... (do NOT Close this window)</title>
<link href="<?php echo IB2_CSS; ?>bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo IB2_CSS; ?>font-awesome.min.css" rel="stylesheet" type="text/css" />

<script type='text/javascript' src='<?php echo includes_url('js/jquery/jquery.js'); ?>'></script>
<script type='text/javascript' src='<?php echo includes_url('js/jquery/jquery-migrate.min.js'); ?>'></script>

</head>

<body>
<p>Processing...</p>

<?php
$action_url = stripslashes($_REQUEST['_orig_action_url']);
$form_method = $_REQUEST['_orig_method'];

unset($_REQUEST['_webinar_key']);
unset($_REQUEST['_orig_method']);
unset($_REQUEST['_orig_action_url']);
if ( isset($_REQUEST['_webinar_redirect']) )
	unset($_REQUEST['_webinar_redirect']);

echo '<form name="ib2-submit-optin" id="ib2-submit-option" action="' . $action_url . '" method="' . $form_method . '">';
foreach ( $_REQUEST as $k => $v ) {
	echo '<input type="hidden" name="' . $k . '" value="' . $v . '" />' . "\n";
}
echo '</form>'; 
?>
<script type="text/javascript">
	jQuery(document).ready(function($){
		setTimeout(function(){
			document.getElementById('ib2-submit-option').submit();
		}, 3000);
	});
</script>
</body>
</html>
<?php			
		}
		exit;
	}
}

add_action('template_redirect', 'ib2_internal_conversion', 12);
function ib2_internal_conversion() {
	global $wpdb, $post, $ib2_variant;
	if ( !is_singular() || ib2_bot() )
		return;
	
	if ( !$meta = get_post_meta($post->ID, 'ib2_conversions', true) )
		return;
	
	if ( $post->post_status != 'publish' ) return;
	if ( isset($_GET['preview']) && $_GET['preview'] == 'true' ) return;
	
	$visitor_id = ( isset($_COOKIE['__ib2vid']) ) ? $_COOKIE['__ib2vid'] : '';
	if ( empty($visitor_id) )
		return false; //$visitor_id = ib2_save_visitor();
	
	if ( is_array($meta) ) {
		$meta = array_unique($meta);
		foreach ( $meta as $post_id ) {
			$variant = ( isset($_COOKIE['__ib2pgvar_' . $post_id]) ) ? $_COOKIE['__ib2pgvar_' . $post_id] : $ib2_variant;
			
			if ( empty($variant) ) continue;
			
			if ( !ib2_variant_exists($post_id, $variant) ) continue;
			
			// Check if visitor has been visit the landing page...
			$visit_chk = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}ib2_hits WHERE `visitorid` = %s AND `variant` = %s AND `post_id` = %d", $visitor_id, $variant, $post_id));
			if ( $visit_chk < 1 ) {
				continue;
			}
			
			// Decide if we're going to record this conversion or not...
			$old = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ib2_conversions WHERE `visitorid` = %s AND `variant` = %s AND `post_id` = %d ORDER BY `ID` DESC LIMIT 1", $visitor_id, $variant, $post_id));

			$last_session = ( isset($_COOKIE['__ib2pgses_' . $post_id . '_' . $variant]) ) ? $_COOKIE['__ib2pgses_' . $post_id . '_' . $variant] : 0;

			if ( $last_session ) {
				$now = time();
				$diff = $now - $last_session;
				
				if ( $diff <= 3600 && $old ) {
					continue;
				}
			}
			
			$subid = ( isset($_COOKIE['__ib2subid_' . $post_id]) ) ? $_COOKIE['__ib2subid_' . $post_id] : '';
			$data = array(
				'post_id' => $post_id,
				'visitorid' => $visitor_id,
				'variant' => $variant,
				'date' => date("Y-m-d H:i:s", time()),
				'subid' => $subid
			);
			
			$wpdb->insert("{$wpdb->prefix}ib2_conversions", $data);
		}
	}
}

add_action('template_redirect', 'ib2_conversion_pixel', 2);
function ib2_conversion_pixel() {
	if ( isset($_GET['ib2track']) && $_GET['ib2track'] == 'conversion' && isset($_GET['page_id']) ) {
		global $wpdb, $ib2_variant;
		// Tracking Conversion Script
		$post_id = (int) $_GET['page_id'];
		$variant = ( isset($_GET['variant']) ) ? $_GET['variant'] : $ib2_variant;
		$visitor_id = ( isset($_GET['visitor_id']) ) ? $_GET['visitor_id'] : '';
		
		$subid = ( isset($_GET['subid']) ) ? urldecode($_GET['subid']) : '';
		
		$write = true;
		if ( !ib2_variant_exists($post_id, $variant) ) $write = false;
		if ( $visitor_id == '' || $variant == '' )  $write = false;
		
		// Check if visitor has been visit the landing page...
		$visit_chk = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}ib2_hits WHERE `visitorid` = %s AND `variant` = %s AND `post_id` = %d", $visitor_id, $variant, $post_id));
		if ( $visit_chk < 1 )
			$write = false;
				
		// Decide if we're going to record this conversion or not...
		$old = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ib2_conversions WHERE `visitorid` = %s AND `variant` = %s AND `post_id` = %d ORDER BY `ID` DESC LIMIT 1", $visitor_id, $variant, $post_id));

		$last_session = ( isset($_COOKIE['__ib2pgses_' . $post_id . '_' . $variant]) ) ? $_COOKIE['__ib2pgses_' . $post_id . '_' . $variant] : 0;

		if ( $last_session ) {
			$now = time();
			$diff = $now - $last_session;
			
			if ( $diff <= 3600 && $old ) {
				$write = false;
			}
		}
			
		if ( $write ) {
			$data = array(
				'post_id' => $post_id,
				'visitorid' => $visitor_id,
				'variant' => $variant,
				'subid' => $subid,
				'date' => date_i18n("Y-m-d H:i:s")
			);
			
			$wpdb->insert("{$wpdb->prefix}ib2_conversions", $data);
		}
		
  		$im = imagecreate(1, 1);
  		$white = imagecolorallocate($im, 255, 255, 255);
		imagesetpixel($im, 1, 1, $white);

		header("content-type:image/jpg");
		imagejpeg($im);
		imagedestroy($im);
		
		exit;
	}
}

add_action('template_redirect', 'ib2_conversion_js', 2);
function ib2_conversion_js() {
	if ( isset($_GET['ib2script']) && $_GET['ib2script'] == 'conversion_js' && isset($_GET['post_id']) ) {
		$post_id = (int) $_GET['post_id'];
		
		header("Content-type:text/javascript");
		
?>

function ib2Conversion() {
	var page_id = <?php echo $post_id; ?>;
	var generateVisitorID = function ( length ) {
	    var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');
	
	    if ( !length ) {
	        length = Math.floor(Math.random() * chars.length);
	    }
	
	    var str = '';
	    for ( var i = 0; i < length; i++ ) {
	        str += chars[Math.floor(Math.random() * chars.length)];
	    }
	    
	    if ( $('#' + str).length )
	    	str = generateID(length);
	    	
    	return str;
	}
	
	var setCookie = function ( cookieName, cookieValue, exdays ) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cookieName + "=" + cookieValue + "; " + expires;
	} 

	var getCookie = function ( cookieName ) {
	    var name = cookieName + "=";
	    var ca = document.cookie.split(';');
	    for ( var i = 0; i < ca.length; i++ ) {
	        var c = ca[i];
	        while ( c.charAt(0) == ' ' ) c = c.substring(1);
	        if ( c.indexOf(name) != -1 ) return c.substring(name.length, c.length);
	    }
    	return "";
	}
	
	var visitorId = getCookie('__ib2vid');
	if ( visitorId == '' ) {
		visitorId = generateVisitorID(8);
		setCookie('__ib2vid', visitorId, 365);
	}
	
	var pageVariant = getCookie('__ib2pgvar_' + page_id),
	subid = getCookie('__ib2subid_' + page_id);
	if ( pageVariant != '' ) {
		var image = new Image();
		image.src = '<?php echo esc_url(add_query_arg('ib2track', 'conversion', get_permalink($post_id))); ?>&variant=' + pageVariant + '&page_id=' + page_id + '&visitor_id=' + visitorId + '&subid=' + subid;
	}
}

ib2Conversion();

<?php
		exit;
	}
}

add_action('template_redirect', 'ib2_track_visitor');
function ib2_track_visitor() {
	global $post;
	if ( !is_singular() )
		return;
	
	$meta = get_post_meta($post->ID, 'ib2_settings', true);
	$enable = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? TRUE : FALSE;
	if ( !$enable ) return;
	
	if ( isset($_GET['mode']) && $_GET['mode'] = 'ib2player' ) return;
	if ( isset($_GET['preview']) && $_GET['preview'] == 'true' ) return;
	
	if ( $post->post_status != 'publish' ) return;
	
	
	if ( isset($_GET['variant']) || is_ib2_admin() )
		return;
	
	// track visitor
	ib2_save_visitor();
}

function ib2_save_visitor() {
	global $post, $wpdb, $ib2_variant;
	if ( !is_singular() || ib2_bot() )
		return false;
	
	if ( !isset($ib2_variant) )
		$ib2_variant = ( isset($_COOKIE['__ib2pgvar_' . $post->ID]) ) ? $_COOKIE['__ib2pgvar_' . $post->ID] : 'a';
	
	// check page session cookie
	if ( isset($_COOKIE['__ib2pgses_' . $post->ID . '_' . $ib2_variant]) ) {
		$last_visit = $_COOKIE['__ib2pgses_' . $post->ID . '_' . $ib2_variant];
		$now = time();
		$diff = $now - $last_visit;
		if ( $diff < 1800 )
			return false;
	}
	
	@setcookie('__ib2pgses_' . $post->ID . '_' . $ib2_variant, time(), 0, '/');
	if ( SITECOOKIEPATH != '/' )
		@setcookie('__ib2pgses_' . $post->ID . '_' . $ib2_variant, time(), 0, SITECOOKIEPATH);
		
	if ( !isset($_COOKIE['__ib2vid']) ) {
		$visitor_id = ib2_random_words();
		@setcookie('__ib2vid', $visitor_id, time()+60*60*24*30, '/');
		if ( SITECOOKIEPATH != '/' )
			@setcookie('__ib2vid', $visitor_id, time()+60*60*24*30, SITECOOKIEPATH);
	} else {
		$visitor_id = $_COOKIE['__ib2vid'];
	}
	
	if ( isset($_GET['subid']) ) {
		@setcookie('__ib2subid_' . $post->ID, urldecode($_GET['subid']), time()+60*60*24*30, '/');
	}
	
	require_once(IB2_INC . 'Browser.php');
	$browser = new Browser();
	$ipaddress = ib2_get_ipaddr();
	$proto = is_ssl() ? 'https://' : 'http://';
	$data = array(
		'post_id' => $post->ID,
		'visitorid' => $visitor_id,
		'variant' => $ib2_variant,
		'url' => $proto . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
		'ipaddress' => $ipaddress,
		'referer' => (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''),
		'browser' => $browser->getBrowser(),
		'location' => ib2_get_country($ipaddress),
		'subid' => (isset($_GET['subid']) ? urldecode($_SERVER['subid']) : ''),
		'date' => date_i18n("Y-m-d H:i:s")
	);
		
	
	
	$wpdb->insert("{$wpdb->prefix}ib2_hits", $data);
	
	unset($data);
	
	return $visitor_id;
}

function ib2_get_country( $ipaddress = '' ) {
	if ( empty($ipaddress) )
		$ipaddress = ib2_get_ipaddr();
	
	if ( $ipaddress == '127.0.0.1' ) return ''; // don't bother
	
	$host = 'http://www.geoplugin.net/json.gp?ip=' . $ipaddress;
	if ( function_exists('curl_init') ) {
		//	use cURL to fetch data
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $host);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.0');
		$response = curl_exec($ch);
		curl_close ($ch);
	} else if ( ini_get('allow_url_fopen') ) {
		// fall back to fopen()
		$response = @file_get_contents($host, 'r');
	} else {
		return '';
	}

	$result = @json_decode($response);
	return $result <> NULL ? $result->geoplugin_countryName . ' (' . $result->geoplugin_countryCode . ')' : false;
}

function ib2_random_words( $length = 8, $number = true, $capital = true ) {
	if ( $length < 1 )
		return false;
	
    $characters = 'abcdefghijklmnopqrstuvwxyz';
	if ( $number )
		$characters .= '1234567890';
	if ( $capital )
		$characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		
    $words = '';    
	for ( $p = 0; $p < $length; $p++ ) {
   		$words .= $characters[mt_rand(0, strlen($characters)-1)];
	}
    return $words;
}

function ib2_get_ipaddr() {
    $ipaddress = '';
    if ( isset($_SERVER['HTTP_CLIENT_IP']) )
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) )
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if ( isset($_SERVER['HTTP_X_FORWARDED']) )
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if ( isset($_SERVER['HTTP_FORWARDED_FOR']) )
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if ( isset($_SERVER['HTTP_FORWARDED']) )
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if ( isset($_SERVER['REMOTE_ADDR']) )
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function ib2_bot() {
	if ( isset($_SERVER['HTTP_USER_AGENT']) ) {
		// Phase 1
		if ( preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT']) ) {
    		return true;
		}
		
		// Phase 2
		$bots = ib2_bot_lists();
		foreach ( $bots as $bot ) {
			if ( strpos(strtolower($_SERVER['HTTP_USER_AGENT']), $bot) !== false )
				return true;
		}
	}
	return false;
}

function ib2_bot_lists() {
	return array('008','abachobot','accoona-ai-agent','addsugarspiderbot','anyapexbot','arachmo','b-l-i-t-z-b-o-t',
	'baiduspider','becomebot','beslistbot','billybobbot','bimbot','bingbot','blitzbot','boitho.com-dc','boitho.com-robot',
	'btbot','catchbot','cerberian drtrs','charlotte','converacrawler','cosmos','covario ids','dataparksearch','diamondbot',
	'discobot','dotbot','earthcom.info','emeraldshield.com webbot','envolk[its]spider','esperanzabot','exabot',
	'fast enterprise crawler','fast-webcrawler','fdse robot','findlinks','furlbot','fyberspider','g2crawler',
	'gaisbot','galaxybot','geniebot','gigabot','girafabot','googlebot','googlebot-image','gurujibot','happyfunbot',
	'hl_ftien_spider','holmes','htdig','iaskspider','ia_archiver','iccrawler','ichiro','igdespyder','irlbot',
	'issuecrawler','jaxified bot','jyxobot','koepabot','l.webis','lapozzbot','larbin','ldspider','lexxebot',
	'linguee bot','linkwalker','lmspider','lwp-trivial','mabontland','magpie-crawler','mediapartners-google',
	'mj12bot','mlbot','mnogosearch','mogimogi','mojeekbot','moreoverbot','morning paper','msnbot','msrbot',
	'mvaclient','mxbot','netresearchserver','netseer crawler','newsgator','ng-search','nicebot','noxtrumbot',
	'nusearch spider','nutchcvs','nymesis','obot','oegp','omgilibot','omniexplorer_bot','oozbot','orbiter',
	'pagebiteshyperbot','peew','polybot','pompos','postpost','psbot','pycurl','qseero','radian6','rampybot',
	'rufusbot','sandcrawler','sbider','scoutjet','scrubby','searchsight','seekbot','semanticdiscovery',
	'sensis web crawler','seochat::bot','seznambot','shim-crawler','shopwiki','shoula robot','silk','sitebot',
	'slurp','snappy','sogou spider','sosospider','speedy spider','sqworm','stackrambler','suggybot','surveybot',
	'synoobot','teoma','terrawizbot','thesubot','thumbnail.cz robot','tineye','truwogps','turnitinbot',
	'tweetedtimes bot','twengabot','updated','urlfilebot','vagabondo','voilabot','vortex','voyager','vyu2',
	'webcollage','websquash.com','wf84','wofindeich robot','womlpefactory','xaldon_webspider','yacy','yahoo! slurp',
	'yahoo! slurp china','yahooseeker','yahooseeker-testing','yandexbot','yandeximages','yandexmetrika','yasaklibot',
	'yeti','yodaobot','yooglifetchagent','youdaobot','zao','zealbot','zspider','zyborg');
}