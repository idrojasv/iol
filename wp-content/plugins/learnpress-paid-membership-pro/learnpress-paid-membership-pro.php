<?php
/*
Plugin Name: LearnPress - Paid Membership Pro Integration
Plugin URI: http://thimpress.com/learnpress
Description: Paid Membership Pro add-on for LearnPress
Author: ThimPress
Version: 2.2.2
Author URI: http://thimpress.com
Tags: learnpress, lms
Text Domain: learnpress
Domain Path: /languages/
*/

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'LP_ADDON_PMPRO_FILE', __FILE__ );
define( 'LP_ADDON_PMPRO_PATH', dirname( __FILE__ ) );
define( 'LP_ADDON_PMPRO_URL', plugin_dir_url( __FILE__ ) );
define( 'LP_ADDON_PMPRO_URI', plugins_url( '/', LP_ADDON_PMPRO_FILE ) );
define( 'LP_ADDON_PMPRO_VER', '2.1.1' );
define( 'LP_ADDON_PMPRO_REQUIRE_VER', '2.1.1' );

/**
 * Class LP_Addon_PMPRO
 */
class LP_Addon_PMPRO {

	/**
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * @var
	 */
	public $pmpro_levels;

	/**
	 * @var
	 */
	protected $user;

	/**
	 * @var
	 */
	protected $user_level;

	/**
	 * @var bool
	 */
	private $_meta_box = false;

	/**
	 * LP_Addon_PMPRO constructor.
	 */
	function __construct() {
		add_action( 'admin_notices', array( $this, 'notifications' ) );

		if ( self::pmpro_is_active() && self::learnpress_is_active() ) {
			$this->_init_hooks();
			include 'inc/order.php';
		}

	}

	public function init() {
		$this->_require();
		$this->pmpro_levels = pmpro_getAllLevels();
		$this->user         = learn_press_get_current_user();
		if ( $this->user ) {
			$this->user_level = pmpro_getMembershipLevelForUser( $this->user->id );
		}

		if ( is_admin() ) {
			$this->admin_require();
		}
	}

	public function admin_require() {
		$this->add_setting();
	}

	public function _require() {
		require_once LP_ADDON_PMPRO_PATH . '/inc/functions.php';
	}

	public function add_setting() {
		require_once LP_ADDON_PMPRO_PATH . '/inc/classes/setting-membership.php';
	}

	public function notifications() {
		if ( $this->pmpro_is_active() ) {
			return;
		};
		?>
		<div class="notice notice-error">
			<p><?php
				echo wp_kses( '<strong>Paid Membership Pro</strong> addon for <strong>LearnPress</strong> requires <a href="https://wordpress.org/plugins/paid-memberships-pro/" target="_blank">Paid Memberships Pro</a> plugin is installed.', array(
					'a'      => array(
						'href'   => array(),
						'target' => array(),
					),
					'strong' => array(),
				) );
				?></p>
		</div>
		<?php
	}

	function pmpro_can_enroll( $course_id ) {
		$course_levels = get_post_meta( $course_id, '_lp_pmpro_levels', false );
		$has_access    = $this->checkUserHasLevel( $course_levels );

		return $has_access;
	}

