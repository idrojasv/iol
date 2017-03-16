<?php
add_shortcode('ib2_menu', 'ib2_menu_handler');
function ib2_menu_handler( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'id' => '',
		'style' => 'plain',
		'menu' => ''
	), $atts ) );
	
	$html = wp_nav_menu( array( 'menu' => $menu, 'container' => 'ul', 'menu_class' => 'ib2-navi ib2-new-navi ib2-navi-' . $style, 'menu_id' => $id . '-nav', 'fallback_cb' => '', 'echo' => false) );
	return $html;
}

add_shortcode('ib2_share', 'ib2_share_handler');
function ib2_share_handler( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'type' => 'twitter',
		'mode' => 'big',
		'url' => ''
	), $atts ) );
	
	if ( isset($_GET['ib2editor']) && isset($_GET['post']) ) { // render for the editor
		return '<img src="' . IB2_IMG . '' . $type . '-share-' . $mode . '.png" border="0" />';
	} else { // render for front...
		global $post;
		$output = '';
		$url = !empty($url) ? filter_var($url, FILTER_SANITIZE_URL) : '';
		$destination = ( !empty($url) && filter_var($url, FILTER_VALIDATE_URL) ) ? $url : get_permalink($post->ID);
		
		if ( $type == 'twitter' ) {
			$counter = ' data-count="none"';
			if ( $mode == 'big' ) $counter = ' data-count="vertical"';
			if ( $mode == 'small-counter' ) $counter = ' data-count="horizontal"';
			$output .= '<a href="https://twitter.com/share" class="twitter-share-button" data-url="' . $destination . '"' . $counter . '>Tweet</a>' . "\n";
			$output .= "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>\n\n";
		} else if ( $type == 'linkedin' ) {
			$counter = '';
			if ( $mode == 'big' ) $counter = ' data-counter="top"';
			if ( $mode == 'small-counter' ) $counter = ' data-counter="right"';

			$output .= '<script src="//platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script>' . "\n";
			$output .= '<script type="IN/Share" data-url="' . $destination . '"' . $counter . '></script>' . "\n";

		} else if ( $type == 'google' ) {
			$counter = ' size="tall" annotation="none"';
			if ( $mode == 'big' ) $counter = ' size="tall" annotation="bubble"';
			if ( $mode == 'small-counter' ) $counter = '';
			$output .= '<script src="https://apis.google.com/js/platform.js" async defer></script>' . "\n";
			$output .= '<g:plusone href="' . $destination . '"' . $counter . '></g:plusone>' . "\n";
		} else if ( $type == 'facebook' ) {
			$counter = 'button';
			if ( $mode == 'big' ) $counter = 'box_count';
			if ( $mode == 'small-counter' ) $counter = 'button_count';
			$output .= '<div class="fb-like" data-width="80" data-href="' . $destination . '" data-layout="' . $counter . '" data-action="like" data-show-faces="false" data-share="false"></div>' . "\n";
		}
		return $output;
	}
}

function ib2_license_key() {
	$key = get_option('ib2_kunci');
	if ( is_array($key) && !empty($key['content']) ) {
		return stripslashes($key['content']);
	}
	return '';
}

