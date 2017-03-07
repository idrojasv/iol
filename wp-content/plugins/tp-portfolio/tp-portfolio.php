<?php
if ( !class_exists( 'THIM_Portfolio' ) ) {
	/**
	 * Thim Theme
	 *
	 * Manage the portfolio in the THIM Framework
	 *
	 * @class      THIM_Portfolio
	 * @package    thimpress
	 * @since      1.3
	 * @author     kien16
	 */
	class THIM_Portfolio {

		/**
		 * @var string
		 * @since 1.0
		 */
		public $version = THIM_PORTFOLIO_VERSION;

		/**
		 * @var object The single instance of the class
		 * @since 1.0
		 */
		protected static $_instance = null;

		/**
		 * @var string
		 * @since 1.0
		 */
		public $plugin_url;

		/**
		 * @var string
		 * @since 1.0
		 */
		public $plugin_path;

		/**
		 * The array of templates that this plugin tracks.
		 *
		 * @var      array
		 */
		protected $templates;

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'portfolio_template_path', 'thim-portfolio/' );
		}

		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @return object Main instance
		 *
		 * @since  1.0
		 * @author Antonino Scarfì <antonino.scarfi@yithemes.com>
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers the portfolio cpt
		 */
		public function __construct() {

			// Define the url and path of plugin
			$this->plugin_url  = untrailingslashit( plugins_url( '/', __FILE__ ) );
			$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );

			// Register CPTU
			add_action( 'after_setup_theme', array( $this, 'register_cptu' ), 20 );

			// Register Taxonomy
			add_action( 'after_setup_theme', array( $this, 'register_taxonomy' ), 20 );


			require_once 'lib/aq_resizer.php';

			// Display custom update messages for posts edits
			add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );

			// Include OWN Metabox
			require_once 'lib/meta-boxes/thim-meta-box.php';
			require_once 'lib/meta-boxes/init.php';
			add_filter( 'thim_meta_boxes', array( $this, 'register_metabox' ), 20 );

			add_action( 'template_include', array( $this, 'template_include' ), 20 );

			// Load text domain
			add_action( 'plugins_loaded', array( $this, 'text_domain' ) );
		}

		public function text_domain() {
			// Get mo file
			$text_domain = 'tp-portfolio';
			$locale      = apply_filters( 'plugin_locale', get_locale(), $text_domain );
			$mo_file     = $text_domain . '-' . $locale . '.mo';
			// Check mo file global
			$mo_global = WP_LANG_DIR . '/plugins/' . $mo_file;
			// Load translate file
			if ( file_exists( $mo_global ) ) {
				load_textdomain( $text_domain, $mo_global );
			} else {
				load_textdomain( $text_domain, CORE_PLUGIN_PATH . '/languages/' . $mo_file );
			}
		}

		/**
		 * Enqueue script and styles in admin side
		 *
		 * Add style and scripts to administrator
		 *
		 * @return void
		 * @since    1.0
		 * @author   thim
		 */
		public function thim_scripts() {
			//scripts
//			wp_deregister_script('isotope');
//			wp_register_script('isotope', JS_URL . 'isotope.pkgd.min.js', array('jquery'), false, true);
//			wp_enqueue_script('isotope');
//
//			wp_deregister_script('jquery.appear');
//			wp_register_script('jquery.appear', JS_URL . 'jquery.appear.js', array('jquery'), false, true);
//			wp_enqueue_script('jquery.appear');
//
//			wp_deregister_script('infinitescroll');
//			wp_register_script('infinitescroll', JS_URL . 'jquery.infinitescroll.min.js', array('jquery'), false, true);
//			wp_enqueue_script('infinitescroll');
//
//			wp_deregister_script('flexslider');
//			wp_register_script('flexslider', JS_URL . 'jquery.flexslider-min.js', array('jquery'), false, true);
//
//			wp_deregister_script('magnific-popup');
//			wp_register_script('magnific-popup', JS_URL . 'magnific-popup.min.js', array('jquery'), '1.0', true);
//			wp_enqueue_script('magnific-popup');
//
//			wp_deregister_script('jquery.prettyPhoto');
//			wp_register_script('jquery.prettyPhoto', JS_URL . 'jquery.prettyPhoto.js', array('jquery'), '1.0', true);
//			wp_enqueue_script('jquery.prettyPhoto');
//
//
//			wp_enqueue_script('jquery-imagesloaded', JS_URL . 'imagesloaded.pkgd.js', array(), false, false);
//			wp_enqueue_script('jquery-portfolio', JS_URL . 'portfolio.js', array(), false, false);
//
//			wp_enqueue_style('css-magnific-popup', CSS_URL . 'magnific-popup.css', array());
//			wp_enqueue_style('css-prettyPhoto', CSS_URL . 'prettyPhoto.css', array());
//			wp_enqueue_style('css-portfolio', CSS_URL . 'portfolio.css', array());
		}


		/**
		 * Template part Redirect.
		 *
		 * @access public
		 * @return void
		 */
		public function template_include( $template ) {

			if ( get_post_type() == 'portfolio' && ( is_category() || is_archive() ) ) {
				$template = $this->get_template_part( 'archive', 'portfolio' );
			} else if ( get_post_type() == 'portfolio' && is_single() ) {
				$template = $this->get_template_part( "single", 'portfolio' );
			}
			return $template;
		}

		/**
		 * Get template part (for templates like the shop-loop).
		 *
		 * @access public
		 *
		 * @param mixed  $slug
		 * @param string $name (default: '')
		 *
		 * @return void
		 */
		public function get_template_part( $slug, $name = '' ) {
			$template = '';
			// Look in yourtheme/slug-name.php and yourtheme/portfolio/slug-name.php
			if ( $name ) {
				$template = locate_template( array( "{$slug}-{$name}.php", 'portfolio/' . "{$slug}-{$name}.php" ) );
			}
			// Get default slug-name.php
			if ( !$template && $name && file_exists( $this->plugin_path . "/templates/{$slug}-{$name}.php" ) ) {
				$template = $this->plugin_path . "/templates/{$slug}-{$name}.php";
			}
			// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/portfolio/slug.php
			if ( !$template ) {
				$template = locate_template( array( "{$slug}.php", 'portfolio/' . "{$slug}.php" ) );
			}
			// Allow 3rd party plugin filter template file from their plugin
			$template = apply_filters( 'get_template_part', $template, $slug, $name );

			return $template;

		}

		/**
		 * Register the Custom Post Type Unlimited
		 *
		 * @return void
		 * @since  1.0
		 * @author thimpress
		 */
		public function register_cptu() {
			$labels = array(
				'name'               => _x( 'Projects', 'General Name', 'tp-portfolio' ),
				'singular_name'      => _x( 'Project', 'Singular Name', 'tp-portfolio' ),
				'menu_name'          => esc_html__( 'Portfolio', 'tp-portfolio' ),
				'parent_item_colon'  => esc_html__( 'Parent Portfolio:', 'tp-portfolio' ),
				'all_items'          => esc_html__( 'All Projects', 'tp-portfolio' ),
				'view_item'          => esc_html__( 'View Project', 'tp-portfolio' ),
				'add_new_item'       => esc_html__( 'Add New Project', 'tp-portfolio' ),
				'add_new'            => esc_html__( 'New Project', 'tp-portfolio' ),
				'edit_item'          => esc_html__( 'Edit Project', 'tp-portfolio' ),
				'update_item'        => esc_html__( 'Update Portfolio', 'tp-portfolio' ),
				'search_items'       => esc_html__( 'Search Projects', 'tp-portfolio' ),
				'not_found'          => esc_html__( 'No Projects found', 'tp-portfolio' ),
				'not_found_in_trash' => esc_html__( 'No Projects found in Trash', 'tp-portfolio' ),
			);
			$args   = array(
				'labels'      => $labels,
				'supports'    => array( 'title', 'editor', 'thumbnail' ),
				'menu_icon'   => 'dashicons-portfolio',
				'public'      => true,
				'has_archive' => true,
				'rewrite'     => array( 'slug' => 'portfolio' )
			);
			register_post_type( 'portfolio', $args );
		}

		/**
		 * Register Portfolio Taxonomy
		 *
		 * @return void
		 * @since  1.0
		 */
		public function register_taxonomy() {
			// Portfolio Categories
			$labels = array(
				'name'                       => _x( 'Project Types', 'Taxonomy General Name', 'tp-portfolio' ),
				'singular_name'              => _x( 'Project Type', 'Taxonomy Singular Name', 'tp-portfolio' ),
				'menu_name'                  => esc_html__( 'Project Types', 'tp-portfolio' ),
				'all_items'                  => esc_html__( 'All Project Types', 'tp-portfolio' ),
				'parent_item'                => esc_html__( 'Parent Project Type', 'tp-portfolio' ),
				'parent_item_colon'          => esc_html__( 'Parent Project Type:', 'tp-portfolio' ),
				'new_item_name'              => esc_html__( 'New Project Type Name', 'tp-portfolio' ),
				'add_new_item'               => esc_html__( 'Add New Project Type', 'tp-portfolio' ),
				'edit_item'                  => esc_html__( 'Edit Project Type', 'tp-portfolio' ),
				'update_item'                => esc_html__( 'Update Project Type', 'tp-portfolio' ),
				'separate_items_with_commas' => esc_html__( 'Separate Project Types with commas', 'tp-portfolio' ),
				'search_items'               => esc_html__( 'Search Project Types', 'tp-portfolio' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove Project Types', 'tp-portfolio' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used Project Types', 'tp-portfolio' ),
			);
			$args   = array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'menu_icon'         => 'dashicons-portfolio',
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'portfolio_category' ),
			);
			register_taxonomy( 'portfolio_category', 'portfolio', $args );
		}

		/**
		 * Change updated messages
		 *
		 * @param  array $messages
		 *
		 * @return array
		 * @since  1.0
		 */
		public function updated_messages( $messages = array() ) {
			global $post, $post_ID;
			$messages['portfolio'] = array(
				0  => '',
				1  => sprintf( __( 'Portfolio updated. <a href="%s">View Portfolio</a>', 'tp-portfolio' ), esc_url( get_permalink( $post_ID ) ) ),
				2  => __( 'Custom field updated.', 'tp-portfolio' ),
				3  => __( 'Custom field deleted.', 'tp-portfolio' ),
				4  => __( 'Portfolio updated.', 'tp-portfolio' ),
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Portfolio restored to revision from %s', 'tp-portfolio' ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
				6  => sprintf( __( 'Portfolio published. <a href="%s">View Portfolio</a>', 'tp-portfolio' ), esc_url( get_permalink( $post_ID ) ) ),
				7  => __( 'Portfolio saved.', 'tp-portfolio' ),
				8  => sprintf( __( 'Portfolio submitted. <a target="_blank" href="%s">Preview Portfolio</a>', 'tp-portfolio' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
				9  => sprintf( __( 'Portfolio scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Portfolio</a>', 'tp-portfolio' ), date_i18n( __( 'M j, Y @ G:i', 'tp-portfolio' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
				10 => sprintf( __( 'Portfolio draft updated. <a target="_blank" href="%s">Preview Portfolio</a>', 'tp-portfolio' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			);

			return $messages;
		}

		/**
		 * Register Portfolio Metabox
		 *
		 * @return void
		 * @since  1.0
		 */
		public function register_options( $options ) {
			// Better has an underscore as last sign
			$prefix = 'thim_portfolio_option_';

			/******************************************
			 *        General Setting
			 ******************************************/
			$options[] = array( "name" => esc_html__( 'General Settings', 'tp-portfolio' ),
			                    "type" => "heading" );

			$options[$prefix . 'cate_hide_breadcrumbs'] = array( 'name' => esc_html__( 'Hide Breadcrumbs?', 'tp-portfolio' ),
			                                                     'type' => 'checkbox',
			                                                     'id'   => $prefix . 'cate_hide_breadcrumbs',
			                                                     'desc' => esc_html__( 'Check this box to hide/show Breadcrumbs', 'tp-portfolio' ),
			                                                     'std'  => ''
			);

			$options[$prefix . 'cate_hide_title'] = array( 'name' => esc_html__( 'Hide Title', 'tp-portfolio' ),
			                                               'type' => 'checkbox',
			                                               'id'   => $prefix . 'cate_hide_title',
			                                               'desc' => esc_html__( 'Check this box to hide/unhide title', 'tp-portfolio' ),
			                                               'std'  => ''
			);

			$options[$prefix . 'top_image'] = array(
				'name' => esc_html__( 'Top Image', 'tp-portfolio' ),
				'desc' => '',
				'id'   => $prefix . 'top_image',
				'type' => 'image',
				'std'  => ''
			);

			$options[$prefix . 'cate_heading_bg_color'] = array( 'name' => esc_html__( 'Background Heading Color', 'tp-portfolio' ),
			                                                     'type' => 'color-opacity',
			                                                     'id'   => $prefix . 'cate_heading_bg_color',
			                                                     'desc' => '',
			                                                     'std'  => ''
			);

			$options[$prefix . 'cate_heading_text_color'] = array( 'name' => __( 'Text Color Heading', 'tp-portfolio' ),
			                                                       'type' => 'color-opacity',
			                                                       'id'   => $prefix . 'cate_heading_text_color',
			                                                       'desc' => '',
			                                                       'std'  => ''
			);

			$options[$prefix . "dimensions"] = array(
				'name'    => esc_html__( 'Dimensions', 'tp-portfolio' ),
				'id'      => $prefix . "dimensions",
				'type'    => 'dimensions',
				'options' => array(
					'width'  => array( 'label' => 'Width', 'default' => '480' ),
					'height' => array( 'label' => 'Height', 'default' => '320' )
				),
			);


			/******************************************
			 *        Archive Settings
			 ******************************************/
			$options[] = array( "name" => esc_html__( 'Archive Settings', 'tp-portfolio' ),
			                    "type" => "heading" );

			$options[$prefix . "archive_layout"] = array( "name"    => esc_html__( 'Select a Layout', 'tp-portfolio' ),
			                                              "desc"    => "",
			                                              "id"      => $prefix . "archive_layout",
			                                              "std"     => "left-sidebar",
			                                              "type"    => "radioimage",
			                                              "options" => array(
				                                              "left-sidebar"  => CORE_PLUGIN_URL . "/lib/thim-options/img/sidebar-left.png",
				                                              "right-sidebar" => CORE_PLUGIN_URL . "/lib/thim-options/img/sidebar-right.png",
				                                              "no-sidebar"    => CORE_PLUGIN_URL . "/lib/thim-options/img/content-boxed.jpg",
				                                              "fullwidth"     => CORE_PLUGIN_URL . "/lib/thim-options/img/content-full.jpg",
			                                              ) );
			$options[$prefix . 'column']         = array(
				'name'    => esc_html__( 'Column', 'tp-portfolio' ),
				'id'      => $prefix . 'column',
				'type'    => 'radioimage',
				'std'     => 'four',
				'options' => array(
					'two'   => CORE_PLUGIN_URL . "/lib/thim-options/img/two-col.png",
					'three' => CORE_PLUGIN_URL . "/lib/thim-options/img/three-col.png",
					'four'  => CORE_PLUGIN_URL . "/lib/thim-options/img/four-col.png",
					'five'  => CORE_PLUGIN_URL . "/lib/thim-options/img/five-col.png",
				),
			);
			$options[$prefix . 'gutter']         = array(
				'name' => esc_html__( 'Enable gutter for Items', 'tp-portfolio' ),
				'id'   => $prefix . 'gutter',
				'type' => 'checkbox'
			);
			$options[$prefix . 'item_size']      = array(
				'name'    => esc_html__( 'Items Size', 'tp-portfolio' ),
				'id'      => $prefix . 'item_size',
				'type'    => 'select',
				'std'     => 'masonry',
				'options' => array(
					'multigrid' => 'Multigrid',
					'masonry'   => 'Masonry',
					'same'      => 'Same size',
				)
			);
			$options[$prefix . 'item_style']     = array(
				'name'    => esc_html__( 'Items Style', 'tp-portfolio' ),
				'id'      => $prefix . 'item_style',
				'type'    => 'select',
				'std'     => 'classic',
				'options' => array(
					'text'    => 'Text',
					'classic' => 'Classic',
				),
			);


			$options[$prefix . 'item_effect'] = array(
				'name'    => esc_html__( 'Images Hover Effects', 'tp-portfolio' ),
				'id'      => $prefix . 'item_effect',
				'type'    => 'select',
				'std'     => 'effects_classic',
				'options' => array(
					'effects_classic' => 'Classic',
					'effects_zoom_01' => 'Zoom In 01',
					'effects_zoom_02' => 'Zoom In 02',
				)
			);
			$options[$prefix . "paging"]      = array(
				'name'    => esc_html__( 'Pagination Styles', 'tp-portfolio' ),
				'id'      => $prefix . "paging",
				'type'    => 'select',
				'std'     => 'all',
				'options' => array(
					'all'             => 'Show All',
					'paging'          => 'Paging',
					'infinite_scroll' => 'Infinite Scroll',
				),
			);

			$options[$prefix . "num_per_view"] = array( "name" => "Number Per View",
			                                            "id"   => $prefix . "num_per_view",
			                                            "std"  => "8",
			                                            "type" => "number" );


			/******************************************
			 *        Single Page Settings
			 ******************************************/
			$options[] = array( "name" => esc_html__( 'Single Page Settings', 'tp-portfolio' ),
			                    "type" => "heading" );

			$options[$prefix . "single_layout"] = array( "name"    => esc_html__( 'Select a Layout', 'tp-portfolio' ),
			                                             "desc"    => "",
			                                             "id"      => $prefix . "single_layout",
			                                             "std"     => "no-sidebar",
			                                             "type"    => "radioimage",
			                                             "options" => array(
				                                             "left-sidebar"  => CORE_PLUGIN_URL . "/lib/thim-options/img/sidebar-left.png",
				                                             "right-sidebar" => CORE_PLUGIN_URL . "/lib/thim-options/img/sidebar-right.png",
				                                             "no-sidebar"    => CORE_PLUGIN_URL . "/lib/thim-options/img/content-boxed.jpg",
			                                             ) );


			$options = apply_filters( 'custom_thim_portfolio_options', $options );

			return $options;
		}

		/**
		 * Register Portfolio Metabox
		 *
		 * @return void
		 * @since  1.0
		 */
		public function register_metabox( $meta_boxes ) {
			$meta_boxes[] = array(
				'id'     => 'portfolio_settings',
				'title'  => esc_html__( 'Portfolio Settings', 'tp-portfolio' ),
				'pages'  => array( 'portfolio' ),
				'fields' => array(
					array(
						'name'    => esc_html__( 'Multigrid Size', 'tp-portfolio' ),
						'id'      => 'feature_images',
						'type'    => 'select',
						'desc'    => esc_html__( 'This config will working for portfolio layout style.', 'tp-portfolio' ),
						'std'     => 'Random',
						'options' => array(
							'random' => "Random",
							'size11' => "Size 1x1(480 x 320)",
							'size12' => "Size 1x2(480 x 640)",
							'size21' => "Size 2x1(960 x 320)",
							'size22' => "Size 2x2(960 x 640)"
						),
					),
					array(
						'name'     => esc_html__( 'Portfolio Type', 'tp-portfolio' ),
						'id'       => "selectPortfolio",
						'type'     => 'select',
						'options'  => array(
							'portfolio_type_image'                  => __( 'Image', 'tp-portfolio' ),
							'portfolio_type_slider'                 => __( 'Slider', 'tp-portfolio' ),
							'portfolio_type_video'                  => __( 'Video', 'tp-portfolio' ),
							'portfolio_type_left_floating_sidebar'  => __( 'Left Floating Sidebar', 'tp-portfolio' ),
							'portfolio_type_right_floating_sidebar' => __( 'Right Floating Sidebar', 'tp-portfolio' ),
							'portfolio_type_gallery'                => __( 'Gallery', 'tp-portfolio' ),
							'portfolio_type_sidebar_slider'         => __( 'Sidebar Slider', 'tp-portfolio' ),
							'portfolio_type_vertical_stacked'       => __( 'Vertical Stacked', 'tp-portfolio' ),
							'portfolio_type_page_builder'           => __( 'Page Builder', 'tp-portfolio' ),

						),
						// Select multiple values, optional. Default is false.
						'multiple' => false,
						'std'      => 'portfolio_type_image',
					),

					array(
						'name'     => esc_html__( 'Video', 'tp-portfolio' ),
						'id'       => 'project_video_type',
						'type'     => 'select',
						'class'    => 'portfolio_type_video',
						'options'  => array(
							'youtube' => 'Youtube',
							'vimeo'   => 'Vimeo',
						),
						'multiple' => false,
						'std'      => array( 'no' )
					),
					array(
						'name'  => esc_html__( "Video URL or own Embedd Code<br />(Audio Embedd Code is possible, too)", 'tp-portfolio' ),
						'id'    => 'project_video_embed',
						'desc'  => esc_html__( "Just paste the ID of the video (E.g. http://www.youtube.com/watch?v=<strong>GUEZCxBcM78</strong>) you want to show, or insert own Embed Code. <br />This will show the Video <strong>INSTEAD</strong> of the Image Slider.<br /><strong>Of course you can also insert your Audio Embedd Code!</strong><br /><br /><strong>Notice:</strong> The Preview Image will be the Image set as Featured Image..", 'tp-portfolio' ),
						'type'  => 'textarea',
						'class' => 'portfolio_type_video',
						'std'   => "",
						'cols'  => "40",
						'rows'  => "8"
					),

					array(
						'name'             => esc_html__( 'Upload Image', 'tp-portfolio' ),
						'desc'             => esc_html__( 'Upload up images for a slideshow - or only one to display a single image. <br /><br /><strong>Notice:</strong> The Preview Image will be the Image set as Featured Image.', 'tp-portfolio' ),
						'id'               => 'project_item_slides',
						'type'             => 'image',
						'max_file_uploads' => 1,
						'class'            => 'portfolio_type_image portfolio_type_gallery portfolio_type_vertical_stacked',
					),

					array(
						'name'             => esc_html__( 'Upload Image', 'tp-portfolio' ),
						'desc'             => esc_html__( 'Upload up images for a slideshow - or only one to display a single image. <br /><br /><strong>Notice:</strong> The Preview Image will be the Image set as Featured Image.', 'tp-portfolio' ),
						'id'               => 'portfolio_sliders',
						'type'             => 'image_video',
						'class'            => 'portfolio_type_sidebar_slider portfolio_type_slider portfolio_type_left_floating_sidebar portfolio_type_right_floating_sidebar',
						'max_file_uploads' => 20,
					),
				)
			);

			return $meta_boxes;
		}
	}

	/**
	 * Main instance of plugin
	 *
	 * @return \THIM_Portfolio
	 * @since  1.0
	 * @author thimpress
	 */
	function THIM_Portfolio() {
		return THIM_Portfolio::instance();
	}

	/**
	 * Instantiate Portfolio class
	 *
	 * @since  1.1
	 * @author thimpress
	 */
	THIM_Portfolio();
}