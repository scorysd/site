<?php

/**
 * Fired during plugin activation
 *
 * @link       http://acewebx.com/contact-us/
 * @since      1.0.0
 *
 * @package    Ace_wallet
 * @subpackage Ace_wallet/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ace_wallet
 * @subpackage Ace_wallet/includes
 * @author     Acewebx <webbninja2@gmail.com>
 */

class Ace_wallet_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		flush_rewrite_rules();
		 global $wpdb;
	    	$charset_collate = $wpdb->get_charset_collate();
		    $sql = "CREATE TABLE `ace_wallet_balance` (
		      id int NOT NULL AUTO_INCREMENT,
		      user_id varchar(220) NOT NULL,
		      current_balance varchar(220) NOT NULL,
		      last_update varchar(220) NOT NULL,
		      status varchar(220) NOT NULL,
		      PRIMARY KEY  (id)
		    ) $charset_collate;";

		    $sql2 = "CREATE TABLE `ace_wallet_log` (
		      id int NOT NULL AUTO_INCREMENT,
		      product_id varchar(220) NOT NULL,
		      order_id varchar(220) NOT NULL,
		      order_status varchar(220) NOT NULL,
		      user_id varchar(220) NOT NULL,
		      order_date varchar(220) NOT NULL,
		      price varchar(220) NOT NULL,
		      currency varchar(220) NOT NULL,
		      log varchar(220) NOT NULL,
		      PRIMARY KEY  (id)
		    ) $charset_collate;";

		    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		    dbDelta($sql);
		    dbDelta($sql2);
		    $success = empty($wpdb->last_error);
		    return $success;
		}else{
			 die("Woocommerce plugin is reuired for this plugin");
		}
	}

}