add_shortcode('ib2_quiz_answer', 'ib2_quiz_answer_handler');
function ib2_quiz_answer_handler( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'q' => 1,
		'a' => 1
	), $atts ) );
	
	if ( isset($_GET['ib2editor']) && isset($_GET['post']) ) { // render for the editor
		$post_id = (int) $_GET['post'];
		$data = get_post_meta($post_id, 'ib2_settings', true);
		$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variationa';
		$meta = ( isset($data[$variant]) ) ? $data[$variant] : '';
		if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
			$t_id = (int) $_GET['template_id'];
			if ( $template = ib2_get_template($t_id) ) {
				$data = maybe_unserialize($template->metadata);
				$meta = ( isset($data[$variant]) ) ? $data[$variant] : '';
			}
		}
		
		if ( empty($meta['quiz']) ) return '';
		
		$quiz = $meta['quiz'];
		$_q = $q - 1;
		$answer = stripslashes($quiz[$_q]['answers'][$a]['answer']);
		return '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="' . $q . '" data-ans="' . $a . '">' . $answer . '</span></label></div></div>';
	} else { // render for front...
		global $post, $ib2_variant;
		if ( !isset($ib2_variant) )
			$ib2_variant = 'a';
		
		$data = get_post_meta($post->ID, 'ib2_settings', true);
		$variant = 'variation' . $ib2_variant;
		$meta = ( isset($data[$variant]) ) ? $data[$variant] : '';
		
		if ( empty($meta['quiz']) ) return '';
		
		$quiz = $meta['quiz'];
		$_q = $q - 1;
		
		$answer = stripslashes($quiz[$_q]['answers'][$a]['answer']);
		return '<div class="form-group"><input type="radio" id="q_' . $q . '_a-' . $a . '" class="answers-radio" name="quiz_que_' . $q . '" value="' . $a . '" data-label="' . esc_attr($answer) . '" /> </div>';
	}
}

add_shortcode('ib2_unused_quiz', 'ib2_unused_quiz_handler');
function ib2_unused_quiz_handler( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'q' => 1,
		'id' => '',
		'tcolor' => '#333333',
		'acolor' => '#333333' 
	), $atts ) );
	
	if ( isset($_GET['ib2editor']) && isset($_GET['post']) ) { // render for the editor
		$output = '<div id="' . $id . '" style="display:none" class="ib2-quiz-page ib2-unused-question" data-question="' . $q . '">';
			$output .= '<h3 class="quiz-text-edit quiz-text-question" style="color:#' . $tcolor . '">This is your number #' . $q . ' question. Simply click this text to edit?</h3>';
			$output .= '<div id="' . $id . '-answers" class="ib2-answer-list" style="color:#' . $acolor . '">';
				$output .= '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="' . $q . '" data-ans="1">Answer number 1 for the number ' . $q . ' question</span></label></div></div>';
				$output .= '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="' . $q . '" data-ans="2">Answer number 2 for the number ' . $q . ' question</span></label></div></div>';
				$output .= '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="' . $q . '" data-ans="3">Answer number 3 for the number ' . $q . ' question</span></label></div></div>';
				$output .= '<div class="form-group has-pretty-child"><div class="clearfix prettyradio labelright margin-right blue"><a class="" href="#"></a> <label><span class="quiz-text-edit" data-que="' . $q . '" data-ans="4">Answer number 4 for the number ' . $q . ' question</span></label></div></div>';
			$output .= '</div>';
		$output .= '</div>';
		return $output;
	} else {
		return '';
	}
}

add_shortcode('ib2_comment', 'ib2_comment_handler');
function ib2_comment_handler( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'id' => '',
		'type' => 'facebook'
	), $atts ) );
	
	if ( isset($_GET['ib2editor']) && isset($_GET['post']) ) { // render for the editor
		$id_suffix = ( $type == 'facebook' ) ? 'fbcomm' : $type;
		$class = ( $type == 'facebook' ) ? 'fb-comment' : $type;
		$output = '<div id="' . $id . '-' . $id_suffix .'" class="' . $class . '-left">';
			$output .= '<div class="' . $class . '-right"></div>';
		$output .= '</div>';
		return $output;
	} else {
		global $post;
		
		$options = get_option('ib2_options');
		if ( $type == 'facebook' ) {
			
			if ( empty($options['fb_appid']) ) return '<p class="text-danger"><em><strong>ERROR:</strong> Facebook comment cannot be displayed. Please go to <a href="' . admin_url('admin.php?page=ib2-settings') . '">IB 2.0 Settings</a> page to integrate IB 2.0 with Facebook app.</em></p>';
			return '<div class="fb-comments" data-href="' . get_permalink($post->ID) . '" data-numposts="5" data-colorscheme="light" data-width="100%"></div>';
		} else if ( $type == 'disqus' ) {
			if ( empty($options['disqus']) ) return '<p class="text-danger"><em><strong>ERROR:</strong> Disqus comment cannot be displayed. Please go to <a href="' . admin_url('admin.php?page=ib2-settings') . '">IB 2.0 Settings</a> page to integrate IB 2.0 with Disqus.</em></p>';
			$output = '
				<div id="disqus_thread"></div>
				<script type="text/javascript">
		    	/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
		    	var disqus_shortname = "' . $options['disqus'] . '"; // required: replace example with your forum shortname
				var disqus_title = "' . esc_attr($post->post_title) . '";
				var disqus_url = "' . get_permalink($post->ID) . '";
				var disqus_developer = 1;
				
		    	/* * * DON\'T EDIT BELOW THIS LINE * * */
		    	(function() {
		        	var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
		        	dsq.src = "http://" + disqus_shortname + ".disqus.com/embed.js";
		        	(document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
		    	})();
				</script>
				<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
				<a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>
			';
			
			return $output;
		}
	}
}

