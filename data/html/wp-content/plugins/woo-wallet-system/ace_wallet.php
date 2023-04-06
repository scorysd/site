<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://acewebx.com/contact-us/
 * @since             1.0.0
 * @package           Ace_wallet
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce Wallet System
 * Plugin URI:        http://acewebx.com/
 * Description:       This plugin help us for online payment we can add money woocommece payment method to the wallet. Woocomerce plugin is required for in this plugin.
 * Version:           1.0.0
 * Author:            AceWebx Team
 * Author URI:        http://acewebx.com/contact-us/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ace_wallet
 * Domain Path:       /languages
 */

// If this file is called directly, abort.



if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ace_wallet-activator.php
 */
function activate_ace_wallet() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ace_wallet-activator.php';
	Ace_wallet_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ace_wallet-deactivator.php
 */
function deactivate_ace_wallet() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ace_wallet-deactivator.php';
	Ace_wallet_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ace_wallet' );
register_deactivation_hook( __FILE__, 'deactivate_ace_wallet' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ace_wallet.php';


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
add_filter( 'woocommerce_payment_gateways', 'ace_add_gateway_class' );
function ace_add_gateway_class( $gateways ) {
	$gateways[] = 'WC_Ace_Gateway'; // your class name is here
	return $gateways;
}

	add_action( 'plugins_loaded', 'Ace_init_gateway_class' );
	function Ace_init_gateway_class() {
		class WC_Ace_Gateway extends WC_Payment_Gateway {
			public function __construct() {
	 
				$this->id = 'ace_custom'; // payment gateway plugin ID
				$this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
				$this->has_fields = true; // in case you need a custom credit card form
				$this->method_title = 'Ace Wallet';
				$this->method_description = 'Description of Ace payment gateway'; // will be displayed on the options page
			 
				// gateways can support subscriptions, refunds, saved payment methods,
				// but in this tutorial we begin with simple payments
				$this->supports = array(
					'products'
				);
			 
				// Method with all the options fields
				$this->init_form_fields();
			 
				// Load the settings.
				$this->init_settings();
				$this->title = $this->get_option( 'title' );
				$this->description = $this->get_option( 'description' );
				$this->enabled = $this->get_option( 'enabled' );
				$this->testmode = 'yes' === $this->get_option( 'testmode' );
				$this->private_key = $this->testmode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
				$this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );
			 
				// This action hook saves the settings
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			 
				// We need custom JavaScript to obtain a token
				//add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
			 
				// You can also register a webhook here
				// add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
			}

			public function init_form_fields(){
	 
				$this->form_fields = array(
					'enabled' => array(
						'title'       => 'Enable/Disable',
						'label'       => 'Ace wallet',
						'type'        => 'checkbox',
						'description' => '',
						'default'     => 'no'
					),
					'title' => array(
						'title'       => 'Title',
						'type'        => 'text',
						'description' => 'This controls the title which the user sees during checkout.',
						'default'     => 'Credit Card',
						'desc_tip'    => true,
					),
					'description' => array(
						'title'       => 'Description',
						'type'        => 'textarea',
						'description' => 'This controls the description which the user sees during checkout.',
						'default'     => 'Pay with your credit card via our super-cool payment gateway.',
					),
					'testmode' => array(
						'title'       => 'Test mode',
						'label'       => 'Enable Test Mode',
						'type'        => 'checkbox',
						'description' => 'Place the payment gateway in test mode using test API keys.',
						'default'     => 'yes',
						'desc_tip'    => true,
					),
					'test_publishable_key' => array(
						'title'       => 'Test Publishable Key',
						'type'        => 'text'
					),
					'test_private_key' => array(
						'title'       => 'Test Private Key',
						'type'        => 'password',
					),
					'publishable_key' => array(
						'title'       => 'Live Publishable Key',
						'type'        => 'text'
					),
					'private_key' => array(
						'title'       => 'Live Private Key',
						'type'        => 'password'
					)
				);
			}

			function validate_fields(){
	 			if( empty( $_POST[ 'billing_first_name' ]) ) {
					wc_add_notice(  'First name is required!', 'error' );
					return false;
				}
				return true;
			}

			public function process_payment( $order_id ) {
				global $woocommerce;
				global $wpdb;
				$current_user  =  get_current_user_id();
				$log_query = "SELECT * FROM ace_wallet_balance WHERE `user_id` = $current_user";
				$log_result = $wpdb->get_results( $log_query );
				$total_ammout = $log_result['0']->current_balance;
				// we need it to get any order detailes
				$order = wc_get_order( $order_id );
				$cart_total =  $order->get_total();
				if( $cart_total <= $total_ammout ){
					$total_wallet_balance  =  $total_ammout-$cart_total;	
					$last_update  =  date('Y-m-d H:i:s');
					$result = $wpdb->update('ace_wallet_balance', array( 'current_balance' => $total_wallet_balance,'last_update'=> $last_update ),array('user_id' => $current_user ) );
					$currency = get_option('woocommerce_currency');

					$wpdb->insert('ace_wallet_log' , array(
					    'product_id'   => '',
					    'order_id'     => $order_id,
					    'order_status' => 'sucess', // ... and so on
					    'user_id'      => $current_user, // ... and so on
					    'order_date'   => date("Y-m-d H:i:s"), // ... and so on
					    'price'        => $cart_total,
					    'currency'     => $currency,
					    'log'          => 'success product pruchase'
					));
					$woocommerce->cart->empty_cart();
					return array(
						'result' => 'success',
						'redirect' => $this->get_return_url( $order )
					);
				}else{
					$data = "please add more money in your wallet";
				}
				wc_add_notice(  'Connection - .'.$data, 'error' );
				return;
		 	} 
		}
	}

}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ace_wallet() {

	$plugin = new Ace_wallet();
	$plugin->run();

}
run_ace_wallet();