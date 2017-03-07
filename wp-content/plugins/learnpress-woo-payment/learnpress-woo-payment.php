<?php
/*
Plugin Name: LearnPress - WooCommerce Payment Methods Integration
Plugin URI: http://thimpress.com/learnpress
Description: Using the payment system provided by WooCommerce
Author: ThimPress
Version: 2.2.3.1
Author URI: http://thimpress.com
Tags: learnpress, woocommerce
Text Domain: learnpress
Domain Path: /languages/
*/
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
define( 'LP_ADDON_WOOCOMMERCE_PAYMENT_FILE', __FILE__ );
define( 'LP_ADDON_WOOCOMMERCE_PAYMENT_PATH', dirname( __FILE__ ) );
define( 'LP_ADDON_WOOCOMMERCE_PAYMENT_VER', '2.2.3' );
define( 'LP_ADDON_WOOCOMMERCE_PAYMENT_REQUIRE_VER', '2.0' );

class LP_Woo_Payment_Init {

	/**
	 * @var int flag to get the error
	 */
	protected static $_error = 0;

	/**
	 * Courses should not display purchase button
	 *
	 * @var array
	 */
	protected $_hide_purchase_buttons = array();

	/**
	 * @var bool
	 */
	protected $_single_purchase = false;

	/**
	 * @var array
	 */
	protected $_response = array();

	/**
	 * @var LP_Woo_Payment_Init|null
	 *
	 * Hold the singleton of LP_Woo_Payment_Init object
	 */
	protected static $_instance = null;

	/**
	 * LP_Woo_Payment_Init constructor.
	 */
	public function __construct() {
		LP_Assets::add_script_tag( $this->_admin_js(), '__all' );
		LP_Assets::add_param( 'woocommerce_cart_option', LP()->settings->get( 'woo_payment_type' ), '__all', 'LP_WooCommerce_Payment' );
		$this->_includes();
		$this->_response['single_purchase'] = LP()->settings->get( 'woo_purchase_button' ) == 'single';
	}

	private function _admin_js() {
		if ( !is_admin() ) {
			return '';
		}
		ob_start();
		?>
		<script>
			$('#learn_press_woo_payment_enabled').on('change', function () {
				$('[name="learn_press_woo_payment_type"]').prop('disabled', !this.checked);
			}).trigger('change');
			$(document).on('change', '.woo_payment_type', function (e) {
				e.preventDefault();
				var _this = $(this),
					_value = _this.val(),
					_wrapper = $('.woocommerce_payment_available');
				if (_value === 'payment') {
					_wrapper.removeClass('hide-if-js');
				} else {
					_wrapper.addClass('hide-if-js');
				}
				return false;
			});
		</script>
		<?php
		return ob_get_clean();
	}

	/**
	 * Include files needed
	 */
	public function _includes() {
		// load text domain
		$this->load_textdomain();
		// WooCommerce activated
		if ( $this->woo_actived() && function_exists( 'LP' ) ) {
			require_once LP_ADDON_WOOCOMMERCE_PAYMENT_PATH . '/incs/functions.php';
			// Enabled payment and checkout
			if ( $this->is_enabled() && $this->woo_payment_enabled() || $this->woo_checkout_enabled() ) {
				require_once LP_ADDON_WOOCOMMERCE_PAYMENT_PATH . '/incs/class-wc-product-lp-course.php';
			}
			// init hooks
			$this->init_hooks();
			$payment = LP_ADDON_WOOCOMMERCE_PAYMENT_PATH . '/incs/class-lp-wc-payment.php';
			if ( file_exists( $payment ) ) {
				require_once $payment;
			}

			if ( $this->is_enabled() && $this->woo_checkout_enabled() ) {
				// WooCommerce checkout
				$checkout = require_once LP_ADDON_WOOCOMMERCE_PAYMENT_PATH . '/incs/class-lp-wc-checkout.php';
				if ( file_exists( $checkout ) ) {
					require_once $checkout;
				}
			}
		} else {
			self::$_error = 1;
			add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
		}
	}