add_shortcode('ib2_video', 'ib2_video_handler');
function ib2_video_handler( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'id' => '',
		'type' => 'hosted' 
	), $atts ) );
	
	if ( isset($_GET['ib2editor']) && isset($_GET['post']) ) { // render for the editor
		$output = '<img src="' . IB2_IMG . 'video-placeholder.png" class="img-responsive vid-placeholder" />';		
		return $output;
	} else {
		global $post, $ib2_variant;
		$output = '';
		$meta = get_post_meta($post->ID, 'ib2_settings', true);
		if ( is_array($meta) ) {
			$data = $meta['variation' . $ib2_variant];
			if ( !empty($data['video_data']) && !empty($data['video_data'][$id]) ) {
				if ( $data['video_data'][$id]['type'] == 'embed' ) {
					$code = stripslashes($data['video_data'][$id]['embed']);
					$code = ( is_base64($code) ) ? base64_decode($code) : $code;
					$code = html_entity_decode($code);
					
					if ( !empty($code) && strpos('class', $code) ) {
						$code = str_replace('class="', 'class="embed-responsive-item ', $code);
						$code = str_replace("class='", "class='embed-responsive-item ", $code);
					} else {
						$code = str_replace('<iframe', '<iframe class="embed-responsive-item"', $code);
						$code = str_replace('<embed', '<embed class="embed-responsive-item"', $code);
						$code = str_replace('<object', '<object class="embed-responsive-item"', $code);
					}

					$output .= $code . "\n";
				} else {
					$vid = uniqid('video_');
					$hash = $vid . sha1(rand().microtime());
					$code = stripslashes($data['video_data'][$id][$data['video_data'][$id]['type']]['code']);
					$code = ( is_base64($code) ) ? base64_decode($code) : $code;
					$autoplay = ( stristr($code, 'autoplay=1') ) ? 'yes' : 'no';
					$code = str_replace('&autoplay=1', '', $code);
					$output .= '<iframe name="' . $hash . '" class="embed-responsive-item" src="' . $code . '&enablejsapi=1" scrolling="no" data-autoplay="' . $autoplay . '" allowFullScreen webkitAllowFullScreen mozallowfullscreen></iframe>';
				}
			}
		}
		
		return $output;
	}
}

add_shortcode('ib2_bgvid', 'ib2_bgvid_handler');
function ib2_bgvid_handler( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'id' => '',
		'mp4' => '',
		'ogg' => '',
		'webm' => ''
	), $atts ) );
	
	$output = '';
	if ( isset($_GET['ib2editor']) && isset($_GET['post']) ) { // render for the editor
		$output .= '<div class="ib2-bgvideo-param" id="' . $id . '_bgvid" data-mp4="' . $mp4 . '" data-ogg="' . $ogg . '" data-webm="' . $webm . '"></div>';		
	} else {
		global $post, $ib2_variant;
		
		$output .= '<div id="' . $id . '_bgvid" class="vid-fullscreen-bg">';
    	$output .= '<video loop muted autoplay class="fullscreen-bg-video">';
      	$output .= '<source src="' . $webm . '" type="video/webm">';
        $output .= '<source src="' . $mp4 . '" type="video/mp4">';
        $output .= '<source src="' . $ogg . '" type="video/ogg">';
    	$output .= '</video>';
		$output .= '</div>';
	}
	return $output;
}

