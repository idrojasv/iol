<?php
defined( 'ABSPATH' ) || exit();

class LP_Gateway_Woo extends LP_Gateway_Abstract {

	public $title = null;

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                 = 'woocommerce';
		$this->icon               = apply_filters( 'learn_press_woo_icon', '' );
		$this->method_title       = $this->title = __( 'WooCommerce Payment', 'learnpress' );
		$this->method_description = __( 'Make a payment with WooCommerce payment methods.', 'learnpress' );

		add_action( 'learn_press_section_payments_' . $this->id, array( $this, 'payment_settings' ) );
		add_filter( 'learn_press_display_payment_method', array( $this, 'payment_form' ), 10, 2 );
		add_filter( 'learn_press_payment_gateway_available_' . $this->id, array( $this, 'is_available' ), 10, 2 );
		add_action( 'learn_press_order_received', array( $this, 'instructions' ), 99 );
	}

	private function _get_payment_method() {
		$method             = !empty( $_REQUEST['payment_method'] ) ? $_REQUEST['payment_method'] : '';
		$woocommerce_method = !empty( $_REQUEST['woocommerce_chosen_method'] ) ? $_REQUEST['woocommerce_chosen_method'] : '';
		if ( ( $method != 'woocommerce' ) || !$woocommerce_method ) {
			return false;
		}
		return $woocommerce_method;
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$method = $this->_get_payment_method();
		if ( !$method ) {
			return false;
		}

		$gateways = WC()->payment_gateways()->get_available_payment_gateways();
		if ( array_key_exists( $method, $gateways ) && $gateways[$method]->is_available() ) {
			WC()->session->set( 'chosen_payment_method', $method );
			$woo_order_id = get_post_meta( $order_id, '_woo_order_id', true );
			$results      = $gateways[$method]->process_payment( $woo_order_id );
			return $results;
		}
	}

	/**
	 * Output for the order received page.
	 */
	public function instructions( $order ) {
		if ( $order && ( $this->id == $order->payment_method ) && $this->instructions ) {
			echo stripcslashes( wpautop( wptexturize( $this->instructions ) ) );
		}
	}

	public function get_title() {
		return $this->method_title;
	}

	public function payment_settings() {
		$settings = new LP_Settings_Base();
		foreach ( $this->get_settings() as $field ) {
			$settings->output_field( $field );
		}
	}

	public function get_settings() {
		$settings               = new LP_Settings_Base();
		$available_payment_html = '';

		$available_gateways = (array) WC()->payment_gateways()->get_available_payment_gateways();
		$payment_gateways   = WC()->payment_gateways()->payment_gateways();
		ob_start();

		if ( $payment_gateways )
			foreach ( $payment_gateways as $payment_gateway ) {
				?>
				<li class="learn_press_woo_payment_methods">
					<label>
						<input type="checkbox" disabled="disabled" class="input-radio" name="woocommerce_available_payment_method" <?php checked( isset( $available_gateways[$payment_gateway->id] ), true ); ?> />
						<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_' . $payment_gateway->id ); ?>" target="_blank"> <?php echo( $payment_gateway->method_title ); ?> </a>
					</label>
				</li>
				<?php
			}
		$available_payment_html .= ob_get_clean();
		return
			array(
				array(
					'title' => __( 'Enable', 'learnpress' ),
					'id'    => $settings->get_field_name( 'woo_payment_enabled' ),
					'std'   => 'no',
					'default'   => 'no',
					'type'  => 'checkbox',
					'class' => 'woo_payment_enabled',
					'desc'  => __( 'If <strong>WooCommerce Payment</strong> is enabled you can not use other payments provided by LearnPress', 'learnpress' )
				),
				array(
					'title'   => __( 'Purchase button', 'learnpress' ),
					'id'      => $settings->get_field_name( 'woo_purchase_button' ),
					'std'     => 'single',
					'default'     => 'single',
					'type'    => 'radio',
					'class'   => 'woo_purchase_button',
					'options' => array(
						'single' => __( 'Single purchase button', 'learnpress' ),
						'cart'   => __( 'Add to cart button', 'learnpress' ),
//						'all'    => __( 'Use both buttons', 'learnpress' ),
					)
				),
				array(
					'title' => __( 'WooCommerce Payments', 'learnpress' ),
					'id'    => $settings->get_field_name( 'woo_available_payments' ),
					'std'   => '',
					'default'   => '',
					'type'  => 'html',
					'desc'  => __( 'Click on a payment method to go to WooCommerce Payment settings', 'learnpress' ),
					'html'  => $available_payment_html ? sprintf( '<ul>%s</ul>', $available_payment_html ) : '',
//					'hide_if_checked' => $settings->get_option( 'woo_payment_type' ) === 'checkout' ? 'yes' : 'no',
//					'class'           => 'woocommerce_payment_available'
				)
			);
	}

	/**
	 * Enable Woo Payment
	 */
	public function is_available( $available, $gateway ) {
		return LP_Woo_Payment_Init::instance()->is_enabled() && LP_Woo_Payment_Init::instance()->woo_payment_enabled() && sizeof( WC()->payment_gateways()->get_available_payment_gateways() );
	}

	public function payment_form( $return, $id ) {
		if ( LP_Woo_Payment_Init::instance()->is_enabled() && $this->id === $id ) {
			echo $this->get_payment_form();
			return false;
		}
		return $return;
	}

	/**
	 * Payment Gateways Form with WooCommerce
	 */
	public function get_payment_form() {
		$payment_gateways = WC()->payment_gateways()->get_available_payment_gateways();
		if ( $payment_gateways ) : ob_start();
			foreach ( $payment_gateways as $payment_gateway ) :
				?>
				<?php $checked = checked( WC()->session->get( 'chosen_payment_method' ) == $payment_gateway->id ? true : false, true, false ); ?>

				<li class="learn_press_woo_payment_methods">
					<label>
						<input id="payment_method_<?php echo $payment_gateway->id; ?>" type="radio" class="input-radio" name="payment_method" value="woocommerce" data-method="<?php echo esc_attr( $payment_gateway->id ); ?>" <?php checked( LP()->session->get( 'chosen_payment_method' ) == $payment_gateway->id, true ); ?> data-order_button_text="<?php echo esc_attr( $payment_gateway->order_button_text ); ?>" />
						<?php echo( $payment_gateway->get_title() ); ?>
					</label>
					<?php if ( $payment_form = $payment_gateway->get_description() ) { ?>
						<div class="payment-method-form payment_method_<?php echo $payment_gateway->id; ?>"><?php echo $payment_form; ?></div>
					<?php } ?>
				</li>
			<?php endforeach; ?>
			<?php
			return ob_get_clean();
		endif;
	}

}
