<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 *
 * @package    Woo_Checkout_For_Digital_Goods
 * @subpackage Woo_Checkout_For_Digital_Goods/public
 */


class Woo_Checkout_For_Digital_Goods_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-checkout-for-digital-goods-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-checkout-for-digital-goods-public.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Function for remove checkout fields.
	 */
	public function custom_override_checkout_fields( $fields ) {

		global $woocommerce;
		$woo_checkout_field_array = get_option('wcdg_checkout_fields');
		
		// return the regular billing fields if we need shipping fields
		if ( $woocommerce->cart->needs_shipping() ) {
			return $fields;
		}
		
		$temp_product = array();
	
		foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];
	
			$get_virtual = get_post_meta($_product->id,'_virtual',true);
			
			if (isset($get_virtual) && $get_virtual == 'no') {
				$temp_product[] = $_product->id;
			}
				
		}
		
		if (count($temp_product) > 0) {
			return $fields;
		} else {
			
		if (isset($woo_checkout_field_array) && !empty($woo_checkout_field_array)) {
			$woo_checkout_field_array_serilize = maybe_unserialize($woo_checkout_field_array);
			foreach ($woo_checkout_field_array_serilize as $key=>$values) {
				if ($values == 'order_comments') {
					unset($fields['order']['order_comments']);
				}else {
					unset($fields['billing'][$values]);
				}
			}
		}else {
		
				unset($fields['billing']['billing_company']);
			    unset($fields['billing']['billing_address_1']);
			    unset($fields['billing']['billing_address_2']);
			    unset($fields['billing']['billing_city']);
			    unset($fields['billing']['billing_postcode']);
			    unset($fields['billing']['billing_country']);
			    unset($fields['billing']['billing_state']);
			    unset($fields['billing']['billing_phone']);
			    unset($fields['order']['order_comments']);
			    unset($fields['billing']['billing_address_2']);
			    unset($fields['billing']['billing_postcode']);
			    unset($fields['billing']['billing_company']);
			    unset($fields['billing']['billing_city']);
			    return $fields;
			  
		}
		}
		  return $fields;
	}
	
	
	/**
	 * BN code added
	 */
	
	function paypal_bn_code_filter_woo_checkout_field ($paypal_args) {
		$paypal_args['bn'] = 'Multidots_SP';
		return $paypal_args;
	}

}
