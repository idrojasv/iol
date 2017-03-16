<?php

class Mailster {

	private $template;
	private $post_data;
	private $campaign_data;
	private $mail = array();
	private $tables = array( 'actions', 'forms', 'forms_lists', 'form_fields', 'links', 'lists', 'lists_subscribers', 'queue', 'subscribers', 'subscriber_fields', 'subscriber_meta' );

	public $wp_mail = null;

	private $_classes = array();

	static $form_active;

	public function __construct() {

		register_activation_hook( MAILSTER_FILE, array( &$this, 'activate' ) );
		register_deactivation_hook( MAILSTER_FILE, array( &$this, 'deactivate' ) );

		$classes = array( 'settings', 'translations', 'campaigns', 'subscribers', 'lists', 'forms', 'manage', 'templates', 'widget', 'frontpage', 'statistics', 'ajax', 'tinymce', 'cron', 'queue', 'actions', 'bounce', 'dashboard', 'update', 'upgrade', 'helpmenu', 'register', 'geo', 'empty' );

		add_action( 'plugins_loaded', array( &$this, 'init' ), 1 );

		add_action( 'widgets_init', create_function( '', 'register_widget( "Mailster_Signup_Widget" );register_widget( "Mailster_Newsletter_List_Widget" );register_widget( "Mailster_Newsletter_Subscriber_Button_Widget" );register_widget( "Mailster_Newsletter_Subscribers_Count_Widget" );' ) );

		foreach ( $classes as $class ) {
			require_once MAILSTER_DIR . "classes/$class.class.php";
			$classname = 'Mailster' . ucwords( $class );
			if ( class_exists( $classname ) ) {
				$this->_classes[ $class ] = new $classname();
			}
		}

		$this->wp_mail = function_exists( 'wp_mail' );

	}


