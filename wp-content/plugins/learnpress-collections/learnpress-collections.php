<?php
/*
Plugin Name: LearnPress - Collections
Plugin URI: http://thimpress.com/learnpress
Description: Collecting related courses into one collection by administrator
Author: ThimPress
Version: 2.1.2
Author URI: http://thimpress.com
Tags: learnpress
Text Domain: learnpress
Domain Path: /languages/
*/

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 *  Define constants
 */
define( 'LP_COLLECTIONS_PATH', dirname( __FILE__ ) );
define( 'LP_COLLECTIONS_VER', '2.0' );
define( 'LP_COLLECTIONS_REQUIRE_VER', '2.0' );

if ( !class_exists( 'LP_Addon_Collections' ) ) {
	/**
	 * Class LP_Addon_Collections
	 */
	class LP_Addon_Collections {
		/**
		 * @var null
		 */
		protected static $_instance = null;

		/**
		 * @var null
		 */
		public static $query = null;

		/**
		 * @var bool
		 */
		public static $in_loop = false;

		/**
		 * @var bool
		 */
		private $_course_collections = false;

		/**
		 * LP_Addon_Collections constructor.
		 */
		function __construct() {
			add_action( 'init', array( $this, 'register_collections' ) );
			add_filter( 'learn_press_admin_tabs_info', array( $this, 'admin_tabs_info' ) );
			add_filter( 'learn_press_admin_tabs_on_pages', array( $this, 'admin_tabs_on_pages' ) );
			add_action( 'load-post.php', array( $this, 'collection_settings_metabox' ), 20 );
			add_action( 'load-post-new.php', array( $this, 'collection_settings_metabox' ), 20 );

			add_action( 'load-post.php', array( $this, 'course_collections_meta_box' ), 20 );
			add_action( 'load-post-new.php', array( $this, 'course_collections_meta_box' ), 20 );

			add_filter( 'post_class', array( $this, 'collection_class' ) );
			add_filter( 'is_learnpress', array( $this, 'is_learnpress' ) );
			add_action( 'save_post_lp_collection', array( $this, 'update_course_collections' ), 10 );
			add_action( 'save_post_lp_course', array( $this, 'update_collection_courses' ), 10 );
			add_action( 'template_include', array( $this, 'template_controller' ), 10 );
			add_shortcode( 'learn_press_collection', array( $this, 'shortcode' ) );
			add_action( 'widgets_init', array( $this, 'register_widget' ) );
			add_filter( 'manage_lp_collection_posts_columns', array( $this, 'set_custom_edit_lp_collection_columns' ) );
			add_action( 'manage_lp_collection_posts_custom_column', array( $this, 'custom_lp_collection_column' ), 10, 2 );
			add_filter( 'learn_press_lp_course_tabs', array( $this, 'admin_course_tabs' ) );
			if ( function_exists( 'learn_press_load_plugin_text_domain' ) ) {
				learn_press_load_plugin_text_domain( LP_COLLECTIONS_PATH );
			}
			require_once LP_COLLECTIONS_PATH . '/inc/functions.php';
		}

		/**
		 * Register new tab in course page
		 *
		 * @param $tabs
		 *
		 * @return mixed
		 */
		public function admin_course_tabs( $tabs ) {
			$tabs['course_collection'] = $this->_course_collections;
			return $tabs;
		}

		/**
		 * Create meta box for admin course tab
		 */
		public function course_collections_meta_box() {
			$prefix                    = '_lp_';
			$args                      = array(
				'id'         => 'course_collection',
				'title'      => 'Collections',
				'post_types' => array( 'lp_course' ),
				'fields'     => array(
					array(
						'name'        => __( 'Collections', 'learnpress_collections' ),
						'id'          => "{$prefix}course_collections",
						'type'        => 'post',
						'post_type'   => 'lp_collection',
						'field_type'  => 'select_advanced',
						'multiple'    => true,
						'desc'        => __( 'Select collections that contains this course.', 'learnpress' ),
						'placeholder' => __( 'Select collections', 'learnpress_collections' ),
						'query_args'  => array(
							'author' => ''
						)
					),
				)
			);
			$this->_course_collections = new RW_Meta_Box( apply_filters( 'learn_press_course_collection_meta_box', $args ) );
		}

		/**
		 * @param $template
		 *
		 * @return string
		 */
		public function template_controller( $template ) {
			$file = '';
			if ( is_singular( array( 'lp_collection' ) ) ) {
				global $post;
				if ( !preg_match( '/\[learn_press_collection\s?(.*)\]/', $post->post_content ) ) {
					$post->post_content = '[learn_press_collection id="' . get_the_ID() . '" limit="2"]';
				}

				$file   = 'single-collection.php';
				$find[] = learn_press_template_path() . "/addons/collections/{$file}";
			} elseif ( is_post_type_archive( 'lp_collection' ) ) {
				$file   = 'archive-collection.php';
				$find[] = learn_press_template_path() . "/addons/collections/{$file}";
			}
			if ( $file ) {
				$template = locate_template( array_unique( $find ) );
				if ( !$template ) {
					$template = LP_COLLECTIONS_PATH . '/templates/' . $file;
				}
			}
			return $template;
		}

		/**
		 * Register new Collections widget
		 */
		public function register_widget() {
			include_once LP_COLLECTIONS_PATH . '/inc/widget.php';

			register_widget( 'LP_Collections_Widget' );
		}

		public function is_learnpress( $is ) {
			return $is || is_post_type_archive( 'lp_collection' ) || is_singular( array( 'lp_collection' ) );
		}

		public function collection_class( $classes ) {
			if ( is_singular( array( 'lp_collection' ) ) ) {
				$classes = (array) $classes;
				if ( false !== ( $key = array_search( 'hentry', $classes ) ) ) {
					unset( $classes[$key] );
				}
			}
			return $classes;
		}

		/**
		 * @param array $atts
		 *
		 * @return string
		 */
		public function shortcode( $atts = null ) {
			$atts = shortcode_atts(
				array(
					'id'    => 0,
					'limit' => 10
				),
				$atts
			);
			ob_start();
			$id      = $atts['id'];
			$content = '';
			if ( $id ) {
				$courses = get_post_meta( $id, '_lp_collection_courses' );
				if ( !$courses ) {
					$courses = array( 0 );
				}
				$limit    = absint( get_post_meta( $id, '_lp_collection_courses_per_page', true ) );
				$limit    = $limit ? $limit : $atts['limit'];
				$args     = array(
					'post_type'           => 'lp_course',
					'post_status'         => 'publish',
					'post__in'            => $courses,
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => $limit,
					'offset'              => ( max( get_query_var( 'collection_page' ) - 1, 0 ) ) * $limit
				);
				$query    = new WP_Query( $args );
				$template = learn_press_collections_locate_template( 'archive-collection-course.php' );
				include $template;

				$content = ob_get_clean();
				wp_reset_postdata();
			}
			return $content;
		}

		public static function locate_template( $name ) {
			return learn_press_collections_locate_template( $name );
		}

		public static function get_template( $name, $args = null ) {
			learn_press_collections_get_template( $name, $args );
		}

		public function register_collections() {
			$labels = array(
				'name'               => _x( 'Collections', 'Post Type General Name', 'learnpress' ),
				'singular_name'      => _x( 'Collection', 'Post Type Singular Name', 'learnpress' ),
				'menu_name'          => __( 'Collections', 'learnpress' ),
				'parent_item_colon'  => __( 'Parent Item:', 'learnpress' ),
				'all_items'          => __( 'Collections', 'learnpress' ),
				'view_item'          => __( 'View Collection', 'learnpress' ),
				'add_new_item'       => __( 'Add New Collection', 'learnpress' ),
				'add_new'            => __( 'Add New', 'learnpress' ),
				'edit_item'          => __( 'Edit Collection', 'learnpress' ),
				'update_item'        => __( 'Update Collection', 'learnpress' ),
				'search_items'       => __( 'Search Collection', 'learnpress' ),
				'not_found'          => __( 'No collection found', 'learnpress' ),
				'not_found_in_trash' => __( 'No collection found in Trash', 'learnpress' ),
			);

			$args = array(
				'labels'             => $labels,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'has_archive'        => true,
				'capability_type'    => 'lp_order',
				'map_meta_cap'       => true,
				'show_in_menu'       => 'learn_press',
				'show_in_admin_bar'  => true,
				'show_in_nav_menus'  => true,
				'taxonomies'         => array(),
				'supports'           => array( 'title', 'editor', 'thumbnail', 'revisions', 'comments', 'excerpt' ),
				'hierarchical'       => true,
				'rewrite'            => array( 'slug' => 'collections', 'hierarchical' => true, 'with_front' => false )
			);
			register_post_type( 'lp_collection', $args );
			///flush_rewrite_rules();
			add_rewrite_tag( '%collection_page%', '([^&]+)' );
			add_rewrite_rule( '^collections/([^/]*)/page/(.*)', 'index.php?lp_collection=$matches[1]&collection_page=$matches[2]', 'top' );
		}

		/*
		* Add tab Collection into collection admin tabs
		*/
		public function admin_tabs_info( $arr ) {
			$arr[11] = array(
				"link" => "edit.php?post_type=lp_collection",
				"name" => __( "Collections", "learnpress-collections" ),
				"id"   => "edit-lp_collection",
			);
			return $arr;
		}

		/*
		* Add admin tabs into page 'edit-lp_collection' and 'lp_collection'
		*/
		public function admin_tabs_on_pages( $arr ) {
			array_push( $arr, 'edit-lp_collection' );
			array_push( $arr, 'lp_collection' );
			return $arr;
		}

		/**
		 * @return mixed
		 */
		public function collection_settings_metabox() {
			$prefix   = '_lp_';
			$meta_box = array(
				'id'       => 'collection_settings',
				'title'    => 'Settings',
				'context'  => 'normal',
				'priority' => 'low',
				'pages'    => array( 'lp_collection' ),
				'fields'   => array(
					array(
						'name'        => __( 'Courses', 'learnpress' ),
						'id'          => "{$prefix}collection_courses",
						'type'        => 'post',
						'post_type'   => 'lp_course',
						'field_type'  => 'select_advanced',
						'multiple'    => true,
						'desc'        => __( 'Collecting related courses into one collection', 'learnpress' ),
						'placeholder' => __( 'Select courses', 'learnpress' ),
						'query_args'  => array(
							'author' => ''
						)
					),
					array(
						'name'    => __( 'Courses per page', 'learnpress' ),
						'id'      => "{$prefix}collection_courses_per_page",
						'type'    => 'number',
						'default' => '10',
						'desc'    => __( 'Number of courses per each page. Default is 10.' )
					)
				)
			);
			return new RW_Meta_Box( apply_filters( 'learn_press_collection_settings_metabox', $meta_box ) );
		}

		/**
		 * Update course collections
		 *
		 * @param $collection_id
		 */
		public function update_course_collections( $collection_id ) {
			$new_courses = isset( $_POST['_lp_collection_courses'] ) ? $_POST['_lp_collection_courses'] : array();
			$old_courses = get_post_meta( $collection_id, '_lpr_collection_courses' ) ? get_post_meta( $collection_id, '_lp_collection_courses' ) : array();
			$added       = array_diff( $new_courses, $old_courses );
			$removed     = array_diff( $old_courses, $new_courses );
			if ( $added ) {
				foreach ( $added as $course ) {
					$collections = get_post_meta( $course, '_lp_course_collections' );
					if ( !in_array( $collection_id, $collections ) ) {
						add_post_meta( $course, '_lp_course_collections', $collection_id );
					}
				}
			}
			if ( $removed ) {
				foreach ( $removed as $course ) {
					$collections = get_post_meta( $course, '_lp_course_collections' );
					if ( in_array( $collection_id, $collections ) ) {
						delete_post_meta( $course, '_lp_course_collections', $collection_id );
					}
				}
			}
		}

		/**
		 * Update collection courses
		 *
		 * @param $course_id
		 */
		public function update_collection_courses( $course_id ) {
			$new_collections = isset ( $_POST['_lp_course_collections'] ) ? $_POST['_lp_course_collections'] : array();
			$old_collections = get_post_meta( $course_id, '_lp_course_collections' ) ? get_post_meta( $course_id, '_lp_course_collections' ) : array();
			$added           = array_diff( $new_collections, $old_collections );
			$removed         = array_diff( $old_collections, $new_collections );

			if ( $added ) {
				foreach ( $added as $collection ) {
					$courses = get_post_meta( $collection, '_lp_collection_courses' );
					if ( !in_array( $course_id, $courses ) ) {
						add_post_meta( $collection, '_lp_collection_courses', $course_id );
					}
				}
			}
			if ( $removed ) {
				foreach ( $removed as $collection ) {
					$courses = get_post_meta( $collection, '_lp_collection_courses' );
					if ( in_array( $course_id, $courses ) ) {
						delete_post_meta( $collection, '_lp_collection_courses', $course_id );
					}
				}
			}
		}

		/**
		 * @param $columns
		 *
		 * @return array
		 */
		public function set_custom_edit_lp_collection_columns( $columns ) {
			$new_columns = array(
				'cb'       => $columns['cb'],
				'title'    => $columns['title'],
				'courses'  => __( 'Courses', 'learnpress' ),
				'comments' => $columns['comments'],
				'date'     => $columns['date']
			);
			return $new_columns;
		}

		public function custom_lp_collection_column( $column, $post_id ) {
			if ( 'courses' == $column ) {
				$ids = get_post_meta( $post_id, '_lp_collection_courses' );
				if ( empty( $ids ) ) {
					_e( 'No Items', 'learnpress' );
				} else {
					foreach ( $ids as $id ) {
						$item = get_post( $id );
						if ( $item ) {
							echo '<a href="' . get_permalink( $item->ID ) . '">' . $item->post_title . '</a><br/>';
						}
					}
				}
			}
		}

		public static function admin_notice() {
			?>
			<div class="error">
				<p><?php printf( __( '<strong>Collections</strong> addon version %s requires <strong>LearnPress</strong> version %s or higher', 'learnpress' ), LP_COLLECTIONS_VER, LP_COLLECTIONS_REQUIRE_VER ); ?></p>
			</div>
			<?php
		}

		/**
		 * @return LP_Addon_Collections|null
		 */
		public static function instance() {
			if ( !defined( 'LEARNPRESS_VERSION' ) || ( version_compare( LEARNPRESS_VERSION, LP_COLLECTIONS_REQUIRE_VER, '<' ) ) ) {
				add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
				return false;
			}

			if ( !self::$_instance ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}
}
add_action( 'learn_press_ready', array( 'LP_Addon_Collections', 'instance' ) );
