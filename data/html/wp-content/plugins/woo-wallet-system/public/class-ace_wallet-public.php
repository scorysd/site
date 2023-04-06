<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://acewebx.com/contact-us/
 * @since      1.0.0
 *
 * @package    Ace_wallet
 * @subpackage Ace_wallet/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ace_wallet
 * @subpackage Ace_wallet/public
 * @author     Acewebx <webbninja2@gmail.com>
 */
class Ace_wallet_Public {

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

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ace_wallet_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ace_wallet_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ace_wallet-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$checkout_page = site_url('checkout'); 
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ace_wallet_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ace_wallet_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ace_wallet-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'2', plugin_dir_url( __FILE__ ) . 'js/ace_wallet_sweet_alert-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name );
		wp_localize_script( $this->plugin_name, 'ajax_object' , array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'ajax_object2' , array( 'checkout_url' =>  $checkout_page ), $this->version, true );


	}

	public function ace_my_custom_endpoints() {
		@session_start();
		add_action('template_redirect','redirect_visitor');
		function redirect_visitor(){
	    	add_rewrite_endpoint( 'ace-wallet', EP_ROOT | EP_PAGES );
	    	flush_rewrite_rules();
		    @session_start();
			global $wpdb;			
			global $woocommerce;			
		    if ( is_page( 'checkout' ) || is_checkout() ) {
		       //echo "true";
		    }else{
		    	if( !empty( $_SESSION['ace_wallet_session'] ) ){
					unset($_SESSION['ace_wallet_session']);
					$_SESSION['ace_expired'] = 'session_expired';
					$ace_product_id = get_option('ace_product_id');
					$_product       =  wc_get_product( $ace_product_id );
			    	$price          = $_product->get_price();
					$currency = get_option('woocommerce_currency');
					$current_user  =  get_current_user_id();
					$wpdb->insert('ace_wallet_log' , array(
					    'product_id'   => $ace_product_id,
					    'order_id'     => '',
					    'order_status' => 'declined', // ... and so on
					    'user_id'      => $current_user, // ... and so on
					    'order_date'   => date("Y-m-d H:i:s"), // ... and so on
					    'price'        => $price,
					    'currency'     => $currency,
					    'log'          => 'requsted declined'
					));
					wp_delete_post( $ace_product_id );	
					$items = $woocommerce->cart->get_cart();
		    		if( !empty( $items ) ){
						$woocommerce->cart->empty_cart();
						if( !empty( $_SESSION['previous_cartdata'] ) ){
							foreach ( $_SESSION['previous_cartdata'] as $key => $value ) {
								$woocommerce->cart->add_to_cart( $value['product_id'], $value['quantity']  );
							}
						}
					}

				}
		    }
		}

		
	}

	public function ace_my_custom_query_vars( $vars ) {
	    $vars[] = 'ace-wallet';
	   return $vars;
	}

	public function ace_myaccount_addmenu( $items ) {
		$items['ace-wallet'] = 'Ace Wallet'; 
		return $items;
	}

	public function ace_wallet_callback() {
		global $wpdb;
		@session_start();
		if( !empty( $_SESSION['ace_added_money'] ) ){ 
?>
	<script type="text/javascript">
		swal({
		  title: "Money has been added",
		  text: "Thanks for adding Money for Ace wallet",
		  icon: "success",
		});
	</script>
<?php
	}
		unset( $_SESSION['ace_added_money'] );		
		$current_user  =  get_current_user_id();
		$log_query = "SELECT * FROM ace_wallet_balance WHERE `user_id` = $current_user";
		$log_result = $wpdb->get_results( $log_query );		
		$total_ammout = $log_result['0']->current_balance;
		if( empty( $total_ammout) ){
			$total_ammout = '0';
		}
?>
	<div class="ace_wallet_money">
		<input type="text" name="add_money" id="input_add_mony">
		<button id="ace_add_mony">Add Money</button>
		<h3>Your Total Balance</h3>
		<p class="wallet_balance"><?php echo get_option('woocommerce_currency').' ( '.$total_ammout.' )'; ?></p>
	</div>
	<style>
		p.wallet_balance {
		    padding: 13px;
		    font-weight: 600;
		}
		.ace_wallet_money h3 {
		    width: 50%;
		    float: left;
		}
		.ace_wallet_money {
		    line-height: 3;
		}
	</style>
<?php		   
	}

	function ace_disable_billing_shipping( $checkout ){
		@session_start();
		if( !empty($_SESSION['ace_wallet_session'] ) ){
			$checkout['billing']  =  array();
			$checkout['shipping'] =  array();
		?>
		<style>
			div#customer_details {
			    display: none;
			}
		</style>
		<?php
		}
		return $checkout;
	}

	public function ace_auto_product_create(){
		if( sanitize_text_field($_POST) ){
			@session_start();
			global $wpdb;
			global $woocommerce;
			$mony_value = sanitize_text_field( $_POST['mony_value'] );
			$post_id = wp_insert_post( array(
                'post_status' => 'publish',
                'post_type' => 'product',
                'post_title' => 'Add Money',
                'post_content' => 'Add amount'
            ) );
            update_option('ace_product_id', $post_id );
            update_option('ace_product_test', 'deleted' );
            update_post_meta( $post_id , '_regular_price', $mony_value );
			update_post_meta( $post_id , '_price', $mony_value );
	        $_SESSION['ace_wallet_session'] = 'ace_wallet_session'; 
    		$currency = get_option('woocommerce_currency');
            $current_user  =  get_current_user_id();
			$wpdb->insert('ace_wallet_log' , array(
			    'product_id'   => $post_id,
			    'order_id'     => '',
			    'order_status' => 'pendding', // ... and so on
			    'user_id'      => $current_user, // ... and so on
			    'order_date'   => date("Y-m-d H:i:s"), // ... and so on
			    'price'        => $mony_value,
			    'currency'     => $currency,
			    'log'          => 'money requsted'
			));

    		$items = $woocommerce->cart->get_cart();
    		if( !empty($items ) ){
    			foreach( $items as $item => $values ) { 
		           	$id       =  $values['product_id'];
		           	$quantity =  $values['quantity'];
		           	$data[] = array('quantity' => $quantity, 'product_id' => $id );
		        }
	        	$_SESSION['previous_cartdata'] = $data;
			}
	        if( !empty( $_SESSION['previous_cartdata'] ) ){
	        	$woocommerce->cart->empty_cart();
	    	}
	    	$woocommerce->cart->add_to_cart( $post_id, 1 );

	    }
	}

	function ace_fillter_payment_getway( $available_gateways ) {

		@session_start();
		global $woocommerce;
			if( !empty($_SESSION['ace_wallet_session'] ) ){
				unset( $available_gateways['cod'] );
				unset( $available_gateways['cheque'] );
				unset( $available_gateways['ace_custom'] );
			}
	    return $available_gateways;
	}

	function ace_wallet_thankupage( $order_id ) {
		session_start();
		global $woocommerce;
		global $wpdb;
		    // Getting an instance of the order object
	    if( !empty( $order_id )   && !empty( $_SESSION['ace_wallet_session'] ) ){
		    $order = wc_get_order( $order_id );
		    $payment_method = $order->get_payment_method();
		    if( $order->is_paid() ){
		        $paid = 'paid';
		    }else{
		        $paid = '';
		    }
		    $order_status  =  $order->get_status();
			$items         = $order->get_items();
		    foreach ( $items as $item ) {
			    $product_id[] = $item['product_id'];
			}
			$product_id    =  $product_id['0'];
			$current_user  =  get_current_user_id();
			$_product      =  wc_get_product( $product_id );
			//$query = "SELECT * FROM ace_wallet_log WHERE `product_id` = $product_id AND `order_by` = $current_user";
			if( !empty( $_product ) ){
				$_product = wc_get_product( $product_id );
			   	$currency = get_option('woocommerce_currency');
			   	$query = "SELECT * FROM ace_wallet_balance WHERE `product_id` = $product_id AND `user_id` = $current_user";
			    $result = $wpdb->get_results( $query );
			    $price = $_product->get_price();
			    if( empty( $result['0'] ) ){

					$wpdb->insert('ace_wallet_log' , array(
					    'product_id'   => $product_id,
					    'order_id'     => $order_id,
					    'order_status' => $order_status, // ... and so on
					    'user_id'      => $current_user, // ... and so on
					    'order_date'   => date("Y-m-d H:i:s"), // ... and so on
					    'price'        => $price,
					    'currency'     => $currency,
					    'log'          => 'success money added'
					));
					$wpdb->insert('ace_wallet_log' , array(
					    'product_id'   => $product_id,
					    'order_id'     => $order_id,
					    'order_status' => $order_status, // ... and so on
					    'user_id'      => $current_user, // ... and so on
					    'order_date'   => date("Y-m-d H:i:s"), // ... and so on
					    'price'        => $price,
					    'currency'     => $currency,
					    'log'          => 'payment method is ( "'.$payment_method.'")' 
					));
					$log_query = "SELECT * FROM ace_wallet_balance WHERE `user_id` = $current_user";
				    $log_result = $wpdb->get_results( $log_query );
				    $oldammout = $log_result['0']->current_balance;
				    if( empty( $log_result['0'] ) ){
						$wpdb->insert('ace_wallet_balance' , array(
						    'user_id'         => $current_user,
						    'current_balance' => $price,
						    'last_update'     => date("Y-m-d H:i:s"), // ... and so on
						    'status'          => $order_status
						));
				    }else{
				    	$last_update  =  date('Y-m-d H:i:s');
				    	$current_ammount = $oldammout+$price;	    	
				    	$result = $wpdb->update('ace_wallet_balance', 
				    		array(
				    			'current_balance' => $current_ammount,
				    			'last_update' => $last_update 
				    		), 
				    		array(
				    			'user_id' => $current_user
				    		)
				    	);
				    }
			   	}
			    wp_delete_post( $product_id );
			    update_option('ace_product_id','');	    	
			    update_option('ace_product_test','');	    	
			}
	    
		    if( !empty( $_SESSION['previous_cartdata'] ) ){
		    	foreach( $_SESSION['previous_cartdata'] as $previous_cartdata_key => $previous_cartdata_value ) {
					$pre_product_id  = $previous_cartdata_value['product_id'];
					$pre_product_q   = $previous_cartdata_value['quantity'];
					$woocommerce->cart->add_to_cart( $pre_product_id, $pre_product_q );
			    }
		   }
	   		unset( $_SESSION['previous_cartdata'] );
	   		unset( $_SESSION['ace_wallet_session'] );
	   		$url = get_site_url().'/my-account/ace-wallet/';
	   		$_SESSION['ace_added_money'] = 'true';
	   		wp_redirect($url);
		}
	    
	}

	function ace_footer_load_hook() {
		if( !empty( $_SESSION['ace_expired'] ) ){
		 	$ace_expired = $_SESSION['ace_expired']; 
		}
		echo '<input type="hidden" id="ace_session_expired" value="'.$ace_expired.'">';
		unset($_SESSION['ace_expired']); 
	}



}