	/**
	 * @param array $levels array level_id
	 *
	 * @return bool
	 */
	private function checkUserHasLevel( $levels ) {
		$levels = (array) $levels;

		if ( !$this->user_level ) {
			return false;
		}

		foreach ( $levels as $l ) {
			if ( $l == $this->user_level->ID ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Init hooks
	 */
	private function _init_hooks() {

		//add_action( 'learn_press_meta_box_loaded', array( $this, 'add_meta_box' ) );
		add_action( 'init', array( __CLASS__, 'load_text_domain' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'learn_press_settings_tabs_array', array( $this, 'add_tab' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		/* Custom Templates */
		add_filter( 'pmpro_pages_custom_template_path', array(
			$this,
			'learn_press_pmpro_pages_custom_template_path'
		), 10, 5 );
		add_action( 'pmpro_checkout_after_pricing_fields', array(
			$this,
			'learn_press_pmpro_checkout_after_pricing_fields'
		) );
		add_filter( 'pmpro_email_data', array( $this, 'learn_press_pmpro_email_data' ), 10, 2 );
		add_action( 'learn_press_before_course_buttons', array( $this, 'learn_press_before_course_buttons' ), 10 );
		add_filter( 'learn_press_purchase_button_text', array( $this, 'learn_press_pmpro_purchase_button_text' ), 10 );
		add_shortcode( 'lp_pmpro_courses', array( $this, 'learn_press_page_levels_short_code' ) );

		add_action( 'pmpro_before_change_membership_level', array(
			$this,
			'learn_press_pmpro_after_change_membership_level'
		), 10, 4 );

		add_action( 'load-post.php', array( $this, 'memberships_meta_box' ), 20 );
		add_action( 'load-post-new.php', array( $this, 'memberships_meta_box' ), 20 );
		add_filter( 'learn_press_lp_course_tabs', array( $this, 'add_meta_box' ) );
		add_filter( 'learn_press_external_link_buy_course', array( $this, 'learn_press_external_link_buy_course' ), 10, 3 );
	}

	public function memberships_meta_box() {
		$this->_meta_box = new RW_Meta_Box( $this->meta_box() );
	}

	public function add_tab( $tabs ) {
		$tabs['membership'] = __( 'Memberships', 'learnpress' );

		return $tabs;
	}

	public function wp_enqueue_script() {
		wp_enqueue_style( 'learn-press-pmpro-style', LP_ADDON_PMPRO_URL . 'assets/style.css', array(), LP_ADDON_PMPRO_VER );
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'learn-press-pmpro-script', LP_ADDON_PMPRO_URL . 'assets/admin-script.js', array(), LP_ADDON_PMPRO_VER, true );
	}

	public function learn_press_pmpro_pages_custom_template_path( $default_templates, $page_name, $type, $where, $ext ) {
		$template = learn_press_pmpro_locate_template( "{$type}/{$page_name}.{$ext}" );

		return array( $template );
	}

	public function learn_press_pmpro_email_data( $data, $email ) {

		$path_email = LP_ADDON_PMPRO_PATH . '/templates/email/';
		$path_email = apply_filters( 'learn_press_pmpro_email_custom_template_path', $path_email, $data, $email );

		if ( !empty( $email->body ) && !empty( $email->template ) && file_exists( $path_email . $email->template . ".html" ) ) {
			$email->body = file_get_contents( $path_email . $email->template . ".html" );
		}

		return $data;
	}

	public function learn_press_pmpro_checkout_after_pricing_fields() {
		$content = pmpro_loadTemplate( 'checkout-custom-pricing', 'local', 'pages' );
		echo $content;
	}

	public function learn_press_before_course_buttons( $course_id ) {

		$content = pmpro_loadTemplate( 'course-notice', 'local', 'pages' );
		echo $content;
	}

	public function add_meta_box( $tabs ) {
		$tabs[] = $this->_meta_box;
		return $tabs;
	}

	function meta_box() {
		$prefix         = '_lp_';
		$options_levels = array();
		foreach ( $this->pmpro_levels as $pmpro_level ) {
			$options_levels[$pmpro_level->id] = $pmpro_level->name;
		}

		$meta_box = array(
			'id'       => 'course_pmpro',
			'title'    => __( 'Course Memberships', 'learnpress' ),
			'pages'    => array( 'lp_course' ),
			'fields'   => array(
				array(
					'name'        => __( 'Select Membership Levels', 'learnpress' ),
					'id'          => "{$prefix}pmpro_levels",
					'type'        => 'select_advanced',
					'options'     => $options_levels,
					'multiple'    => true,
					'placeholder' => __( 'Select membership levels', 'learnpress' ),
				),
			)
		);

		return apply_filters( 'learn_press_pmpro_meta_box_args', $meta_box );
	}

	public function learn_press_external_link_buy_course ( $external_link_buy_course, $course, $user ) {
		$buy_through_membership = LP()->settings->get( 'buy_through_membership' );
		$is_required            = learn_press_pmpro_check_require_template();

		if ( ! empty( $buy_through_membership ) && $buy_through_membership === 'yes' && $is_required ) {
			return '';
		}

		return $external_link_buy_course;
	}

	public function learn_press_pmpro_purchase_button_text( $purchase_text ) {
		$is_required            = learn_press_pmpro_check_require_template();
		$buy_through_membership = LP()->settings->get( 'buy_through_membership' );
		$new_text               = LP()->settings->get( 'button_buy_course' );
		if ( !empty( $buy_through_membership ) && $buy_through_membership == 'no' && !empty( $new_text ) && $is_required ) {
			return $new_text;
		}

		return $purchase_text;
	}

	public function learn_press_page_levels_short_code() {

		echo do_shortcode( '[pmpro_levels]' );
	}

	public function learn_press_pmpro_after_change_membership_level( $level_id, $user_id, $old_levels, $cancel_level ) {

	}

	/**
	 * Return TRUE if Paid Membership PRO plugin is installed and active
	 *
	 * @return bool
	 */
	static function pmpro_is_active() {
		if ( !function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		return is_plugin_active( 'paid-memberships-pro/paid-memberships-pro.php' );
	}

	/**
	 * Return TRUE if Paid Membership PRO plugin is installed and active
	 *
	 * @return bool
	 */
	static function learnpress_is_active() {
		if ( !function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		return is_plugin_active( 'learnpress/learnpress.php' ) || is_plugin_active( 'LearnPress/learnpress.php' );
	}

	/**
	 * Load plugin text domain
	 */
	public static function load_text_domain() {
		if ( function_exists( 'learn_press_load_plugin_text_domain' ) ) {
			learn_press_load_plugin_text_domain( LP_ADDON_PMPRO_PATH, true );
		}
	}

	public static function admin_notice() {
		?>
		<div class="error">
			<p><?php printf( __( '<strong>Paid Membership Pro</strong> addon version %s requires LearnPress version %s or higher', 'learnpress' ), LP_ADDON_PMPRO_VER, LP_ADDON_PMPRO_REQUIRE_VER ); ?></p>
		</div>
		<?php
	}

	/**
	 * Return unique instance of LP_Addon_BBPress_Forum
	 */
	static function instance() {
		if ( !defined( 'LEARNPRESS_VERSION' ) || ( version_compare( LEARNPRESS_VERSION, LP_ADDON_PMPRO_REQUIRE_VER, '<' ) ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );

			return false;
		}
		if ( !self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}

add_action( 'learn_press_ready', array( 'LP_Addon_PMPRO', 'instance' ) );
