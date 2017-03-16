<?php
add_action('wp_ajax_ib2_show_templates', 'ib2_ajax_show_templates');
function ib2_ajax_show_templates() {
	if ( isset($_REQUEST['type']) ) {
		$type = $_REQUEST['type'];
		$keyword = sanitize_text_field($_REQUEST['keyword']);
		$subtype = $_REQUEST['subtype'];
		
		ib2_get_templates_html( array('keyword' => $keyword, 'type' => $type, 'subtype' => $subtype) );
	}
	die();
}

add_action('wp_ajax_ib2_delete_template', 'ib2_ajax_delete_template');
function ib2_ajax_delete_template() {
	if ( isset($_REQUEST['template_id']) ) {
		global $wpdb;
		$template_id = $_REQUEST['template_id'];
		$wpdb->delete("{$wpdb->prefix}ib2_templates", array('ID' => $template_id));
	}
	die();
}

add_action('wp_ajax_ib2_publish_facebook', 'ib2_ajax_publish_facebook');
function ib2_ajax_publish_facebook() {
	if ( isset($_REQUEST['fb_ids']) && isset($_REQUEST['post_id']) ) {
		global $wpdb;
		
		$fb_ids = $_REQUEST['fb_ids'];
		$post_id = (int) $_REQUEST['post_id'];
		$options = get_option('ib2_options');
		
		$fbpage_id = 0;
		if ( $fb_ids ) {
			foreach ( $fb_ids as $id ) {
				$chk = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}ib2_facebook` WHERE `fb_id` = %s", $id));
				if ( $chk > 0 ) {
					$wpdb->update("{$wpdb->prefix}ib2_facebook", array('post_id' => $post_id), array('fb_id' => $id));
				} else {
					$wpdb->insert("{$wpdb->prefix}ib2_facebook", array('post_id' => $post_id, 'fb_id' => $id));
				}
				$fbpage_id = $id;
			}
		}

		if ( empty($fbpage_id) ) {
			$response = array( 'success' => false );
			wp_send_json_error( $response );
			die();
		}
		
		$response = array(
			'success' => true,
			'fburl' => 'http://www.facebook.com/' . $fbpage_id . '?v=app_' . esc_attr($options['fb_appid'])
		);
		wp_send_json( $response );
	}
	die();
}

add_action('wp_ajax_ib2_process_optin', 'ib2_ajax_process_optin');
function ib2_ajax_process_optin() {
	if ( isset($_REQUEST['code']) && isset($_REQUEST['post_id']) ) {
		$post_id = (int) $_REQUEST['post_id'];
		$optin_id = $_REQUEST['optin_id'];
		$classes = stripslashes($_REQUEST['classes']);
		$styles = stripslashes($_REQUEST['styles']);
		$code = trim($_REQUEST['code']);
		$code = html_entity_decode(stripslashes($code));
		
		// original code...
		$_code = $code;
		
		$meta = get_post_meta($post_id, 'ib2_optins', true);
		if ( !is_array($meta) ) $meta = array();
		
		//if ( isset($meta[$optin_id]['raw']) && $meta[$optin_id]['raw'] == stripslashes($_code) ) {
			//$response = $meta[$optin_id]['filtered'];
		//}
		
		if ( !isset($response) ) {
			$response = array();
			ib2_extract_optin($response, $code);
		}

		if ( empty($response) ) {
			$response = array( 'success' => false );
			wp_send_json_error( $response );
			die();
		}
		// save orig code to post meta...
		$meta[$_REQUEST['optin_id']] = array('raw' => $_code, 'filtered' => $response);
		update_post_meta($_REQUEST['post_id'], 'ib2_optins', $meta);
		
		// build the fields...
		$data = ib2_populate_fields($response['fields'], $classes, $styles);
		
		// hidden fields...
		if ( !empty($response['hiddens']) ) {
			foreach ( $response['hiddens'] as $k => $v ) {
				$data .= '<input type="hidden" name="' . $k . '" value="' . $v . '">';
			}
		}
		
		if ( !isset($response['method']) )
			$response['method'] = 'post';
		
		$result = array(
				'action' => $response['action'],
				'method' => $response['method'],
				'html' => $data,
				'rawcode' => $_code,
				'data' => $response
			);
			
		// return the data in json...
		wp_send_json($result);
	}
	die();
}

add_action('wp_ajax_ib2_aviary_download', 'ib2_ajax_aviary_download');
function ib2_ajax_aviary_download() {
	if ( isset($_REQUEST['image']) ) {
		$img_url = esc_url($_REQUEST['image']);
		$post_id = (int) $_REQUEST['post_id'];
		$image = media_sideload_image($img_url, $post_id);
		
		if ( is_wp_error($image) ) {
			$response = array( 'success' => false );
			wp_send_json_error($response);
			die();
		}
		
		require_once( IB2_INC . 'simple_html_dom.php');
		$html = str_get_html($image);
		$new_image = $html->find('img', 0)->src;
		$response = array(
			'success' => true,
			'url' => $new_image,
		);
		
		wp_send_json($response);
	}
}

add_action('wp_ajax_ib2_download_image', 'ib2_ajax_download_image');
function ib2_ajax_download_image() {
	if ( isset($_REQUEST['img_url']) ) {
		$img_url = esc_url($_REQUEST['img_url']);
		$post_id = (int) $_REQUEST['post_id'];
		$image = media_sideload_image($img_url, $post_id);
		
		if ( is_wp_error($image) ) {
			$response = array( 'success' => false );
			wp_send_json_error($response);
			die();
		}
		
		require_once( IB2_INC . 'simple_html_dom.php');
		$html = str_get_html($image);
		$new_image = $html->find('img', 0)->src;
		list($width, $height) = @getimagesize($new_image);
		$response = array(
			'success' => true,
			'img' => $new_image,
			'width' => $width,
			'height' => $height
		);
		
		wp_send_json($response);
	}
	die();
}

add_action('wp_ajax_ib2_generate_legal', 'ib2_ajax_generate_legal');
function ib2_ajax_generate_legal() {
	if ( isset($_REQUEST['ctype']) && isset($_REQUEST['b_name']) ) {
		$file = IB2_PATH . 'templates/' . $_REQUEST['ctype'] . '.txt';
		if ( !file_exists($file) ) {
			$response = array(
				'success' => false,
			);
			wp_send_json_error($response);
			die();
		}
		
		$content = @file_get_contents($file);
		
		$bn = ( !empty($_POST['b_name']) ) ? trim(stripslashes($_POST['b_name'])) : 'YOUR BUSINESS NAME';
		$be = ( !empty($_POST['b_email']) ) ? trim(stripslashes($_POST['b_email'])) : 'YOUR EMAIL ADDRESS';
		$ba = ( !empty($_POST['b_addr']) ) ? trim(stripslashes($_POST['b_addr'])) : 'YOUR BUSINESS ADDRESS';
		$bc = ( !empty($_POST['b_country']) ) ? trim(stripslashes($_POST['b_country'])) : 'YOUR COUNTRY';
	
		$content = str_replace('{%BUSINESS_NAME%}', $bn, $content);
		$content = str_replace('{%EMAIL_ADDRESS%}', $be, $content);
		$content = str_replace('{%BUSINESS_ADDRESS%}', $ba, $content);
		$content = str_replace('{%COUNTRY%}', $bc, $content);
		
		$response = array(
				'success' => true,
				'content' => $content,
			);
			
		wp_send_json($response);
	}
	die();
}

add_action('wp_ajax_ib2_search_images', 'ib2_ajax_search_images');
function ib2_ajax_search_images() {
	if ( isset($_REQUEST['query']) ) {
		$error = 'none';
		$options = get_option('ib2_options');
		$query = sanitize_text_field($_REQUEST['query']);
		$start = ( isset($_REQUEST['start']) ) ? (int) $_REQUEST['start'] : 0;
		
		$cache_key = 'ib2_' . md5($query . $start);
		$images = false; //get_transient($cache_key);
		
		if ( empty($images) || !$image || !is_array($images) ) {
			$use_pixabay = ( !empty($options['pixabay_key']) && !empty($options['pixabay_id']) ) ? TRUE : FALSE;
			$images = array();
			
			$url  = "https://ajax.googleapis.com/ajax/services/search/images?";
			$url .= "v=1.0&q=" . urlencode($query) . "&start={$start}&imgsz=small|medium|large|xlarge|xxlarge&rsz=8&as_rights=(cc_publicdomain|cc_attribute|cc_sharealike).-(cc_noncommercial|cc_nonderived)&userip=" . $_SERVER['REMOTE_ADDR'];
			
			$results = wp_remote_get($url);
			
			if ( is_wp_error($results) ) {
				$error = 'connection';
			}
			
			$data = json_decode($results['body']);
			$pages  = 0;
			if ( isset($data->responseData->cursor) ) {
				$cursor = $data->responseData->cursor;
				$pages  = isset($cursor->pages) ? count($cursor->pages) : 0;
			}
			
			if ( $pages <= 0 || !isset($data->responseData->results) ) {
				$error = 'noresults';
			}

			if ( isset($data->responseStatus) && $data->responseStatus == 403 ) {
				$error = 'nopixabay';
			}
		
			if ( $error == 'none' ) {
				foreach ( $data->responseData->results as $gl ) {
					$img = array();
					$img['title'] = esc_attr($gl->titleNoFormatting);
					$img['url'] = esc_url($gl->url);
					$img['thumb'] = esc_url($gl->tbUrl);
					$img['width'] = $gl->width;
					$img['height'] = $gl->height;
					$img['source'] = $gl->visibleUrl;
				
					$images[] = $img;
					unset($img);
				}
			}	
			
			// Pixabay
			if ( !empty($options['pixabay_key']) && !empty($options['pixabay_id']) ) {
				$px_page = $start + 1;
				$px_username = stripslashes($options['pixabay_id']);
				$px_api_key = stripslashes($options['pixabay_key']);
				$pixabay = ib2_pixabay_images($px_username, $px_api_key, $query, $px_page);
				if ( $pixabay && isset($pixabay->totalHits) && $pixabay->totalHits > 0 ) {
					$error = 'none';
					$px_pages = floor($pixabay->totalHits / 10);
					$pages = ( $pages < $px_pages ) ? $px_pages : $pages;
					foreach ( $pixabay->hits as $pxi ) {
						$img = array();
						$img['title'] = esc_attr($pxi->tags);
						$img['url'] = esc_url($pxi->webformatURL);
						$img['thumb'] = esc_url($pxi->previewURL);
						$img['width'] = $pxi->webformatWidth;
						$img['height'] = $pxi->webformatHeight;
						$img['source'] = 'Pixabay.com';
						
						$images[] = $img;
						unset($img);
					}
				}
			}
			
			// Flickr
			if ( !empty($options['flickr']) ) {
				$fl_page = $start + 1;
				$fl_api_key = stripslashes($options['flickr']);
				$flickr = ib2_flickr_search($fl_api_key, $query, $fl_page);
				if ( $flickr && isset($flickr->photos) && isset($flickr->photos->photo) && $flickr->photos->photo > 0 ) {
					$error = 'none';
					$fl_pages = floor($flickr->photos->pages / 5);
					$pages = ( $pages < $fl_pages ) ? $fl_pages : $pages;
					foreach ( $flickr->photos->photo as $flk ) {
						if ( $flk->ispublic != 1 ) continue;
						$fl_photo = ib2_flickr_image($fl_api_key, $flk->id);
						if ( $fl_photo && isset($fl_photo->sizes) ) {
							foreach ( $fl_photo->sizes->size as $fph ) {
								$img = array();
								$img['title'] = esc_attr($flk->title);
								$img['source'] = 'Flickr.com';
								
								if ( $fph->label == 'Thumb' ) {
									$img['thumb'] = esc_url($fph->source);
								}
								
								if ( $fph->label == 'Small' ) {
									$img['thumb'] = esc_url($fph->source);
								}
								
								if ( $fph->label == 'Large' ) {
									$img['url'] = esc_url($fph->source);
									$img['width'] = $fph->width;
									$img['height'] = $fph->height;
									if ( !isset($img['thumb']) ) {
										$img['thumb'] = esc_url($fph->source);
									}
								}
								
								if ( $fph->label == 'Original' ) {
									$img['url'] = esc_url($fph->source);
									$img['width'] = $fph->width;
									$img['height'] = $fph->height;
									if ( !isset($img['thumb']) ) {
										$img['thumb'] = esc_url($fph->source);
									}
								}
								
								$images[] = $img;
								unset($img);
							}
						}
					}
				}
			}
			
			if ( $error == 'noresults' || empty($images) ) {
				$response = array( 
					'success' => true,
					'output' => '<p>No search results for <em>"' . $query . '"</em>. Please try another search.</p>',
					'pages' => 0
				);
				wp_send_json($response);
				die();
			}

			if ( $error == 'nopixabay' ) {
				$response = array( 
					'success' => true,
					'output' => '<p>ERROR: To enable image search, you MUST integrate InstaBuilder 2.0 with Pixabay or Flickr first. Please go to the InstaBuilder 2.0 settings page to do so.</p>',
					'pages' => 0
				);
				wp_send_json($response);
				die();
			}

			if ( $error == 'connection' ) {
				$response = array( 
					'success' => false
				);
				wp_send_json_error($response);
				die();
			}
			
			// cache images
			set_transient($cache_key, $images, 1800);
		}

		$output = '';
		foreach ( $images as $image ) {
			if ( isset($image['thumb']) && isset($image['url']) ) :
				$output .= '<div class="isearch-thumb isearch-result">';
				$output .= '<img src="' . esc_url($image['thumb']) . '" border="0" title="' . esc_attr($image['title']) . '" data-src="' . esc_url($image['url']) . '" />';
				$output .= '<div class="img-size-info"><small>' . $image['width'] . ' x ' . $image['height'] . ' &bull; ' . $image['source'] . '</small></div>';
				$output .= '<button type="button" class="btn btn-primary btn-xs use-this-image">Use Image</button>';
				$output .= '</div>';
			endif;
		}

		$response = array(
				'success' => true,
				'output' => $output,
				'pages'	=> $pages
			);
			
		wp_send_json($response);
	}
	die();
}

/*
add_action('wp_ajax_ib2_sort_fields', 'ib2_ajax_sort_fields');
function ib2_ajax_sort_fields() {
	if ( isset($_REQUEST['postdata']) ) {
		require_once( IB2_INC . 'simple_html_dom.php');
		
		$vars = rawurldecode($_REQUEST['postdata']);
		parse_str($vars, $post);
		ib2_dump($post);
	}
	die();
}
*/

add_action('wp_ajax_ib2_update_permalink', 'ib2_ajax_update_permalink');
function ib2_ajax_update_permalink() {
	if ( isset($_REQUEST['new_slug']) ) {
		$post_id = isset($_POST['post_id'])? intval($_POST['post_id']) : 0;
		$post = get_post($post_id);
		$title = sanitize_text_field(ib2_esc($post->post_title));
		$slug = isset($_POST['new_slug'])? sanitize_text_field($_POST['new_slug']) : null;
		list($permalink, $post_name) = get_sample_permalink($post_id, $title, $slug);
		
		$the_post = array(
				'ID' => $post_id,
				'post_title' => $title
		  	);
				
		$GLOBALS['newSlug'] = $post_name;
		$update_post_slug = create_function('$data', '$data["post_name"] = $GLOBALS["newSlug"]; return $data;');
		add_filter('wp_insert_post_data', $update_post_slug);

		// Update the post into the database
		wp_update_post($the_post);
			
		remove_filter('wp_insert_post_data', $update_post_slug);
			
		if ( strlen($post_name) > 30 ) {
			$post_name_abridged = substr($post_name, 0, 14). '&hellip;' . substr($post_name, -14);
		} else {
			$post_name_abridged = $post_name;
		}
						
		$response = array(
				'success' => true,
				'new_url' => str_replace(array('%pagename%','%postname%'), $post_name, $permalink),
				'display_url' => str_replace(array('%pagename%','%postname%'), $post_name_abridged, $permalink),
				'post_name'	=> $post_name
			);
		wp_send_json($response);
	}
	die();
}

add_action('wp_ajax_ib2_change_permalink', 'ib2_ajax_change_permalink');
function ib2_ajax_change_permalink() {
	if ( isset($_REQUEST['new_slug']) ) {
		$post_id = isset($_POST['post_id'])? intval($_POST['post_id']) : 0;
		$title = isset($_POST['post_title'])? $_POST['post_title'] : '';
		$slug = isset($_POST['new_slug'])? sanitize_text_field($_POST['new_slug']) : null;
		list($permalink, $post_name) = get_sample_permalink( $post_id, $title, $slug );
		
		$response = array(
				'success' => true,
				'permalink' => $permalink,
				'post_name'	=> $post_name
			);
		wp_send_json($response);
	}
	die();
}

add_action('wp_ajax_ib2_get_graphics', 'ib2_ajax_get_graphics');
function ib2_ajax_get_graphics() {
	if ( isset($_REQUEST['folder']) ) {
		$folder = $_REQUEST['folder'];
		$type = 'small';
		if ( $folder == 'badges' )
			$type = 'medium';
		
		if ( $folder == 'buttons'
			|| $folder == 'buy_buttons' 
			|| $folder == 'buy_now_area' 
			|| $folder == 'credit_cards' 
			|| $folder == 'texts' 
			|| $folder == 'cartoon'
			|| $folder == 'others' 
		) {
			$type = 'big';
		}
		
		ib2_graphics_html( $folder, $type );
	}
	die();
}

add_action('wp_ajax_ib2_change_menu', 'ib2_ajax_change_menu');
function ib2_ajax_change_menu() {
	if ( isset($_REQUEST['menu']) ) {
		$menu = $_REQUEST['menu'];
		$class = $_REQUEST['style'];
		wp_nav_menu( array( 'menu' => $menu, 'container' => 'ul', 'menu_class' => 'ib2-navi ib2-navi-' . $class, 'menu_id' => $_REQUEST['id'] . '-nav', 'fallback_cb' => '') );
	}
	die();
}

add_action('wp_ajax_ib2_check_tmpl', 'ib2_ajax_check_tmpl');
function ib2_ajax_check_tmpl() {
	if ( isset($_REQUEST['name']) && isset($_REQUEST['type']) ) {
		global $wpdb;
		
		$name = sanitize_text_field($_REQUEST['name']);
		$type = $_REQUEST['type'];
		
		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}ib2_templates` WHERE `name` = %s AND `type` = %s", $name, $type));
		if ( $row ) {
			echo $row->ID;
		} else {
			die('none');
		}
	}
	die();
}

add_action('wp_ajax_ib2_gate_url', 'ib2_ajax_gate_url');
function ib2_ajax_gate_url() {
	if ( isset($_REQUEST['thanks_id']) && isset($_REQUEST['code']) ) {
		$thanks_id = (int) $_REQUEST['thanks_id'];
		$code = trim(stripslashes($_REQUEST['code']));
		if ( empty($thanks_id) )
			die('Please select a thank you page first');
		
		$url = add_query_arg('ib2uc', $code, get_permalink($thanks_id));
		die(esc_url($url));
	}
}

add_action('wp_ajax_ib2_remote_copy', 'ib2_ajax_remote_copy');
function ib2_ajax_remote_copy() {
	if ( isset($_REQUEST['content']) ) {
		$data = array(
			'type' => $_REQUEST['type'],
			'content' => stripslashes($_REQUEST['content']),
			'elid' => $_REQUEST['elid'],
			'dat' => stripslashes_deep($_REQUEST['dat'])
		);
		set_transient('ib2_remote_element', $data, 3600);
		die();
	}
}

add_action('wp_ajax_ib2_remote_paste', 'ib2_ajax_remote_paste');
function ib2_ajax_remote_paste() {
	$data = get_transient('ib2_remote_element');
	if ( empty($data) ) {
		$response = array( 'success' => false );
		wp_send_json_error( $response );
		die();
	}
	
	$response = array(
			'success' => true,
			'type' => $data['type'],
			'content' => stripslashes($data['content']),
			'elid' => $data['elid'],
			'dat' => stripslashes_deep($data['dat'])
		);
	wp_send_json($response);
	die();
}

add_action('wp_ajax_ib2_save_post', 'ib2_ajax_save_post');
function ib2_ajax_save_post() {
	if ( isset($_REQUEST['post_id']) ) {
		global $wpdb;
		
		$post_id = (int) $_REQUEST['post_id'];
		$allcss = ( !empty($_POST['allcss']) ) ? $_POST['allcss'] : array();
	
		if ( count($allcss) > 0 ) {
			foreach ( $allcss as $k => $v ) {
				$str = stripslashes($v);
				$allcss[$k] = base64_decode($str);
			}
		}

		$save_result = true;
		$autosave = ( isset($_POST['autosave']) && $_POST['autosave'] == 'yes' ) ? TRUE : FALSE;
		$quiz = ( !empty($_POST['quiz']) ) ? stripslashes_deep($_POST['quiz']) : array();
		$video_data = ( !empty($_POST['videoData']) ) ? stripslashes_deep($_POST['videoData']) : array();
		$carousel_data = ( !empty($_POST['carouselData']) ) ? stripslashes_deep($_POST['carouselData']) : array();
		$optincodes = isset($_POST['optincodes']) ? stripslashes($_POST['optincodes']) : '';
		$variation = $_POST['variation'];
		$page_data = array(
				'allcss' => $allcss,
				'quiz' => $quiz,
				'page_width' => stripslashes($_POST['pageWidth']),
				'font_face' => stripslashes($_POST['fontFace']),
				'font_color' => stripslashes($_POST['fontColor']),
				'font_size' => stripslashes($_POST['fontSize']),
				'line_height' => stripslashes($_POST['lineHeight']),
				'white_space' => stripslashes($_POST['whiteSpace']),
				'link_color' => stripslashes($_POST['fontSize']),
				'link_hover_color' => stripslashes($_POST['linkHoverColor']),
				'background_color' => stripslashes($_POST['backgroundColor']),
				'background_img' => base64_decode(stripslashes($_POST['backgroundImg']), 'instabuilder2'),
				'background_img_mode' => stripslashes($_POST['backgroundImgMode']),
				'background_repeat' => stripslashes($_POST['backgroundRepeat']),
				'background_pos' => stripslashes($_POST['backgroundPos']),
				'background_attach' => stripslashes($_POST['backgroundAttach']),
				'background_video' => base64_decode(stripslashes($_POST['backgroundVideo']), 'instabuilder2'),
				'background_video_mute' => $_POST['backgroundVideoMute'],
				'background_video_loop' => $_POST['backgroundVideoLoop'],
				'background_video_ctrl' => $_POST['backgroundVideoCtrl'],
				'title' => sanitize_text_field(stripslashes($_POST['title'])),
				'css' => base64_decode(trim(stripslashes($_POST['css']))),
				'content_style' => base64_decode(trim(stripslashes($_POST['contentStyle']))),
				'meta_desc' => sanitize_text_field(stripslashes($_POST['metaDesc'])),
				'meta_keys' => sanitize_text_field(stripslashes($_POST['metaKeys'])),
				'noindex' => stripslashes($_POST['noindex']),
				'nofollow' => stripslashes($_POST['nofollow']),
				'noodp' => stripslashes($_POST['noodp']),
				'noydir' => stripslashes($_POST['noydir']),
				'noarchive' => stripslashes($_POST['noarchive']),
				'head_scripts' => base64_decode(stripslashes($_POST['headScripts'])),
				'body_scripts' => base64_decode(stripslashes($_POST['bodyScripts'])),
				'footer_scripts' => base64_decode(stripslashes($_POST['footerScripts'])),
				'favicon' => base64_decode(stripslashes($_POST['favicon'])),
				'popup' => $_POST['popup'],
				'popup_time' => stripslashes($_POST['popupTime']),
				'popup_id' => stripslashes($_POST['popupId']),
				'slider' => ( isset($_POST['slider']) ? $_POST['slider'] : 0 ),
				'slider_time' => stripslashes($_POST['sliderTime']),
				'slider_content' => ( isset($_POST['sliderContent']) ? base64_decode(stripslashes($_POST['sliderContent'])) : '' ),
				'slider_close' => ( isset($_POST['sliderClose']) ? $_POST['sliderClose'] : 0 ),
				'attention_bar' => $_POST['attentionBar'],
				'attention_bar_text' => base64_decode(stripslashes($_POST['attentionBarText'])),
				'attention_bar_time' => stripslashes($_POST['attentionBarTime']),
				'attention_bar_anchor' => base64_decode(strip_tags(stripslashes($_POST['attentionBarAnchor']))),
				'attention_bar_url' => base64_decode(strip_tags(stripslashes($_POST['attentionBarUrl']))),
				'attention_bar_background' => stripslashes($_POST['attentionBarBackground']),
				'attention_bar_border' => stripslashes($_POST['attentionBarBorder']),
				'attention_bar_font' => stripslashes($_POST['attentionBarFont']),
				'attention_bar_fontcolor' => stripslashes($_POST['attentionBarFontcolor']),
				'exit_splash' => $_POST['exitSplash'],
				'exit_msg' => base64_decode(strip_tags(stripslashes($_POST['exitMsg']))),
				'exit_url' => base64_decode(sanitize_text_field(stripslashes($_POST['exitUrl']))),
				'right_click' => $_POST['rightClick'],
				'right_click_msg' => base64_decode(strip_tags(stripslashes($_POST['rightClickMsg']))),
				'right_click_img' => $_POST['rightClickImg'],
				'welcome_gate' => isset($_POST['welcomeGate']) ? $_POST['welcomeGate'] : 0,
				'locked_page' => isset($_POST['wgateId']) ? $_POST['wgateId'] : 0,
				'gate_type' => isset($_POST['gateType']) ? $_POST['gateType'] : 'welcome',
				'gate_thanks_id' => isset($_POST['gateThanksId']) ? $_POST['gateThanksId'] : array(),
				'gate_code' => isset($_POST['gateCode']) ? $_POST['gateCode'] : '',
				'content' => base64_decode(trim(stripslashes($_POST['content']))),
				'optincodes' => base64_decode($optincodes),
				'circular' => $_POST['circular'],
				'video_data' => $video_data,
				'carousel_data' => $carousel_data
			);
		
		if ( $_POST['status'] == 'template' && isset($_POST['templateName']) ) {
			$template_id = ( isset($_POST['templateID']) ) ? (int) $_POST['templateID'] : 0;
			$template = ib2_get_template($template_id);
			if ( !$template )
				$metadata = array();
			else {
				$metadata = maybe_unserialize($template->metadata);
				if ( !is_array($metadata) )
					$metadata = array();
			}
			
			// Temp code to create built-in templates
			$local_plugin_dir = 'http://localhost/wordpress/wp-content/plugins/instabuilder2/';
			$page_data['css'] = str_replace($local_plugin_dir, '{%IB2_PLUGIN_DIR%}', $page_data['css']);
			$page_data['background_img'] = str_replace($local_plugin_dir, '{%IB2_PLUGIN_DIR%}', $page_data['background_img']);
			$page_data['content_style'] = str_replace($local_plugin_dir, '{%IB2_PLUGIN_DIR%}', $page_data['content_style']);
			$page_data['content'] = str_replace($local_plugin_dir, '{%IB2_PLUGIN_DIR%}', $page_data['content']);
			// End of temp code

			// Temp code to create built-in templates
			$local_plugin_dir = 'http://localhost/wordpress/wp-content/uploads/' . date("Y") . '/' . date("m") . '/';
			$page_data['css'] = str_replace($local_plugin_dir, '{%IB2_PLUGIN_DIR%}/assets/img/templates/', $page_data['css']);
			$page_data['background_img'] = str_replace($local_plugin_dir, '{%IB2_PLUGIN_DIR%}/assets/img/templates/', $page_data['background_img']);
			$page_data['content_style'] = str_replace($local_plugin_dir, '{%IB2_PLUGIN_DIR%}/assets/img/templates/', $page_data['content_style']);
			$page_data['content'] = str_replace($local_plugin_dir, '{%IB2_PLUGIN_DIR%}/assets/img/templates/', $page_data['content']);
			// End of temp code

			// Temp code to create built-in templates
			$local_plugin_dir = 'http://shavuotkreatifsolusi.com/ibtheme/wp-content/plugins/instabuilder2/';
			$page_data['css'] = str_replace($local_plugin_dir, '{%IB2_PLUGIN_DIR%}', $page_data['css']);
			$page_data['background_img'] = str_replace($local_plugin_dir, '{%IB2_PLUGIN_DIR%}', $page_data['background_img']);
			$page_data['content_style'] = str_replace($local_plugin_dir, '{%IB2_PLUGIN_DIR%}', $page_data['content_style']);
			$page_data['content'] = str_replace($local_plugin_dir, '{%IB2_PLUGIN_DIR%}', $page_data['content']);
			// End of temp code
			
			$metadata['conversion_type'] = $_POST['conversionType'];
			$metadata['conversion_id'] = $_POST['conversionID'];
			$metadata['variation' . $variation] = $page_data;
			$db_data = array(
				'name' => sanitize_text_field($_POST['templateName']),
				'type' => sanitize_text_field($_POST['templateType']),
				'subtype' => sanitize_text_field($_POST['templateSubType']),
				'screenshot' => sanitize_text_field($_POST['templateThumb']),
				'tags' => sanitize_text_field($_POST['templateTags']),
				'metadata' => maybe_serialize($metadata),
				'created' => date_i18n("Y-m-d H:i:s"),
				'mode' => 'custom'
			);
				
			$edit = ( !empty($template_id) ) ? true : false;
			if ( $edit ) {
				$wpdb->update( "{$wpdb->prefix}ib2_templates", $db_data, array('ID' => $template_id) );
				//remove cache
				wp_cache_delete('template_' . $template_id, 'ib2_templates');
			} else
				$wpdb->insert( "{$wpdb->prefix}ib2_templates", $db_data );
			
			$save_result = true;
		} else {
			
			// PAGE GATE SETTINGS
			// Revert the data first regarless if this feature is enabled or not ...
			if ( $gate_meta = get_post_meta($post_id, 'ib2_gate_master', true) ) {
				if ( !empty($gate_meta['lock_ids']) ) {
					$old_lock_ids = $gate_meta['lock_ids'];
					if ( is_array($old_lock_ids) && count($old_lock_ids) > 0 ) {
						foreach ( $old_lock_ids as $old_lock_id ) {
							delete_post_meta($old_lock_id, 'ib2_welcome_gate');
						}
					}
				}
				
				// Back Compat
				if ( !empty($gate_meta['lock_id']) ) {
					$old_lock_id = (int) $gate_meta['lock_id'];
					delete_post_meta($old_lock_id, 'ib2_welcome_gate');
				}
			}
			
			if ( !empty($_POST['welcomeGate']) && !empty($_POST['wgateId']) ) {
				$lock_ids = $_POST['wgateId'];
				if ( !is_array($lock_ids) ) $lock_ids = array($lock_ids);
				$gate_data = array('gate_id' => $post_id);
				foreach ( $lock_ids as $lock_id ) {
					update_post_meta($lock_id, 'ib2_welcome_gate', $gate_data);
				}
				
				$gate_type = isset($_POST['gateType']) ? $_POST['gateType'] : 'welcome';
				update_post_meta($post_id, 'ib2_gate_master', array('lock_ids' => $lock_ids, 'type' => $gate_type));
			}
			
			$metadata = get_post_meta($post_id, 'ib2_settings', true);
			if ( !is_array($metadata) )
				$metadata = array();
			
			$metadata['enable'] = 'yes';
			$metadata['conversion_type'] = $_POST['conversionType'];
			$tp_id =  $_POST['conversionID'];
			$metadata['conversion_id'] = $tp_id;
			
			$metadata['welcome_gate'] = isset($_POST['welcomeGate']) ? $_POST['welcomeGate'] : 0;
			$metadata['locked_page'] = isset($_POST['wgateId']) ? $_POST['wgateId'] : 0;
			$metadata['gate_type'] = isset($_POST['gateType']) ? $_POST['gateType'] : 'welcome';
			$metadata['gate_thanks_id'] = isset($_POST['gateThanksId']) ? $_POST['gateThanksId'] : array();
			$metadata['gate_code'] = isset($_POST['gateCode']) ? $_POST['gateCode'] : '';
				
			$metadata['variation' . $variation] = $page_data;
			
			//if ( isset($metadata['variation' . $variation]) )
				//unset($metadata['variation' . $variation]);
			
			//$variant_name = 'variation' . $variation;
			
			$title = sanitize_text_field(stripslashes($_POST['title']));
			$the_post = array(
				'ID' => $post_id,
				'post_title' => $title
		  	);
		
			if ( isset($_POST['status']) && $_POST['status'] == 'publish' ) {
				$the_post['post_status'] = 'publish';
			}
			
			if ( isset($_POST['oldSlug']) && isset($_POST['newSlug']) && $_POST['oldSlug'] != $_POST['newSlug'] ) {
				$update_post_slug = create_function('$data', '$data["post_name"] = $_POST["newSlug"]; return $data;');
				add_filter('wp_insert_post_data', $update_post_slug);
			}
			
			// Update the post into the database
		  	wp_update_post($the_post);
			
			if ( isset($update_post_slug) ) {
				remove_filter('wp_insert_post_data', $update_post_slug);
			}
			
			// Update the post meta
			update_post_meta($post_id, 'ib2_settings', $metadata);
			
			// Update variant metadata
			//update_post_meta($post_id, $variant_name, $page_data);
			
			// add/update into IB 2.0 pages table
			ib2_page_entry($post_id);
			
			// SAVE HISTORY
			if ( !$autosave ) :
				$history_data = array(
					'post_id' => $post_id,
					'variant' => $variation,
					'content' => maybe_serialize($page_data),
					'date' => time()
				);
				$history = ib2_save_history($history_data);
			endif;
			
			// update traffic weight
			if ( isset($_POST['weight']) && is_array($_POST['weight']) ) {
				foreach ( $_POST['weight'] as $k => $v ) {
					ib2_variant_entry("post_id={$post_id}&variant={$k}&weight={$v}");
				}
			}
			
			// assign thank you page
			if ( !empty($tp_id) ) {
				$tp_meta = get_post_meta($tp_id, 'ib2_conversions', true);
				if ( !is_array($tp_meta) )
					$tp_meta = array();
				
				$tp_meta[] = $post_id;
				
				$tp_meta = array_unique($tp_meta);
				update_post_meta($tp_id, 'ib2_conversions', $tp_meta);
			}
			
			// save quiz
			if ( count($quiz) > 0 ) {
				$new_quiz = ib2_save_quiz($post_id, $variation);
				foreach ( $quiz as $qz ) {
					$args = array();
					$args['post_id'] = $post_id;
					$args['order'] = $qz['order'];
					$args['variant'] = $variation;
					$args['question'] = trim($qz['question']);
					
					$a = 1;
					foreach ( $qz['answers'] as $ans ) {
						$args['a' . $a] = trim($ans['answer']);
						$a++;
					}

					@ib2_save_question($args);
				}
			} else {
				// attempt to delete if there's any previous quiz
				ib2_delete_quiz($post_id, $variation);
			}
		}
		
		$post = get_post($post_id);
		/**
		 * Fires once a post has been saved.
		 */
		do_action( "save_post_{$post->post_type}", $post_id, $post, true );
		
		/**
		 * Fires once a post has been saved.
		 */
		do_action( 'save_post', $post_id, $post, true );
		
		// Send back the response
		$response = array(
			'success' => $save_result,
		);
			
		wp_send_json($response);
	}
	die();
}