	/**
	 *
	 *
	 * @param unknown $class
	 * @param unknown $args
	 * @return unknown
	 */
	public function __call( $class, $args ) {

		if ( ! isset( $this->_classes[ $class ] ) ) {
			if ( WP_DEBUG ) {
				throw new Exception( "Class $class doesn't exists", 1 );
			} else {
				$class = 'empty';
			}
		}

		return $this->_classes[ $class ];
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function stats() {
		return $this->statistics();
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function mail() {
		require_once MAILSTER_DIR . 'classes/mail.class.php';

		return MailsterMail::get_instance();
	}


	/**
	 *
	 *
	 * @param unknown $content (optional)
	 * @return unknown
	 */
	public function placeholder( $content = '' ) {
		require_once MAILSTER_DIR . 'classes/placeholder.class.php';

		return new MailsterPlaceholder( $content );
	}


	/**
	 *
	 *
	 * @param unknown $file     (optional)
	 * @param unknown $template (optional)
	 * @return unknown
	 */
	public function notification( $file = 'notification.html', $template = null ) {
		require_once MAILSTER_DIR . 'classes/notification.class.php';
		if ( is_null( $template ) ) {
			$template = 'basic';
		}

		return MailsterNotification::get_instance( $template, $file );
	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @param unknown $file (optional)
	 * @return unknown
	 */
	public function template( $slug = null, $file = null ) {

		static $template;
		if ( $template ) {
			return $template;
		}

		if ( is_null( $slug ) ) {
			$slug = mailster_option( 'default_template', 'mailster' );
		}
		$file = is_null( $file ) ? 'index.html' : $file;
		require_once MAILSTER_DIR . 'classes/template.class.php';

		$template = new MailsterTemplate( $slug, $file );

		return $template;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function form() {

		require_once MAILSTER_DIR . 'classes/form.class.php';
		return new MailsterForm();
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function helper() {

		require_once MAILSTER_DIR . 'classes/helper.class.php';
		return new MailsterHelper();
	}



	public function init() {

		// remove revisions if newsletter is finished
		add_action( 'mailster_reset_mail', array( &$this, 'reset_mail_delayed' ), 10, 3 );

		add_action( 'mailster_cron', array( &$this, 'check_homepage' ) );
		add_action( 'mailster_cron', array( &$this, 'check_compatibility' ) );

		$this->wp_mail_setup();

		if ( is_admin() ) {

			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts_styles' ), 10, 1 );
			add_action( 'admin_menu', array( &$this, 'special_pages' ), 60 );
			add_action( 'admin_notices', array( &$this, 'admin_notices' ) );

			add_filter( 'plugin_action_links', array( &$this, 'add_action_link' ), 10, 2 );
			add_filter( 'plugin_row_meta', array( &$this, 'add_plugin_links' ), 10, 2 );

			add_filter( 'install_plugin_complete_actions', array( &$this, 'add_install_plugin_complete_actions' ), 10, 3 );

			add_filter( 'add_meta_boxes', array( &$this, 'add_homepage_info' ), 10, 3 );

			add_filter( 'wp_import_post_data_processed', array( &$this, 'import_post_data' ), 10, 2 );
			add_filter( 'display_post_states', array( &$this, 'display_post_states' ), 10, 2 );

			// frontpage stuff (!is_admin())
		} else {

		}

		do_action( 'mailster', $this );

	}


	public function save_admin_notices() {

		global $mailster_notices;

		update_option( 'mailster_notices', empty( $mailster_notices ) ? null : (array) $mailster_notices );

	}


	public function admin_notices() {

		global $mailster_notices;

		if ( $mailster_notices = get_option( 'mailster_notices', array() ) ) {

			$successes = array();
			$errors = array();
			$infos = array();
			$warnings = array();
			$msg;
			$dismiss = isset( $_GET['mailster_remove_notice_all'] ) ? esc_attr( $_GET['mailster_remove_notice_all'] ) : false;

			if ( isset( $_GET['mailster_remove_notice'] ) ) {

				unset( $mailster_notices[ $_GET['mailster_remove_notice'] ] );

				update_option( 'mailster_notices', $mailster_notices );

			}

			if ( ! is_array( $mailster_notices ) ) {
				$mailster_notices = array();
			}

			foreach ( $mailster_notices as $id => $notice ) {

				if ( isset( $notice['cap'] ) && ! empty( $notice['cap'] ) ) {

					// specific users or admin
					if ( is_numeric( $notice['cap'] ) ) {
						if ( get_current_user_id() != $notice['cap'] && ! current_user_can( 'manage_options' ) ) {
							continue;
						}

						// certain capability
					} else {
						if ( ! current_user_can( $notice['cap'] ) ) {
							continue;
						}
					}
				}

				$type = esc_attr( $notice['type'] );

				$classes = array( 'notice', 'mailster-notice', 'notice-' . $type );
				if ( 'success' == $type ) {
					$classes[] = 'updated';
				}
				if ( 'error' == $type ) {
					$classes[] = 'error';
				}

				$msg = '<div data-id="' . $id . '" id="mailster-notice-' . $id . '" class="' . implode( ' ', $classes ) . '">';

				$text = ( isset( $notice['text'] ) ? $notice['text'] : '' );
				$text = isset( $notice['cb'] ) && function_exists( $notice['cb'] )
					? call_user_func( $notice['cb'], $text )
					: $text;

				if ( $text === false ) {
					continue;
				}

				$msg .= '<p>' . ( $text ? $text : '&nbsp;' ) . '</p>';
				if ( ! $notice['once'] || is_numeric( $notice['once'] ) ) {
					$msg .= '<a class="notice-dismiss" title="' . esc_attr__( 'Dismiss this notice (Alt-click to dismiss all notices)', 'mailster' ) . '" href="' . add_query_arg( array( 'mailster_remove_notice' => $id ) ) . '">' . esc_attr__( 'Dismiss', 'mailster' ) . '<span class="screen-reader-text">' . esc_attr__( 'Dismiss this notice (Alt-click to dismiss all notices)', 'mailster' ) . '</span></a>';

					if ( is_numeric( $notice['once'] ) && (int) $notice['once'] - time() < 0 ) {
						unset( $mailster_notices[ $id ] );
					}
				} else {
					unset( $mailster_notices[ $id ] );
				}

				$msg .= '</div>';

				if ( $notice['type'] == 'success' && $dismiss != 'success' ) {
					$successes[] = $msg;
				}

				if ( $notice['type'] == 'error' && $dismiss != 'error' ) {
					$errors[] = $msg;
				}

				if ( $notice['type'] == 'info' && $dismiss != 'info' ) {
					$infos[] = $msg;
				}

				if ( $notice['type'] == 'warning' && $dismiss != 'warning' ) {
					$warnings[] = $msg;
				}

				if ( 'success' == $dismiss && isset( $mailster_notices[ $id ] ) ) {
					unset( $mailster_notices[ $id ] );
				}

				if ( 'error' == $dismiss && isset( $mailster_notices[ $id ] ) ) {
					unset( $mailster_notices[ $id ] );
				}

				if ( 'info' == $dismiss && isset( $mailster_notices[ $id ] ) ) {
					unset( $mailster_notices[ $id ] );
				}

				if ( 'warning' == $dismiss && isset( $mailster_notices[ $id ] ) ) {
					unset( $mailster_notices[ $id ] );
				}
			}

			$suffix = SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'mailster-notice', MAILSTER_URI . 'assets/js/notice-script' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION, true );
			wp_enqueue_style( 'mailster-notice', MAILSTER_URI . 'assets/css/notice-style' . $suffix . '.css', array(), MAILSTER_VERSION );

			echo implode( '', $successes );
			echo implode( '', $errors );
			echo implode( '', $infos );
			echo implode( '', $warnings );

			add_action( 'shutdown', array( &$this, 'save_admin_notices' ) );

		}

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id (optional)
	 * @return unknown
	 */
	public function get_base_link( $campaign_id = '' ) {

		$is_permalink = mailster( 'helper' )->using_permalinks();

		$prefix = ! mailster_option( 'got_url_rewrite' ) ? '/index.php' : '/';

		return $is_permalink
			? home_url( $prefix . '/mailster/' . $campaign_id )
			: add_query_arg( 'mailster', $campaign_id, home_url( $prefix ) );

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id (optional)
	 * @return unknown
	 */
	public function get_unsubscribe_link( $campaign_id = '' ) {

		$is_permalink = mailster( 'helper' )->using_permalinks();

		$prefix = ! mailster_option( 'got_url_rewrite' ) ? '/index.php' : '/';

		$unsubscribe_homepage = get_page( mailster_option( 'homepage' ) );
		$unsubscribe_homepage = apply_filters( 'mymail_unsubscribe_link', apply_filters( 'mailster_unsubscribe_link', $unsubscribe_homepage ) );

		if ( $unsubscribe_homepage ) {
			$unsubscribe_homepage = get_permalink( mailster_option( 'homepage' ) );
		} else {
			$unsubscribe_homepage = get_bloginfo( 'url' );
		}

		$slugs = mailster_option( 'slugs' );
		$slug = isset( $slugs['unsubscribe'] ) ? $slugs['unsubscribe'] : 'unsubscribe';

		if ( ! $is_permalink ) {
			$unsubscribe_homepage = str_replace( trailingslashit( get_bloginfo( 'url' ) ), untrailingslashit( get_bloginfo( 'url' ) ) . $prefix, $unsubscribe_homepage );
		}

		return $is_permalink
			? trailingslashit( $unsubscribe_homepage ) . $slug
			: add_query_arg( 'mailster_unsubscribe', md5( $campaign_id . '_unsubscribe' ), $unsubscribe_homepage );

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $email       (optional)
	 * @return unknown
	 */
	public function get_forward_link( $campaign_id, $email = '' ) {

		$page = get_permalink( $campaign_id );

		return add_query_arg( array( 'mailster_forward' => urlencode( $email ) ), $page );

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $hash        (optional)
	 * @return unknown
	 */
	public function get_profile_link( $campaign_id, $hash = '' ) {

		$is_permalink = mailster( 'helper' )->using_permalinks();

		$prefix = ! mailster_option( 'got_url_rewrite' ) ? '/index.php' : '/';

		$homepage = get_page( mailster_option( 'homepage' ) )
			? get_permalink( mailster_option( 'homepage' ) )
			: get_bloginfo( 'url' );

		$slugs = mailster_option( 'slugs' );
		$slug = isset( $slugs['profile'] ) ? $slugs['profile'] : 'profile';

		if ( ! $is_permalink ) {
			$homepage = str_replace( trailingslashit( get_bloginfo( 'url' ) ), untrailingslashit( get_bloginfo( 'url' ) ) . $prefix, $homepage );
		}

		return $is_permalink
			? trailingslashit( $homepage ) . $slug
			: add_query_arg( 'mailster_profile', $hash, $homepage );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function check_link_structure() {

		$args = array();

		// only if permalink structure is used
		if ( mailster( 'helper' )->using_permalinks() ) {

			$hash = str_repeat( '0', 32 );

			$urls = array(
				trailingslashit( $this->get_unsubscribe_link( 0 ) ) . $hash,
				trailingslashit( $this->get_profile_link( 0 ) ) . $hash,
				trailingslashit( $this->get_base_link( 0 ) ) . $hash,
			);

			foreach ( $urls as $url ) {

				$response = wp_remote_get( $url, $args );

				$code = wp_remote_retrieve_response_code( $response );
				if ( $code != 200 ) {
					return false;
				}
			}
		}

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $content     (optional)
	 * @param unknown $hash        (optional)
	 * @param unknown $campaing_id (optional)
	 * @return unknown
	 */
	public function replace_links( $content = '', $hash = '', $campaing_id = '' ) {

		// get all links from the basecontent
		preg_match_all( '#href=(\'|")?(https?[^\'"]+)(\'|")?#', $content, $links );
		$links = $links[2];

		$used = array();

		$new_structure = mailster( 'helper' )->using_permalinks();
		$base = $this->get_base_link( $campaing_id );

		foreach ( $links as $link ) {

			$link = apply_filters( 'mymail_replace_link', apply_filters( 'mailster_replace_link', $link, $base, $hash ), $base, $hash );

			if ( $new_structure ) {
				$replace = trailingslashit( $base ) . $hash . '/' . rtrim( strtr( base64_encode( $link ), '+/', '-_' ), '=' );

				! isset( $used[ $link ] )
					? $used[ $link ] = 1
					: $replace .= '/' . ( $used[ $link ]++ );

			} else {

				$link = str_replace( array( '%7B', '%7D' ), array( '{', '}' ), $link );
				$target = rtrim( strtr( base64_encode( $link ), '+/', '-_' ), '=' );
				$replace = $base . '&k=' . $hash . '&t=' . $target;
				! isset( $used[ $link ] )
					? $used[ $link ] = 1
					: $replace .= '&c=' . ( $used[ $link ]++ );

			}

			$link = '"' . $link . '"';
			if ( ( $pos = strpos( $content, $link ) ) !== false ) {
				$content = substr_replace( $content, '"' . $replace . '"', $pos, strlen( $link ) );
			}
		}

		return $content;

	}


	/**
	 *
	 *
	 * @param unknown $offset    (optional)
	 * @param unknown $post_type (optional)
	 * @param unknown $term_ids  (optional)
	 * @param unknown $simple    (optional)
	 * @return unknown
	 */
	public function get_last_post( $offset = 0, $post_type = 'post', $term_ids = array(), $simple = false ) {

		global $wpdb;

		$key = md5( serialize( array( $offset, $post_type, $term_ids, $simple ) ) );

		$posts = mailster_cache_get( 'get_last_post' );

		if ( ! $posts ) {
			$posts = array();
		}

		if ( isset( $posts[ $key ] ) ) {
			return $posts[ $key ];
		}

		$args = array(
			'posts_per_page' => 1,
			'numberposts' => 1,
			'post_type' => $post_type,
			'offset' => $offset,
			'update_post_meta_cache' => false,
			'no_found_rows' => true,
			'cache_results' => false,
		);

		$exclude = mailster_cache_get( 'get_last_post_ignore' );

		if ( ! $exclude ) {
			$exclude = $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'mailster_ignore' AND meta_value != '0'" );
		}

		if ( ! empty( $exclude ) ) {
			$args['post__not_in'] = (array) $exclude;
		}

		mailster_cache_set( 'get_last_post_ignore', $exclude );

		if ( ! empty( $term_ids ) ) {

			$tax_query = array();

			$taxonomies = get_object_taxonomies( $post_type, 'names' );

			for ( $i = 0; $i < count( $term_ids ); $i++ ) {
				if ( empty( $term_ids[ $i ] ) ) {
					continue;
				}

				$tax_query[] = array(
					'taxonomy' => $taxonomies[ $i ],
					'field' => 'id',
					'terms' => explode( ',', $term_ids[ $i ] ),
				);
			}

			if ( ! empty( $tax_query ) ) {
				$tax_query['relation'] = 'AND';
				$args = wp_parse_args( $args, array( 'tax_query' => $tax_query ) );
			}
		} else {
			$args['update_post_term_cache'] = false;
		}

		$post = get_posts( $args );

		if ( $post ) {
			$post = $post[0];

			if ( ! $simple ) {

				if ( ! $post->post_excerpt ) {
					if ( preg_match( '/<!--more(.*?)?-->/', $post->post_content, $matches ) ) {
						$content = explode( $matches[0], $post->post_content, 2 );
						$post->post_excerpt = trim( $content[0] );
					}
				}

				if ( $length = apply_filters( 'mailster_excerpt_length', false ) ) {
					$post->post_excerpt = wp_trim_words( $post->post_excerpt, $length );
				}
				$post->post_excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );

				$post->post_content = apply_filters( 'the_content', $post->post_content );
			}
		} else {
			$post = false;
		}

		$posts[ $key ] = $post;

		mailster_cache_set( 'get_last_post', $posts );

		return $post;
	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @param unknown $userstyle  (optional)
	 * @param unknown $customhead (optional)
	 * @return unknown
	 */
	public function sanitize_content( $content, $userstyle = false, $customhead = null ) {
		if ( empty( $content ) ) {
			return '';
		}

		if ( function_exists( 'mb_convert_encoding' ) ) {
			$encoding = mb_detect_encoding( $content, 'auto' );
			if ( $encoding != 'UTF-8' ) {
				$content = mb_convert_encoding( $content, $encoding, 'UTF-8' );
			}
		}

		$content = stripslashes( $content );
		$bodyattributes = '';
		$pre_stuff = '';
		$protocol = ( is_ssl() ? 'https' : 'http' );

		preg_match( '#^(.*)?<head([^>]*)>(.*?)<\/head>#is', is_null( $customhead ) ? $content : stripslashes( $customhead ), $matches );
		if ( ! empty( $matches ) ) {
			$pre_stuff = $matches[1];
			$head = '<head' . $matches[2] . '>' . $matches[3] . '</head>';
		} else {
			$pre_stuff = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n" . '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n";
			$head = '<head>' . "\n\t" . '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n\t" . '<meta name="viewport" content="width=device-width" />' . "\n\t" . '<title>{subject}</title>' . "\n" . '</head>';
		}

		preg_match( '#<body([^>]*)>(.*?)<\/body>#is', $content, $matches );
		if ( ! empty( $matches ) ) {
			$bodyattributes = $matches[1];
			$bodyattributes = ' ' . trim( str_replace( array( 'position: relative;', 'mailster-loading', ' class=""', ' style=""' ), '', $bodyattributes ) );
			$body = $matches[2];
		} else {
			$body = $content;
		}

		// custom styles
		global $mailster_mystyles;

		if ( $userstyle && ! empty( $mailster_mystyles ) ) {
			// check for existing styles
			preg_match_all( '#(<style ?[^<]+?>([^<]+)<\/style>)#', $body, $originalstyles );

			if ( ! empty( $originalstyles[0] ) ) {
				foreach ( $mailster_mystyles as $style ) {
					$block = end( $originalstyles[0] );
					$body = str_replace( $block, $block . '<style type="text/css">' . $style . '</style>', $body );
				}
			}
		}

		$body = preg_replace( '#<div ?[^>]+?class=\"modulebuttons(.*)<\/div>#i', '', $body );
		$body = trim( preg_replace( '#<button[^>]*?>.*?</button>#i', '', $body ) );
		$content = $head . "\n<body$bodyattributes>\n" . apply_filters( 'mymail_sanitize_content_body', apply_filters( 'mailster_sanitize_content_body', $body ) ) . "\n</body></html>";

		$content = str_replace( '<body >', '<body>', $content );
		$content = str_replace( ' src="//', ' src="' . $protocol . '://', $content );
		$content = str_replace( ' href="//', ' href="' . $protocol . '://', $content );
		$content = preg_replace( '#<script[^>]*?>.*?</script>#si', '', $content );
		$content = str_replace( array( 'mailster-highlight', 'mailster-loading', 'ui-draggable', ' -handle' ), '', $content );

		$allowed_tags = array( 'address', 'a', 'big', 'blockquote', 'body', 'br', 'b', 'center', 'cite', 'code', 'dd', 'dfn', 'div', 'dl', 'dt', 'em', 'font', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html', 'img', 'i', 'kbd', 'li', 'meta', 'ol', 'pre', 'p', 'span', 'small', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'thead', 'tfoot', 'td', 'th', 'title', 'tr', 'tt', 'ul', 'u', 'map', 'area', 'video', 'audio', 'buttons', 'single', 'multi', 'modules', 'module', 'if', 'elseif', 'else', 'a', 'big', 'blockquote', 'body', 'br', 'b', 'center', 'cite', 'code', 'dd', 'dfn', 'div', 'dl', 'dt', 'em', 'font', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html', 'img', 'i', 'kbd', 'li', 'meta', 'ol', 'pre', 'p', 'span', 'small', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'thead', 'tfoot', 'td', 'th', 'title', 'tr', 'tt', 'ul', 'u', 'map', 'area', 'video', 'audio', 'buttons', 'single', 'multi', 'modules', 'module', 'if', 'elseif', 'else' );

		$allowed_tags = apply_filters( 'mymail_allowed_tags', apply_filters( 'mailster_allowed_tags', $allowed_tags ) );

		$allowed_tags = '<' . implode( '><', $allowed_tags ) . '>';

		// save comments with conditional stuff
		preg_match_all( '#<!--\s?\[\s?if(.*)?>(.*)?<!\[endif\]-->#sU', $content, $comments );

		$commentid = uniqid();
		foreach ( $comments[0] as $i => $comment ) {
			$content = str_replace( $comment, 'HTML_COMMENT_' . $i . '_' . $commentid, $content );
		}

		$content = strip_tags( $content, $allowed_tags );

		foreach ( $comments[0] as $i => $comment ) {
			$content = str_replace( 'HTML_COMMENT_' . $i . '_' . $commentid, $comment, $content );
		}

		$content = $pre_stuff . $content;

		return apply_filters( 'mymail_sanitize_content', apply_filters( 'mailster_sanitize_content', $content ) );
	}


	/**
	 *
	 *
	 * @param unknown $html
	 * @param unknown $linksonly (optional)
	 * @return unknown
	 */
	public function plain_text( $html, $linksonly = false ) {

		// allow to hook into this method
		$result = apply_filters( 'mymail_plain_text', apply_filters( 'mailster_plain_text', null, $html, $linksonly ), $html, $linksonly );
		if ( ! is_null( $result ) ) {
			return $result;
		}

		if ( $linksonly ) {
			$links = '/< *a[^>]*href *= *"([^#]*)"[^>]*>(.*)< *\/ *a *>/Uis';
			$text = preg_replace( $links, '${2} [${1}]', $html );
			$text = str_replace( array( ' ', '&nbsp;' ), ' ', strip_tags( $text ) );
			$text = @html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );

			return trim( $text );

		} else {
			require_once MAILSTER_DIR . 'classes/libs/class.html2text.php';
			$htmlconverter = new \Html2Text\Html2Text( $html, array( 'width' => 200 ) );

			return trim( $htmlconverter->get_text() );

		}

	}


	/**
	 *
	 *
	 * @param unknown $links
	 * @param unknown $file
	 * @return unknown
	 */
	public function add_action_link( $links, $file ) {

		if ( $file == MAILSTER_SLUG ) {
			array_unshift( $links, '<a href="edit.php?post_type=newsletter&page=mailster_addons">' . __( 'Add Ons', 'mailster' ) . '</a>' );
			array_unshift( $links, '<a href="edit.php?post_type=newsletter&page=mailster_settings">' . __( 'Settings', 'mailster' ) . '</a>' );
			array_unshift( $links, '<a href="admin.php?page=mailster_setup">' . __( 'Wizard', 'mailster' ) . '</a>' );
		}

		return $links;
	}


	/**
	 *
	 *
	 * @param unknown $links
	 * @param unknown $file
	 * @return unknown
	 */
	public function add_plugin_links( $links, $file ) {

		if ( $file == MAILSTER_SLUG ) {
			$links[] = '<a href="edit.php?post_type=newsletter&page=mailster_templates&more">' . __( 'Templates', 'mailster' ) . '</a>';
		}

		return $links;
	}


	/**
	 *
	 *
	 * @param unknown $install_actions
	 * @param unknown $api
	 * @param unknown $plugin_file
	 * @return unknown
	 */
	public function add_install_plugin_complete_actions( $install_actions, $api, $plugin_file ) {

		if ( ! isset( $_GET['mailster-addon'] ) ) {
			return $install_actions;
		}

		$install_actions['mailster_addons'] = '<a href="edit.php?post_type=newsletter&page=mailster_addons">' . __( 'Return to Add Ons Page', 'mailster' ) . '</a>';

		if ( isset( $install_actions['plugins_page'] ) ) {
			unset( $install_actions['plugins_page'] );
		}

		return $install_actions;
	}


	public function special_pages() {

		$page = add_submenu_page( null, 'Setup', 'Setup', 'read', 'mailster_setup', array( &$this, 'setup_page' ) );
		add_action( 'load-' . $page, array( &$this, 'setup_scripts_styles' ) );
		add_action( 'load-' . $page, array( &$this, 'remove_menu_enties' ) );

		$page = add_submenu_page( null, 'Welcome', 'Welcome', 'read', 'mailster_welcome', array( &$this, 'welcome_page' ) );
		add_action( 'load-' . $page, array( &$this, 'welcome_scripts_styles' ) );

		$page = add_submenu_page( 'edit.php?post_type=newsletter', __( 'Add Ons', 'mailster' ), __( 'Add Ons', 'mailster' ), 'mailster_manage_addons', 'mailster_addons', array( &$this, 'addon_page' ) );
		add_action( 'load-' . $page, array( &$this, 'addon_scripts_styles' ) );

	}


	public function remove_menu_enties() {

		global $submenu;

		if ( get_option( 'mailster_setup' ) ) {
			return;
		}

		$submenu['edit.php?post_type=newsletter'] = array();

		add_submenu_page( 'edit.php?post_type=newsletter', __( 'Mailster Setup', 'mailster' ), __( 'Mailster Setup', 'mailster' ), 'read', 'mailster_setup', array( &$this, 'setup_page' ) );

	}


	public function setup_page() {

		mailster_update_option( 'setup', false );
		remove_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		include MAILSTER_DIR . 'views/setup.php';

	}


	public function welcome_page() {

		mailster_update_option( 'welcome', false );
		remove_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		include MAILSTER_DIR . 'views/welcome.php';

	}


	public function addon_page() {

		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'thickbox' );

		include MAILSTER_DIR . 'views/addons.php';

	}


	/**
	 *
	 *
	 * @param unknown $hook
	 */
	public function admin_scripts_styles( $hook ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-icons', MAILSTER_URI . 'assets/css/icons' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_style( 'mailster-admin', MAILSTER_URI . 'assets/css/admin' . $suffix . '.css', array( 'mailster-icons' ), MAILSTER_VERSION );

	}


	/**
	 *
	 *
	 * @param unknown $hook
	 */
	public function setup_scripts_styles( $hook ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-setup', MAILSTER_URI . 'assets/css/setup-style' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-setup', MAILSTER_URI . 'assets/js/setup-script' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION );
		wp_localize_script( 'mailster-setup', 'mailsterL10n', array(
			'load_language' => __( 'Loading Languages', 'mailster' ),
			'enable_first' => __( 'Enable %s first', 'mailster' ),
			'use_deliverymethod' => __( 'Use %s as your delivery method', 'mailster' ),
			'check_language' => __( 'Check for languages', 'mailster' ),
			'install_addon' => __( 'Installing Add on', 'mailster' ),
			'activate_addon' => __( 'Activating Add on', 'mailster' ),
			'receiving_content' => __( 'Receiving Content', 'mailster' ),
			'skip_validation' => __( 'Without Registration you are not able to get automatic update or support!', 'mailster' ),
		) );

	}


	/**
	 *
	 *
	 * @param unknown $hook
	 */
	public function welcome_scripts_styles( $hook ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-welcome', MAILSTER_URI . 'assets/css/welcome-style' . $suffix . '.css', array(), MAILSTER_VERSION );

	}


	/**
	 *
	 *
	 * @param unknown $hook
	 */
	public function addon_scripts_styles( $hook ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-addons', MAILSTER_URI . 'assets/css/addons-style' . $suffix . '.css', array(), MAILSTER_VERSION );

	}


	public function activate() {

		global $wpdb;

		if ( is_network_admin() && is_multisite() ) {

			$old_blog = $wpdb->blogid;
			$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		} else {

			$blogids = array( false );

		}

		foreach ( $blogids as $blog_id ) {

			if ( $blog_id ) {
				switch_to_blog( $blog_id );
			}

			$isNew = get_option( 'mailster' ) == false;

			$this->on_activate( $isNew );

			foreach ( $this->_classes as $classname => $class ) {
				if ( method_exists( $class, 'on_activate' ) ) {
					$class->on_activate( $isNew );
				}
			}
		}

		if ( $blog_id ) {
			switch_to_blog( $old_blog );
			return;
		}

	}


	public function deactivate() {

		global $wpdb;

		if ( is_network_admin() && is_multisite() ) {

			$old_blog = $wpdb->blogid;
			$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		} else {

			$blogids = array( false );

		}

		foreach ( $blogids as $blog_id ) {

			if ( $blog_id ) {
				switch_to_blog( $blog_id );
			}

			foreach ( $this->_classes as $classname => $class ) {
				if ( method_exists( $class, 'on_deactivate' ) ) {
					$class->on_deactivate();
				}
			}

			$this->on_deactivate();

		}

		if ( $blog_id ) {
			switch_to_blog( $old_blog );
			return;
		}

	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_activate( $new ) {

		$this->check_compatibility( true, $new );

		$this->dbstructure();

		if ( $new ) {

			if ( is_plugin_active( 'myMail/myMail.php' ) ) {

				if ( deactivate_plugins( 'myMail/myMail.php', true, is_network_admin() ) ) {
					mailster_notice( '<strong>MyMail is now Mailster! The old version has been deactivated and can get removed!</strong>', 'error', false, 'warnings' );
					mailster_update_option( 'update_required', true );
				}
			} elseif ( get_option( 'mymail' ) ) {

				mailster_update_option( 'update_required', true );

			} else {
				update_option( 'mailster', time() );
				update_option( 'mailster_dbversion', MAILSTER_DBVERSION );
			}

			if ( ! is_network_admin() ) {
				add_action( 'activated_plugin', array( &$this, 'activation_redirect' ) );
			}
		}

	}


	public function on_deactivate() {

		flush_rewrite_rules();
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function check_compatibility( $notices = true, $die = false ) {

		$errors = (object) array(
			'error_count' => 0,
			'warning_count' => 0,
			'errors' => new WP_Error(),
			'warnings' => new WP_Error(),
		);

		$content_dir = dirname( MAILSTER_UPLOAD_DIR );

		if ( version_compare( PHP_VERSION, '5.3' ) < 0 ) {
			$errors->errors->add( 'minphpversion', sprintf( 'Mailster requires PHP version 5.3 or higher. Your current version is %s. Please update or ask your hosting provider to help you updating.', PHP_VERSION ) );
		}
		if ( version_compare( get_bloginfo( 'version' ), '3.8' ) < 0 ) {
			$errors->errors->add( 'minphpversion', sprintf( 'Mailster requires WordPress version 3.8 or higher. Your current version is %s.', get_bloginfo( 'version' ) ) );
		}
		if ( ! class_exists( 'DOMDocument' ) ) {
			$errors->errors->add( 'DOMDocument', 'Mailster requires the <a href="https://php.net/manual/en/class.domdocument.php" target="_blank">DOMDocument</a> library.' );
		}
		if ( ! function_exists( 'fsockopen' ) ) {
			$errors->warnings->add( 'fsockopen', 'Your server does not support <a href="https://php.net/manual/en/function.fsockopen.php" target="_blank">fsockopen</a>.' );
		}
		if ( ! is_dir( $content_dir ) || ! wp_is_writable( $content_dir ) ) {
			$errors->warnings->add( 'writeable', sprintf( 'Your content folder in %s is not writeable.', '"' . $content_dir . '"' ) );
		}
		if ( max( intval( @ini_get( 'memory_limit' ) ), intval( WP_MAX_MEMORY_LIMIT ) ) < 128 ) {
			$errors->warnings->add( 'menorylimit', 'Your Memory Limit is ' . size_format( WP_MEMORY_LIMIT * 1048576 ) . ', Mailster recommends at least 128 MB' );
		}

		$errors->error_count = count( $errors->errors->errors );
		$errors->warning_count = count( $errors->warnings->errors );

		if ( $notices ) {

			if ( $errors->error_count ) {

				$html = '<strong>' . implode( '<br>', $errors->errors->get_error_messages() ) . '</strong>';

				if ( $die ) {
					die( '<div style="font-family:sans-serif;">' . $html . '</div>' );
				} else {
					mailster_notice( $html, 'error', false, 'errors' );
				}
			} else {
				mailster_remove_notice( 'errors' );
			}

			if ( $errors->warning_count ) {

				$html = '<strong>' . implode( '<br>', $errors->warnings->get_error_messages() ) . '</strong>';
				mailster_notice( $html, 'error', false, 'warnings' );

			} else {
				mailster_remove_notice( 'warnings' );
			}
		}

		return $errors;

	}


	/**
	 *
	 *
	 * @param unknown $code
	 * @param unknown $short    (optional)
	 * @param unknown $fallback (optional)
	 * @return unknown
	 */
	public function get_update_error( $code, $short = false, $fallback = null ) {

		if ( is_wp_error( $code ) ) {
			$fallback = $code->get_error_message();
			$code = $code->get_error_code();
		}

		switch ( $code ) {

			case 678: // No Licensecode provided
				$error_msg = $short ? __( 'Register via the %s.', 'mailster' ) : __( 'To get automatic updates for Mailster you need to register on the %s.', 'mailster' );
				$error_msg = sprintf( $error_msg, '<a href="' . admin_url( 'admin.php?page=mailster_dashboard' ) . '" target="_top">' . __( 'Dashboard', 'mailster' ) . '</a>' );
			break;

			case 679: // Licensecode invalid
				$error_msg = __( 'Your purchase code is invalid.', 'mailster' );
				if ( ! $short ) {
					$error_msg .= ' ' . __( 'To get automatic updates for Mailster you need provide a valid purchase code.', 'mailster' );
				}

			break;

			case 680: // Licensecode in use
				$error_msg = $short ? __( 'Code in use!', 'mailster' ) : __( 'Your purchase code is already in use and can only be used for one site.', 'mailster' );
			break;

			case 500: // Internal Server Error
			case 503: // Service Unavailable
			case 'http_err':
				$error_msg = __( 'Envato servers are currently down. Please try again later!', 'mailster' );
			break;

			case 406: // already assigned
				$error_msg = __( 'This purchase code is already assigned to another user!', 'mailster' );
			break;

			default:
				$error_msg = $fallback ? $fallback : __( 'There was an error while processing your request!', 'mailster' ) . ' [' . $code . ']';
			break;
		}

		return $error_msg;

	}


	/**
	 *
	 *
	 * @param unknown $fullnames (optional)
	 * @return unknown
	 */
	public function get_tables( $fullnames = false ) {

		global $wpdb;

		if ( ! $fullnames ) {
			return $this->tables;
		}

		$tables = array();
		foreach ( $this->tables as $table ) {
			$tables[] = "{$wpdb->prefix}mailster_$table";
		}

		return $tables;

	}


	/**
	 *
	 *
	 * @param unknown $set_charset (optional)
	 * @return unknown
	 */
	public function get_table_structure( $set_charset = true ) {

		global $wpdb;

		$collate = '';

		if ( $set_charset && $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		return array(

			"CREATE TABLE {$wpdb->prefix}mailster_subscribers (
                ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                hash varchar(32) NOT NULL,
                email varchar(191) NOT NULL,
                wp_id bigint(20) unsigned NOT NULL DEFAULT 0,
                status int(11) unsigned NOT NULL DEFAULT 0,
                added int(11) unsigned NOT NULL DEFAULT 0,
                updated int(11) unsigned NOT NULL DEFAULT 0,
                signup int(11) unsigned NOT NULL DEFAULT 0,
                confirm int(11) unsigned NOT NULL DEFAULT 0,
                ip_signup varchar(45) NOT NULL DEFAULT '',
                ip_confirm varchar(45) NOT NULL DEFAULT '',
                rating decimal(3,2) unsigned NOT NULL DEFAULT 0.25,
                PRIMARY KEY  (ID),
                UNIQUE KEY email (email),
                UNIQUE KEY hash (hash),
                KEY wp_id (wp_id),
                KEY status (status),
                KEY rating (rating)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_subscriber_fields (
                subscriber_id bigint(20) unsigned NOT NULL,
                meta_key varchar(191) NOT NULL,
                meta_value longtext NOT NULL,
                UNIQUE KEY id (subscriber_id,meta_key),
                KEY subscriber_id (subscriber_id),
                KEY meta_key (meta_key)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_subscriber_meta (
                subscriber_id bigint(20) unsigned NOT NULL,
                campaign_id bigint(20) unsigned NOT NULL,
                meta_key varchar(191) NOT NULL,
                meta_value longtext NOT NULL,
                UNIQUE KEY id (subscriber_id,campaign_id,meta_key),
                KEY subscriber_id (subscriber_id),
                KEY campaign_id (campaign_id),
                KEY meta_key (meta_key)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_queue (
                subscriber_id bigint(20) unsigned NOT NULL DEFAULT 0,
                campaign_id bigint(20) unsigned NOT NULL DEFAULT 0,
                requeued tinyint(1) unsigned NOT NULL DEFAULT 0,
                added int(11) unsigned NOT NULL DEFAULT 0,
                timestamp int(11) unsigned NOT NULL DEFAULT 0,
                sent int(11) unsigned NOT NULL DEFAULT 0,
                priority tinyint(1) unsigned NOT NULL DEFAULT 0,
                count tinyint(1) unsigned NOT NULL DEFAULT 0,
                error tinyint(1) unsigned NOT NULL DEFAULT 0,
                ignore_status tinyint(1) unsigned NOT NULL DEFAULT 0,
                options varchar(191) NOT NULL DEFAULT '',
                UNIQUE KEY id (subscriber_id,campaign_id,requeued,options),
                KEY subscriber_id (subscriber_id),
                KEY campaign_id (campaign_id),
                KEY requeued (requeued),
                KEY timestamp (timestamp),
                KEY priority (priority),
                KEY count (count),
                KEY error (error),
                KEY ignore_status (ignore_status)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_actions (
                subscriber_id bigint(20) unsigned NOT NULL DEFAULT 0,
                campaign_id bigint(20) unsigned NOT NULL DEFAULT 0,
                timestamp int(11) unsigned NOT NULL DEFAULT 0,
                count int(11) unsigned NOT NULL DEFAULT 0,
                type tinyint(1) NOT NULL DEFAULT 0,
                link_id bigint(20) unsigned NOT NULL DEFAULT 0,
                UNIQUE KEY id (subscriber_id,campaign_id,type,link_id),
                KEY subscriber_id (subscriber_id),
                KEY campaign_id (campaign_id),
                KEY type (type)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_links (
                ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                link varchar(2083) NOT NULL,
                i tinyint(1) unsigned NOT NULL,
                PRIMARY KEY  (ID)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_lists (
                ID bigint(20) NOT NULL AUTO_INCREMENT,
                parent_id bigint(20) unsigned NOT NULL,
                name varchar(191) NOT NULL,
                slug varchar(191) NOT NULL,
                description longtext NOT NULL,
                added int(11) unsigned NOT NULL,
                updated int(11) unsigned NOT NULL,
                PRIMARY KEY  (ID),
                UNIQUE KEY name (name),
                UNIQUE KEY slug (slug)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_lists_subscribers (
                list_id bigint(20) unsigned NOT NULL,
                subscriber_id bigint(20) unsigned NOT NULL,
                added int(11) unsigned NOT NULL,
                UNIQUE KEY id (list_id,subscriber_id),
                KEY list_id (list_id),
                KEY subscriber_id (subscriber_id)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_forms (
                ID bigint(20) NOT NULL AUTO_INCREMENT,
                name varchar(191) NOT NULL DEFAULT '',
                submit varchar(191) NOT NULL DEFAULT '',
                asterisk tinyint(1) DEFAULT 1,
                userschoice tinyint(1) DEFAULT 0,
                precheck tinyint(1) DEFAULT 0,
                dropdown tinyint(1) DEFAULT 0,
                prefill tinyint(1) DEFAULT 0,
                inline tinyint(1) DEFAULT 0,
                overwrite tinyint(1) DEFAULT 0,
                addlists tinyint(1) DEFAULT 0,
                style longtext,
                custom_style longtext,
                doubleoptin tinyint(1) DEFAULT 1,
                subject longtext,
                headline longtext,
                content longtext,
                link longtext,
                resend tinyint(1) DEFAULT 0,
                resend_count int(11) DEFAULT 2,
                resend_time int(11) DEFAULT 48,
                template varchar(191) NOT NULL DEFAULT '',
                vcard tinyint(1) DEFAULT 0,
                vcard_content longtext,
                confirmredirect varchar(2083) DEFAULT NULL,
                redirect varchar(2083) DEFAULT NULL,
                added int(11) unsigned DEFAULT NULL,
                updated int(11) unsigned DEFAULT NULL,
                PRIMARY KEY  (ID)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_form_fields (
                form_id bigint(20) unsigned NOT NULL,
                field_id varchar(191) NOT NULL,
                name varchar(191) NOT NULL,
                error_msg varchar(191) NOT NULL,
                required tinyint(1) unsigned NOT NULL,
                position int(11) unsigned NOT NULL,
                UNIQUE KEY id (form_id,field_id)
            ) $collate;",

			"CREATE TABLE {$wpdb->prefix}mailster_forms_lists (
                form_id bigint(20) unsigned NOT NULL,
                list_id bigint(20) unsigned NOT NULL,
                added int(11) unsigned NOT NULL,
                UNIQUE KEY id (form_id,list_id),
                KEY form_id (form_id),
                KEY list_id (list_id)
            ) $collate;",

		);
	}


	/**
	 *
	 *
	 * @param unknown $output      (optional)
	 * @param unknown $execute     (optional)
	 * @param unknown $set_charset (optional)
	 * @param unknown $hide_errors (optional)
	 * @return unknown
	 */
	public function dbstructure( $output = false, $execute = true, $set_charset = true, $hide_errors = true ) {

		global $wpdb;

		$tables = $this->get_table_structure( $set_charset );

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$results = array();

		if ( $hide_errors ) {
			$wpdb->hide_errors();
		}

		foreach ( $tables as $tablequery ) {
			$results[] = dbDelta( $tablequery, $execute );
		}

		if ( $output ) {
			foreach ( $results as $result ) {
				if ( $result ) {
					echo implode( "\n", $result ) . "\n";
				}
			}
		}

		return true;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function optimize_tables() {

		global $wpdb;

		return false !== $wpdb->query( "OPTIMIZE TABLE {$wpdb->prefix}mailster_" . implode( ", {$wpdb->prefix}mailster_", $this->get_tables() ) );
	}


	/**
	 *
	 *
	 * @param unknown $plugin
	 */
	public function activation_redirect( $plugin ) {

		// only on single plugin activation
		if ( $plugin != MAILSTER_SLUG || ! isset( $_GET['plugin'] ) ) {
			return;
		}

		// $this->send_welcome_mail();
		wp_redirect( admin_url( 'edit.php?post_type=newsletter&page=mailster_setup' ), 302 );

		exit;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function send_welcome_mail() {

		$current_user = wp_get_current_user();

		$n = mailster( 'notification' );
		$n->to( $current_user->user_email );
		$n->subject( __( 'Your Mailster Newsletter Plugin is ready!', 'mailster' ) );
		$n->replace( array(
				'headline' => '',
				'baseurl' => admin_url(),
				'notification' => 'This welcome mail was sent from your website <a href="' . home_url() . '">' . get_bloginfo( 'name' ) . '</a>. This also makes sure you can send emails with your current settings',
				'name' => $current_user->display_name,
				'preheader' => 'Thank you, ' . $current_user->display_name . '! ',
		) );
		$n->requeue( false );
		$n->template( 'welcome_mail' );

		return $n->add();

	}


	/**
	 *
	 *
	 * @param unknown $keysonly (optional)
	 * @return unknown
	 */
	public function get_custom_fields( $keysonly = false ) {

		$fields = mailster_option( 'custom_field', array() );
		$fields = $keysonly ? array_keys( $fields ) : $fields;

		return $fields;

	}


	/**
	 *
	 *
	 * @param unknown $keysonly (optional)
	 * @return unknown
	 */
	public function get_custom_date_fields( $keysonly = false ) {

		$fields = array();

		$all_fields = $this->get_custom_fields( false );
		foreach ( $all_fields as $key => $data ) {
			if ( $data['type'] == 'date' ) {
				$fields[ $key ] = $data;
			}
		}
		return $keysonly ? array_keys( $fields ) : $fields;

	}


	public function check_homepage() {

		// no check if setup hasn't finished
		if ( ! get_option( 'mailster_setup' ) ) {
			return;
		};

		$hp = get_post( mailster_option( 'homepage' ) );

		mailster_remove_notice( 'no_homepage' );
		mailster_remove_notice( 'wrong_homepage_status' );

		if ( ! $hp || $hp->post_status == 'trash' ) {

			mailster_notice( sprintf( '<strong>' . __( 'You haven\'t defined a homepage for the newsletter. This is required to make the subscription form work correctly. Please check the %1$s or %2$s.', 'mailster' ), '<a href="edit.php?post_type=newsletter&page=mailster_settings&mailster_remove_notice=no_homepage#frontend">' . __( 'frontend settings page', 'mailster' ) . '</a>', '<a href="' . add_query_arg( 'mailster_create_homepage', 1, admin_url() ) . '">' . __( 'create it right now', 'mailster' ) . '</a>' ) . '</strong>', 'error', false, 'no_homepage' );

		} elseif ( $hp->post_status != 'publish' ) {

			mailster_notice( sprintf( '<strong>' . __( 'Your newsletter homepage is not visible. Please %s the page', 'mailster' ), '<a href="post.php?post=' . $hp->ID . '&action=edit&mailster_remove_notice=mailster_wrong_homepage_status">' . __( 'update', 'mailster' ) . '</a>' ) . '</strong>', 'error', false, 'wrong_homepage_status', $hp->post_author );

		}

	}


	/**
	 *
	 *
	 * @param unknown $post_type
	 * @param unknown $post
	 */
	public function add_homepage_info( $post_type, $post ) {

		if ( $post_type != 'page' ) {
			return;
		}

		if ( mailster_option( 'homepage' ) == $post->ID ) {

			if ( ! preg_match( '#\[newsletter_signup\]#', $post->post_content )
				|| ! preg_match( '#\[newsletter_signup_form#', $post->post_content )
				|| ! preg_match( '#\[newsletter_confirm\]#', $post->post_content )
				|| ! preg_match( '#\[newsletter_unsubscribe\]#', $post->post_content ) ) {

				mailster_notice( '<strong>' . sprintf( __( 'This is your newsletter homepage but it seems it is not set up correctly! Please follow %s for help!', 'mailster' ), '<a href="https://kb.mailster.co/how-can-i-setup-the-newsletter-homepage/">' . __( 'this guid', 'mailster' ) . '</a>' ) . '</strong>', 'error', true, 'homepage_info' );

			} else {

			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $system_mail (optional)
	 */
	public function wp_mail_setup( $system_mail = null ) {

		if ( is_null( $system_mail ) ) {
			$system_mail = mailster_option( 'system_mail' );
		}

		if ( $system_mail ) {

			if ( $system_mail == 'template' ) {

				add_filter( 'wp_mail', array( &$this, 'wp_mail_set' ) );
				add_filter( 'wp_mail_content_type', array( &$this, 'wp_mail_content_type' ) );

			} else {

				if ( $this->wp_mail ) {
					add_action( 'admin_notices', array( &$this, 'wp_mail_notice' ) );
				}
			}
		}
	}


	/**
	 *
	 *
	 * @param unknown $content_type
	 * @return unknown
	 */
	public function wp_mail_content_type( $content_type ) {
		return 'text/html';
	}


	/**
	 *
	 *
	 * @param unknown $args
	 * @return unknown
	 */
	public function wp_mail_set( $args ) {

		$current_filter = current_filter();
		$methods = wp_list_pluck( debug_backtrace(), 'function' );
		$caller = null;
		foreach ( $methods as $method ) {
			if ( ! in_array( $method, array( 'wp_mail_set', 'wp_mail', 'include', 'include_once', 'require', 'require_once', 'apply_filters', 'do_action' ) ) ) {
				$caller = $method;
				break;
			}
		}

		$template = mailster_option( 'default_template' );
		$template = apply_filters( 'mailster_wp_mail_template', $template, $caller, $current_filter );

		$file = mailster_option( 'system_mail_template', 'notification.html' );
		$file = apply_filters( 'mymail_wp_mail_template_file', apply_filters( 'mailster_wp_mail_template_file', $file, $caller, $current_filter ), $caller, $current_filter );

		if ( $template ) {
			$template = mailster( 'template', $template, $file );
			$content = $template->get( true, true );
		} else {
			$content = $headline . '<br>' . $content;
		}

		$replace = apply_filters( 'mymail_send_replace', apply_filters( 'mailster_send_replace', array( 'notification' => '' ), $caller, $current_filter ) );
		$message = apply_filters( 'mymail_send_message', apply_filters( 'mailster_send_message', $args['message'], $caller, $current_filter ) );
		$subject = apply_filters( 'mymail_send_subject', apply_filters( 'mailster_send_subject', $args['subject'], $caller, $current_filter ) );
		$headline = apply_filters( 'mymail_send_headline', apply_filters( 'mailster_send_headline', $args['subject'], $caller, $current_filter ) );

		if ( apply_filters( 'mymail_wp_mail_htmlify', apply_filters( 'mailster_wp_mail_htmlify', true ) ) ) {
			$message = $this->wp_mail_map_links( $message );
			$message = str_replace( array( '<br>', '<br />', '<br/>' ), "\n", $message );
			$message = preg_replace( '/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n", $message );
			$message = wpautop( $message, true );
		}

		$placeholder = mailster( 'placeholder', $content );

		$placeholder->add( array(
			'subject' => $subject,
			'preheader' => $headline,
			'headline' => $headline,
			'content' => $message,
		) );

		$placeholder->add( $replace );

		$message = $placeholder->get_content();

		$message = mailster( 'helper' )->add_mailster_styles( $message );
		$message = mailster( 'helper' )->inline_style( $message );

		$args['message'] = $message;

		$placeholder->set_content( $subject );

		$args['subject'] = $placeholder->get_content();

		return $args;
	}


	/**
	 *
	 *
	 * @param unknown $message
	 * @return unknown
	 */
	public function wp_mail_map_links( $message ) {

		// map links with anchor tags
		if ( preg_match_all( '/(<)(https?:\/\/\S*)(>)/', $message, $links ) ) {
			foreach ( $links[0] as $i => $link ) {
				$message = preg_replace( '/' . preg_quote( $links[0][ $i ], '/' ) . '/', '<a href="' . $links[2][ $i ] . '">' . $links[2][ $i ] . '</a>', $message, 1 );
			}
		}
		if ( preg_match_all( '/(\s)(https?:\/\/\S*)(\s)?/', $message, $links ) ) {
			foreach ( $links[2] as $i => $link ) {
				$message = preg_replace( '/' . preg_quote( $links[1][ $i ] . $links[2][ $i ], '/' ) . '/', $links[1][ $i ] . '<a href="' . $links[2][ $i ] . '">' . $links[2][ $i ] . '</a>' . $links[3][ $i ], $message, 1 );
			}
		}

		return $message;
	}


	public function wp_mail_notice() {
		echo '<div class="error"><p>function <strong>wp_mail</strong> already exists from a different plugin! Please disable it before using Mailsters wp_mail alternative!</p></div>';
	}


	/**
	 *
	 *
	 * @param unknown $to
	 * @param unknown $subject
	 * @param unknown $message
	 * @param unknown $headers     (optional)
	 * @param unknown $attachments (optional)
	 * @param unknown $file        (optional)
	 * @return unknown
	 */
	public function wp_mail( $to, $subject, $message, $headers = '', $attachments = array(), $file = null ) {

		if ( is_array( $headers ) ) {
			$headers = implode( "\r\n", $headers ) . "\r\n";
		}

		$message = $this->wp_mail_map_links( $message );
		// only if content type is not html
		if ( ! preg_match( '#content-type:(.*)text/html#i', $headers ) ) {
			$message = str_replace( array( '<br>', '<br />', '<br/>' ), "\n", $message );
			$message = preg_replace( '/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n", $message );
			$message = wpautop( $message, true );
		}

		$current_filter = current_filter();
		$methods = wp_list_pluck( debug_backtrace(), 'function' );
		$caller = null;
		foreach ( $methods as $method ) {
			if ( ! in_array( $method, array( 'wp_mail_set', 'wp_mail', 'include', 'include_once', 'require', 'require_once', 'apply_filters', 'do_action' ) ) ) {
				$caller = $method;
				break;
			}
		}

		$template = mailster_option( 'default_template' );
		$template = apply_filters( 'mailster_wp_mail_template', $template, $caller, $current_filter );

		$file = ! is_null( $file ) ? $file : mailster_option( 'system_mail_template', 'notification.html' );
		$file = apply_filters( 'mymail_wp_mail_template_file', apply_filters( 'mailster_wp_mail_template_file', $file, $caller, $current_filter ), $caller, $current_filter );

		$mail = mailster( 'mail' );
		$mail->from = apply_filters( 'wp_mail_from', mailster_option( 'from' ) );
		$mail->from_name = apply_filters( 'wp_mail_from_name', mailster_option( 'from_name' ) );

		$mail->apply_raw_headers( $headers );

		$mail->to = $to;
		$mail->message = $message;
		$mail->subject = $subject;

		if ( ! is_array( $attachments ) ) {
			$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
		}

		$mail->attachments = $attachments;

		$replace = apply_filters( 'mymail_send_replace', apply_filters( 'mailster_send_replace', array( 'notification' => '' ) ) );
		$message = apply_filters( 'mymail_send_message', apply_filters( 'mailster_send_message', $message ) );
		$headline = apply_filters( 'mymail_send_headline', apply_filters( 'mailster_send_headline', $subject ) );

		return $mail->send_notification( $message, $headline, $replace, false, $file );

	}


	/**
	 *
	 *
	 * @param unknown $field (optional)
	 * @return unknown
	 */
	public function plugin_info( $field = null ) {

		$plugins = get_site_transient( 'update_plugins' );

		if ( isset( $plugins->response[ MAILSTER_SLUG ] ) ) {
			$plugin_info = $plugins->response[ MAILSTER_SLUG ];
		} elseif ( isset( $plugins->no_update[ MAILSTER_SLUG ] ) ) {
			$plugin_info = $plugins->no_update[ MAILSTER_SLUG ];
		} else {
			return null;
		}

		if ( is_null( $field ) ) {
			return $plugin_info;
		}
		if ( isset( $plugin_info->{$field} ) ) {
			return $plugin_info->{$field};
		}

		return null;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function has_update() {

		$new_version = $this->plugin_info( 'new_version' );

		return version_compare( $new_version, MAILSTER_VERSION, '>' );
	}


	/**
	 *
	 *
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function is_verified( $force = false ) {

		$license = get_option( 'mailster_license' );
		if ( ! $license ) {
			return false;
		}

		$old = get_option( '_transient_mailster_verified', 'no' );

		if ( ! ( $verified = get_transient( 'mailster_verified' ) ) || $force ) {

			$verified = 'no';
			$recheck = DAY_IN_SECONDS;

			$result = UpdateCenterPlugin::verify( MAILSTER_SLUG );
			if ( ! is_wp_error( $result ) ) {
				$verified = 'yes';
			} else {
				switch ( $result->get_error_code() ) {
					case 500: // Internal Server Error
					case 503: // Service Unavailable
					case 'http_err':
						$recheck = 900;
						$verified = $old;
						break;
				}
			}

			set_transient( 'mailster_verified', $verified, $recheck );

		}

		return 'yes' == $verified;

	}


	/**
	 *
	 *
	 * @param unknown $post_id
	 * @param unknown $part     (optional)
	 * @param unknown $meta_key
	 * @return unknown
	 */
	public function meta( $post_id, $part = null, $meta_key ) {

		$meta = get_post_meta( $post_id, $meta_key, true );

		if ( is_null( $part ) ) {
			return $meta;
		}

		if ( isset( $meta[ $part ] ) ) {
			return $meta[ $part ];
		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $key
	 * @param unknown $value    (optional)
	 * @param unknown $meta_key
	 * @return unknown
	 */
	public function update_meta( $id, $key, $value = null, $meta_key ) {
		if ( is_array( $key ) ) {
			$meta = $key;
			return update_post_meta( $id, $meta_key, $meta );
		}
		$meta = $this->meta( $id, null, $meta_key );
		$old = isset( $meta[ $key ] ) ? $meta[ $key ] : '';
		$meta[ $key ] = $value;
		return update_post_meta( $id, $meta_key, $meta, $old );
	}


	/**
	 *
	 *
	 * @param unknown $post_states
	 * @param unknown $post
	 * @return unknown
	 */
	public function display_post_states( $post_states, $post ) {

		if ( $post->ID == mailster_option( 'homepage' ) ) {
			$post_states['mailster_is_homepage'] = __( 'Newsletter Homepage', 'mailster' );
		}

		return $post_states;

	}


	/**
	 *
	 *
	 * @param unknown $postdata
	 * @param unknown $post
	 * @return unknown
	 */
	public function import_post_data( $postdata, $post ) {

		if ( ! isset( $postdata['post_type'] ) || $postdata['post_type'] != 'newsletter' ) {
			return $postdata;
		}

		kses_remove_filters();

		preg_match_all( '/(src|background|href)=["\'](.*)["\']/Ui', $postdata['post_content'], $links );
		$links = $links[2];

		$old_home_url = '';
		foreach ( $links as $link ) {
			if ( preg_match( '/(.*)wp-content(.*)\/mailster/U', $link, $match ) ) {
				$new_link = str_replace( $match[0], MAILSTER_UPLOAD_URI, $link );
				$old_home_url = $match[1];
				$postdata['post_content'] = str_replace( $link, $new_link, $postdata['post_content'] );
			}
		}

		if ( $old_home_url ) {
			$postdata['post_content'] = str_replace( $old_home_url, trailingslashit( home_url() ), $postdata['post_content'] );
		}

		mailster_notice( '<strong>' . __( 'Please make sure all your campaigns are imported correctly!', 'mailster' ) . '</strong>', 'error', false, 'import_campaings' );

		return $postdata;

	}


	private function thirdpartystuff() {

		do_action( 'mailster_thirdpartystuff' );
		do_action( 'mymail_thirdpartystuff' );

		if ( function_exists( 'w3tc_objectcache_flush' ) ) {
			add_action( 'shutdown', 'w3tc_objectcache_flush' );
		}

		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			add_action( 'shutdown', 'wp_cache_clear_cache' );
		}

	}


}