	/**
	 * Init hooks
	 */
	public function init_hooks() {
		if ( !$this->is_enabled() ) {
			return;
		}
		add_filter( 'woocommerce_product_class', array( $this, 'product_class' ), 10, 4 );
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'disable_quantity_box' ), 10, 3 );
		add_filter( 'woocommerce_add_to_cart_handler', array( $this, 'add_to_cart_handler' ), 10, 2 );
		add_filter( 'learn_press_purchase_course_login_redirect', '__return_false' );

		add_action( 'woocommerce_order_status_changed', array( $this, 'learnpress_update_order_status' ), 10, 3 );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'create_order_2' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'learn_press_before_course_buttons', array( $this, 'purchase_course_notice' ), 10 );
		add_action( 'learn_press_after_course_buttons', array( $this, 'after_course_buttons' ) );
		add_action( 'learn_press_before_purchase_button', array( $this, 'before_purchase_button' ) );
		add_action( 'learn_press_after_purchase_button', array( $this, 'after_purchase_button' ) );
		add_action( 'learn_press_after_purchase_button', array( $this, 'add_to_cart' ) );
		add_action( 'admin_notices', array( $this, 'wc_order_notice' ), 99 );
	}

	/**
	 * @param $product_type
	 * @param $adding_to_cart
	 *
	 * @return mixed
	 */
	public function add_to_cart_handler( $product_type, $adding_to_cart ) {
		if ( !$product_type != LP_COURSE_CPT ) {
			return $product_type;
		}
		$this->_response['single_purchase'] = learn_press_get_request( 'single-purchase' ) == 'yes';
		if ( $this->_response['single_purchase'] ) {
			WC()->cart->empty_cart();
		}
		add_action( 'woocommerce_add_to_cart', array( $this, 'added_to_cart' ), 10, 6 );
	}

	/**
	 * @param $cart_item_key
	 * @param $product_id
	 * @param $quantity
	 * @param $variation_id
	 * @param $variation
	 * @param $cart_item_data
	 */
	public function added_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
		if ( $this->_response['single_purchase'] ) {
			$this->_response['redirect'] = wc_get_checkout_url();
		} else {
		}
		add_filter( 'pre_option_woocommerce_cart_redirect_after_add', array( $this, 'cart_redirect_after_add' ), 1000, 2 );
		add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'add_to_cart_redirect' ), 1000 );
		ob_start();
		wc_add_to_cart_message( array( $product_id => $quantity ), true );
		wc_print_notices();
		$this->_response['message'] = ob_get_clean();
		add_action( 'shutdown', array( $this, 'shutdown' ), 100 );
	}

	/**
	 * @param $a
	 * @param $b
	 *
	 * @return string
	 */
	public function cart_redirect_after_add( $a, $b ) {
		return 'no';
	}

	/**
	 * @param $a
	 *
	 * @return bool
	 */
	public function add_to_cart_redirect( $a ) {
		return false;
	}

	/**
	 *
	 */
	public function shutdown() {
		$output = ob_get_clean();
		if ( $this->_response ) {
			learn_press_send_json( $this->_response );
		}
	}

	/**
	 *
	 */
	public function wc_order_notice() {
		global $post, $pagenow;
		if ( $pagenow != 'post.php' || empty( $post ) || get_post_type( $post->ID ) != 'shop_order' ) {
			return;
		}
		if ( !$lp_order_id = get_post_meta( $post->ID, '_learn_press_order_id', true ) ) {
			return;
		}
		?>
		<style type="text/css">
			.woo-payment-order-notice p {
				font-size: 24px;
			}
		</style>
		<div class="error woo-payment-order-notice">
			<p>
				<?php printf( __( 'This order is related to LearnPress order, so if you want to do anything with LearnPres please edit it <a href="%s">here</a>', 'learnpress' ), get_edit_post_link( $lp_order_id ) ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * @param $wc_order_id
	 * @param $posted
	 */
	public function create_order_2( $wc_order_id, $posted ) {
		// Get LP order key related with WC order
		if ( get_post_meta( $wc_order_id, '_lp_order_id' ) ) {
			return;
		}

		// Get wc order
		$wc_order = wc_get_order( $wc_order_id );
		if ( !$wc_order ) {
			return;
		}

		// Get wc order items
		$wc_items = $wc_order->get_items();
		if ( !$wc_items ) {
			return;
		}

		// Find LP courses in WC order and preparing to create LP Order
		$courses = array();
		foreach ( $wc_items as $item ) {
			$course_id = $item['product_id'];
			// ignore item is not a course post type
			if ( LP_COURSE_CPT != get_post_type( $course_id ) ) {
				continue;
			}
			$courses[] = $item;
		}

		// If there is no course in wc order
		if ( !$courses ) {
			return;
		}

		// Create LP Order
		$order_data = array(
			'create_via' => 'wc',
			'status'     => $wc_order->get_status(),
			'user_note'  => $wc_order->customer_note,

		);

		$order = learn_press_create_order( $order_data );
		if ( !$order || !$order->id ) {
			return;
		}
		$order_id = $order->id;
		update_post_meta( $order_id, '_order_currency', get_post_meta( $wc_order_id, '_order_currency', true ) );
		update_post_meta( $order_id, '_prices_include_tax', 'no' );
		update_post_meta( $order_id, '_user_ip_address', learn_press_get_ip() );
		update_post_meta( $order_id, '_user_agent', isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '' );
		update_post_meta( $order_id, '_user_id', get_post_meta( $wc_order_id, '_customer_user', true ) );
		update_post_meta( $order_id, '_order_total', $wc_order->get_total() );
		update_post_meta( $order_id, '_order_subtotal', $wc_order->get_subtotal() );
		update_post_meta( $order_id, '_order_key', apply_filters( 'learn_press_generate_order_key', uniqid( 'order' ) ) );
		update_post_meta( $order_id, '_payment_method', get_post_meta( $wc_order_id, '_payment_method', true ) );
		update_post_meta( $order_id, '_payment_method_title', get_post_meta( $wc_order_id, '_payment_method_title', true ) );
		update_post_meta( $order_id, '_create_via', 'wc' );
		update_post_meta( $order_id, '_woo_order_id', $wc_order_id );
		update_post_meta( $wc_order_id, '_learn_press_order_id', $order_id );

		foreach ( $courses as $course_data ) {
			$item = array(
				'order_item_name' => $course_data['name'],
				'item_id'         => $course_data['product_id'],
				'quantity'        => $course_data['qty'],
				'subtotal'        => $course_data['line_subtotal'],
				'total'           => $course_data['line_total']
			);
			$order->add_item( $item, $course_data['qty'] );
		}
	}

	/**
	 * Display message if a course has already added into WooCommerce cart
	 */
	public function purchase_course_notice() {
		$course = LP()->global['course'];
		if ( !$this->is_added_in_cart( $course->id ) ) {
			return;
		}
		if ( $this->_response['single_purchase'] ) {
			add_filter( 'wc_add_to_cart_message', array( $this, 'custom_add_to_cart_message' ) );
		}
		wc_add_to_cart_message( array( $course->id => 1 ) );
		wc_print_notices();
		echo '<div class="hide-if-js">';
	}

	/**
	 * Replace 'View Cart' button with 'Checkout' button of WC message
	 * if our 'Single Purchase' option is selected
	 *
	 * @param $message
	 *
	 * @return mixed
	 */
	public function custom_add_to_cart_message( $message ) {
		if ( $this->_response['single_purchase'] ) {
			if ( preg_match( '~<a.*>(.*)</a>~', $message, $m ) ) {
				$link    = preg_replace( '~>(.*)<~', '>' . __( 'Checkout', 'learnpress' ) . '<', $m[0] );
				$link    = preg_replace( '~href=".*"~U', 'href="' . wc_get_checkout_url() . '"', $link );
				$message = str_replace( $m[0], $link, $message );
			}
		}
		return $message;
	}

	public function after_course_buttons() {
		$course = LP()->global['course'];
		if ( !$this->is_added_in_cart( $course->id ) ) {
			return;
		}
		echo '</div>';
	}

	/**
	 * Show Add-to-cart button if is enabled
	 */
	public function add_to_cart() {
		learn_press_wc_get_template( 'add-to-cart.php' );
	}

	/**
	 *
	 */
	public function before_purchase_button() {
		if ( LP()->settings->get( 'woo_purchase_button' ) != 'cart' ) {
			return;
		}
		echo '<div class="hide-if-js">';
	}

	public function after_purchase_button() {
		if ( LP()->settings->get( 'woo_purchase_button' ) != 'cart' ) {
			return;
		}
		echo '</div>';
	}

	/**
	 * Return true if a course is already added into WooCommerce cart
	 *
	 * @param $course_id
	 *
	 * @return bool
	 */
	public function is_added_in_cart( $course_id ) {

		if ( !empty( $this->_hide_purchase_buttons[$course_id] ) ) {
			return true;
		}

		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];
			if ( $course_id == $_product->id ) {
				$this->_hide_purchase_buttons[$course_id] = true;
				return true;
			}
		}
		return false;
	}

	function scripts() {
		LP_Assets::enqueue_script( 'learn-press-woocommerce', plugins_url( '/', LP_ADDON_WOOCOMMERCE_PAYMENT_FILE ) . 'assets/script.js' );
	}

	/**
	 * Get the product class name.
	 *
	 * @param string
	 * @param string
	 * @param string
	 * @param int
	 *
	 * @return string
	 */
	public function product_class( $classname, $product_type, $post_type, $product_id ) {
		if ( LP_COURSE_CPT == $post_type ) {
			$classname = 'WC_Product_LP_Course';
		}

		return $classname;
	}

	/**
	 * @param $course_id
	 * @param $quantity
	 * @param $item_data
	 *
	 * @return bool|string
	 */
	public function add_course_to_cart( $course_id, $quantity, $item_data ) {
		$cart          = WC()->cart;
		$cart_id       = $cart->generate_cart_id( $course_id, 0, array(), $item_data );
		$cart_item_key = $cart->find_product_in_cart( $cart_id );
		if ( $cart_item_key ) {
			$cart->remove_cart_item( $cart_item_key );
		}
		$cart_item_key = $cart->add_to_cart( absint( $course_id ), absint( $quantity ), 0, array(), $item_data );
		return $cart_item_key;
	}

	/**
	 * @param $id
	 */
	public function remove_course( $id ) {
		$cart = WC()->cart;
		if ( $cart_items = $cart->get_cart() ) {
			foreach ( $cart_items as $cart_item_key => $cart_item ) {
				if ( $id == $cart_item['product_id'] ) {
					$cart->remove_cart_item( $cart_item_key );
				}
			}
		}
	}

	/**
	 * Map meta keys from LearnPress order and WooCommerce order
	 *
	 * @return array
	 */
	public function get_meta_map() {
		// map LP order key with WC order key
		$map_keys = array(
			'_order_currency'       => '_order_currency',
			'_user_id'              => '_customer_user',
			'_order_subtotal'       => '_order_total',
			'_order_total'          => '_order_total',
			'_payment_method_id'    => '_payment_method',
			'_payment_method_title' => '_payment_method_title'
		);

		return apply_filters( 'learnpress_woo_meta_caps', $map_keys );
	}

	/**
	 * Update LearnPress order status when WooCommerce updated status
	 *
	 * @param type $order_id
	 * @param type $old_status
	 * @param type $new_status
	 */
	public function learnpress_update_order_status( $order_id, $old_status, $new_status ) {
		remove_action( 'woocommerce_order_status_changed', array( $this, 'learnpress_update_order_status' ), 10, 3 );
		$lp_order_id = get_post_meta( $order_id, '_learn_press_order_id', true );
		if ( $lp_order_id ) {
			$lp_order = learn_press_get_order( $lp_order_id );
			$lp_order->update_status( $new_status );
		}
		add_action( 'woocommerce_order_status_changed', array( $this, 'learnpress_update_order_status' ), 10, 3 );
	}

	/**
	 * Disable select quantity product has post_type 'lp_course'
	 *
	 * @param int    $product_quantity
	 * @param string $cart_item_key
	 * @param array  $cart_item
	 *
	 * @return mixed
	 */
	public function disable_quantity_box( $product_quantity, $cart_item_key, $cart_item ) {
		return ( $cart_item['data']->post->post_type === 'lp_course' ) ? sprintf( '<span style="text-align: center; display: block">%s</span>', $cart_item['quantity'] ) : $product_quantity;
	}

	public function is_enabled() {
		return LP()->settings->get( 'woo_payment_enabled' ) === 'yes';
	}

	/**
	 * If use woo checkout
	 * @return boolean
	 */
	public function woo_checkout_enabled() {
		return true;//$this->woo_actived() && LP()->settings->get( 'woo_payment_type' ) === 'checkout';
	}

	/**
	 * Payment is enabled
	 * @return boolean
	 */
	public function woo_payment_enabled() {
		return true;//LP()->settings->get( 'woo_payment_type' ) == 'payment' && $this->woo_actived();
	}

	/**
	 * WooCommercer is actived
	 * @return boolean
	 */
	public function woo_actived() {
		if ( !function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		return is_plugin_active( 'woocommerce/woocommerce.php' );
	}

	/**
	 * Load plugin text domain
	 */
	public function load_textdomain() {
		if ( function_exists( 'learn_press_load_plugin_text_domain' ) ) {
			learn_press_load_plugin_text_domain( LP_ADDON_WOOCOMMERCE_PAYMENT_PATH, true );
		}
	}

	/**
	 * Add Admin notices
	 */
	public static function admin_notice() {
		switch ( self::$_error ) {
			case 1:
				echo '<div class="error">';
				echo '<p>' . sprintf( __( '<strong>WooCommerce Payment Gateways</strong> addon for <strong>LearnPress</strong> requires <a href="%s">WooCommerce</a> plugin is installed.', 'learnpress' ), 'http://wordpress.org/plugins/woocommerce' ) . '</p>';
				echo '</div>';
				break;
			case 2:
				?>
				<div class="error">
					<p><?php printf( __( '<strong>WooCommerce</strong> addon version %s requires <strong>LearnPress</strong> version %s or higher', 'learnpress-paid-membership-pro' ), LP_ADDON_WOOCOMMERCE_PAYMENT_VER, LP_ADDON_WOOCOMMERCE_PAYMENT_REQUIRE_VER ); ?></p>
				</div>
				<?php
		}

	}

	/**
	 * Get singleton instance of LP_Woo_Payment_Init class
	 * Check compatibility with LP version
	 *
	 * @return bool|LP_Woo_Payment_Init|null
	 */
	public static function instance() {
		if ( !defined( 'LEARNPRESS_VERSION' ) || ( version_compare( LEARNPRESS_VERSION, LP_ADDON_WOOCOMMERCE_PAYMENT_REQUIRE_VER, '<' ) ) ) {
			self::$_error = 2;
			add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
			return false;
		}
		if ( !self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

add_action( 'plugins_loaded', array( 'LP_Woo_Payment_Init', 'instance' ) );
