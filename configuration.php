<?php
/*
Plugin Name: Simple Credits
Author: Edgar Kussberg
Version: 3.5
Description: Plugin that enables credits payment system in woocommerce shop.
License: GPLv3
*/

include_once dirname(__FILE__) . '/lang.php';
include_once dirname(__FILE__) . '/functions.inc.php';
include_once dirname(__FILE__) . '/admin.inc.php';
include_once dirname(__FILE__) . '/classes/creditsTable.php';

// Translation object for the plugin
global $translate;
$translate = new WoocreditTranslate();

// JAL DB version
global $jal_db_version;
$jal_db_version = "1.1";

// Hook to remove category
add_action('pre_get_posts', 'custom_pre_get_posts_query' );

add_action('wp_head','addScripts');
add_action('woocommerce_order_status_completed', 'check_thankyou');

// Add custom product fields to woocommerce
add_action( 'woocommerce_product_options_general_product_data', 'woo_add_custom_general_fields' );
add_action( 'woocommerce_process_product_meta', 'woo_add_custom_general_fields_save' );

// Add custom shortcodes
add_shortcode('creditwoocommerce', 'creditwoocommerceshortcode' );
add_shortcode('usercreditwoocommerce', 'userCreditBalance' );
add_shortcode('user_bought_products', 'show_bought_products' );
add_shortcode('buy_credits_button', 'creditsbuybutton' );

// Add menu and plugin link for settings
function credits_plugin_settings_link($links) {
	$settings_link = '<a href="'.get_site_url().'/wp-admin/edit.php?post_type=product&page=woocommerceCredit">Settings</a>';
	array_unshift($links, $settings_link);
	return $links;
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'credits_plugin_settings_link' );

function wooCommercemenu() {
	global $translate;
	$menu = $translate->wooTranslate('user credit',get_bloginfo('language'));
	add_submenu_page('edit.php?post_type=product', $menu, $menu, 'edit_posts', 'woocommerceCredit', 'woocommerceCredit');
}

add_action('admin_menu', 'wooCommercemenu');

// Remove some woocommerce standard actions
function remove_loop_button(){
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

	add_action('woocommerce_single_product_summary', 'single_product_buy_button', 30);
	add_action('woocommerce_in_cart_product_title', 'product_title', 5);
}
add_action('init','remove_loop_button');

// Forbid to show the credit bundles in the store
function forbid_credits_page() {
	global $post;
	$terms = wp_get_post_terms( $post->ID, 'product_cat' );
	$categories = array();
	foreach ( $terms as $term )
		$categories[] = $term->slug;
	if(is_product_category('credit') || (is_product() && has_term( 'credit', 'product_cat' ))) {
		header('Location: '.get_permalink( woocommerce_get_page_id( 'shop' )).'');
	}
}
add_action('wp', 'forbid_credits_page');

// Database setup when installing and deleting the plugin
function jal_install() {
	global $wpdb;
	global $jal_db_version;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$table_name = $wpdb->prefix."woocredit_orders";
	$sql = "CREATE TABLE $table_name (
	user_id int(11) NOT NULL,
	order_id int(11) NOT NULL,
	`date` timestamp NULL DEFAULT CURRENT_TIMESTAMP);";
	dbDelta( $sql );

	$table_name = $wpdb->prefix."woocredit_users";
	$sql = "CREATE TABLE $table_name (
	user_id int(11) NOT NULL,
	credit DECIMAL(10,2) UNSIGNED NOT NULL,
	UNIQUE KEY `index` (`user_id`));";
	dbDelta( $sql );

	$table_name = $wpdb->prefix."woocredit_changes";
	$sql = "CREATE TABLE $table_name (
	user_id int(11) NOT NULL,
	amount int(11) NOT NULL,
	`date` timestamp NULL DEFAULT CURRENT_TIMESTAMP);";
	dbDelta( $sql );

	$table_name = $wpdb->prefix."woocredit_products";
	$sql = "CREATE TABLE $table_name (
	user_id int(11) NOT NULL,
	product_id int(11) NOT NULL,
	price DECIMAL(10,2) UNSIGNED NOT NULL,
	`date` timestamp NULL DEFAULT CURRENT_TIMESTAMP);";
	dbDelta( $sql );

	$table_name = $wpdb->prefix."terms";

	$exist = $wpdb->get_var("SELECT term_id FROM $table_name WHERE slug = 'credit';");

	if(!$exist) {
		$sql = "INSERT INTO $table_name (name, slug, term_group)
		VALUES ('Credit','credit','0');";
		dbDelta( $sql );

		$table_name = $wpdb->prefix . "term_taxonomy";
		$sql = "INSERT INTO $table_name (term_id, taxonomy)
		VALUES ('".$wpdb->insert_id."','product_cat');";
		dbDelta( $sql );
	}

	add_option( "jal_db_version", $jal_db_version );
}

function jal_uninstall() {
	global $wpdb;

	$table_name = $wpdb->prefix . "woocredit_orders";
	$sql = "DROP TABLE IF EXISTS $table_name;";
	$wpdb->query($sql);

	$table_name = $wpdb->prefix . "woocredit_users";
	$sql = "DROP TABLE IF EXISTS $table_name;";
	$wpdb->query($sql);

	$table_name = $wpdb->prefix . "woocredit_changes";
	$sql = "DROP TABLE IF EXISTS $table_name;";
	$wpdb->query($sql);

	$table_name = $wpdb->prefix . "woocredit_products";
	$sql = "DROP TABLE IF EXISTS $table_name;";
	$wpdb->query($sql);
}

register_activation_hook( __FILE__, 'jal_install' );
register_uninstall_hook( __FILE__, 'jal_uninstall');
?>