add_shortcode('ib2_carousel', 'ib2_carousel_handler');
function ib2_carousel_handler( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'id' => '',
	), $atts ) );
	
	if ( isset($_GET['ib2editor']) && isset($_GET['post']) ) { // render for the editor
		$output = '<img src="' . IB2_IMG . 'slides-placeholder.jpg" class="img-responsive" />';		
		return $output;
	} else {
		global $post, $ib2_variant;
		
		$meta = get_post_meta($post->ID, 'ib2_settings', true);
		$data = $meta['variation' . $ib2_variant];
		
		$output = '';
		if ( !empty($data['carousel_data']) && !empty($data['carousel_data'][$id]) ) {
			$slider_data = $data['carousel_data'][$id];
			
			$output .= '<div id="' . $id . '-carousel" class="carousel slide" data-ride="carousel">';
			
				$output .= '<ol class="carousel-indicators">';
				$i = 0;
				foreach ( $slider_data as $slider ) {
					if ( empty($slider['imageurl']) ) continue;
					$active = ( $i == 0 ) ? ' class="active"' : '';
					$output .= '<li data-target="#' . $id . '-carousel" data-slide-to="' . $i . '"' . $active . '></li>';
					
					$i++;
				}
				$output .= '</ol>';
			
				$output .= '<div class="carousel-inner" role="listbox">';
				$i = 0;
				foreach ( $slider_data as $slider ) {
					if ( empty($slider['imageurl']) ) continue;
					$active = ( $i == 0 ) ? ' active' : '';
					$title = stripslashes($slider['title']);
					$img = stripslashes($slider['imageurl']);
					$url = stripslashes($slider['desturl']);
					$output .= '<div class="item' . $active . '">';
						if ( !empty($url) ) $output .= '<a href="' . $url . '">';
						$output .= '<img src="' . $img . '" alt="' . $title . '" />';
	      				$output .= '<div class="carousel-caption">' . $title  . '</div>';
						if ( !empty($url) ) $output .= '</a>';
					$output .= '</div>';
						
					$i++;
				}
				$output .= '</div>';
				
				$output .= '<a class="left carousel-control" href="#' . $id . '-carousel" role="button" data-slide="prev">';
	    			$output .= '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>';
	    			$output .= '<span class="sr-only">Previous</span>';
	  			$output .= '</a>';
	  			$output .= '<a class="right carousel-control" href="#' . $id . '-carousel" role="button" data-slide="next">';
	    			$output .= '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
	    			$output .= '<span class="sr-only">Next</span>';
	  			$output .= '</a>';

			$output .= '</div>';
			
			$output .= '<script>jQuery(document).ready(function($){$("#' . $id . '-carousel").carousel();});</script>';
		}
		return $output;
	}
}

add_action('wp', 'ib2_setup_schedule');
function ib2_setup_schedule() {
	if ( !wp_next_scheduled('ib2_daily_event') ) {
		wp_schedule_event( time(), 'daily', 'ib2_daily_event');
	}
}
add_action('ib2_daily_event', 'ib2_do_this_daily');
function ib2_do_this_daily() {
	$key = get_option('ib2_kunci');
	$server_key = 'QpQPzzmxx4xxReo2bB1eP9qKP1lsGyl7';
	$license_key = ( is_array($key) && !empty($key['content']) ) ? stripslashes($key['content']) : '';
	$md5_hash = md5($license_key . $server_key);
	$api_url = "http://instabuilder.com/v2.0/!/instamember_api/license/{$license_key}";
	
	$call = wp_remote_get($api_url, array(
			'timeout' => 45,
			'httpversion' => '1.1',
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $server_key . ':' . $md5_hash )
			)
	    )
	);
	
	if ( is_wp_error($call) )
		return;
	
	$license = json_decode($call['body']);
	if ( !isset($license->status) )
		return;
	
	if ( $license->status == 'Success' || $license->status == 'Maxed' || $license->status == 'Expired' )
		return;
	
	delete_option('ib2_kunci');
}

