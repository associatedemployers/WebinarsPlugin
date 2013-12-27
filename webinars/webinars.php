<?php
/* 
Plugin Name: Webinars
Description: Webinars plugin for Associated Employers
Version: 1.1
Author: AE Development
*/

date_default_timezone_set('America/Denver');
wp_enqueue_script(array("common", "post"));
wp_enqueue_style('style2', WP_PLUGIN_URL . '/webinars/css/style.css');

register_activation_hook( __FILE__, 'webinars_install' );

//MODIFY: Add this to functions.php when using the Media Uploader.  wp_enqueue_media();
//wp_enqueue_script('jquery_validate2', WP_PLUGIN_URL . '/webinars/js/jquery.validate.min.js');
//wp_enqueue_script('additional-methods2', WP_PLUGIN_URL . '/webinars/js/additional-methods.min.js');
//wp_enqueue_script('google_maps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false');
//wp_enqueue_script('webgrain2_javascript', WP_PLUGIN_URL . '/webinars/js/js.js');



/*
<---- DISABLE THIS FOR FORM POST TESTING ---->
If a form has been submitted with the PROCESS $_GET var.
If YES then redirect the user to the list page to prevent form resubmission.
<---- DISABLE THIS FOR FORM POST TESTING ---->
*/
if(isset($_GET['page'])) { $pg_page = $_GET['page']; } else { $pg_page = ''; }
if(isset($_GET['process'])) { $pg_process = $_GET['process']; } else { $pg_process = ''; }
if(isset($pg_page) && isset($pg_process)) { 
	if($pg_page == 'wg-handle2' && $pg_process == true) { header('Location: ' . get_settings('siteurl') . '/wp-admin/admin.php?page=wg-handle2'); }
	if($pg_page == 'wg-second2' && $pg_process == true) { header('Location: ' . get_settings('siteurl') . '/wp-admin/admin.php?page=wg-second2'); }
}



/***********************************************/
/************WEBGRAIN OBJECT************/
/***********************************************/
class Webgrain2 {
	public $main_handle = 'wg-handle2'; // Main handle
	public $main_tabname = 'Webinars'; // Main menu name
	public $main_tabicon = 'ae.png'; // Main menu icon

	public $first_new_button_title = 'New Webinar'; // Name of 1st "New Button"
	public $first_menu_title = 'Webinars'; // Name of 1st sub tab
	public $first_menu_table = 'wp_webinars'; // Table of 1st sub tab
	
	public $second_handle = 'wg-second2'; // Handle of 2nd sub tab
	public $second_new_button_title = 'New Order'; // Name of 2nd "New Button"
	public $second_menu_title = 'Orders'; // Name of 2nd sub tab
	public $second_menu_table = 'wp_webinar_code'; // Table of 2nd sub tab
}

$webgrain2 = new Webgrain2();

add_action('admin_menu', 'wg_menu2');
function wg_menu2() {
	global $webgrain2;
	add_menu_page($webgrain2->main_tabname, $webgrain2->main_tabname, 'administrator', $webgrain2->main_handle, 'init_first_item2', get_settings('siteurl') . '/wp-content/plugins/webinars/' . $webgrain2->main_tabicon, 5);
	add_submenu_page('wg-handle2', $webgrain2->first_menu_title, $webgrain2->first_menu_title, 'administrator',($webgrain2->main_handle), 'init_first_item2');
	add_submenu_page('wg-handle2', $webgrain2->second_menu_title, $webgrain2->second_menu_title, 'administrator', ($webgrain2->second_handle), 'init_second_item2');
}


/********************************************/
/************CREATE DATABASE TABLE************/
/********************************************/
function webinars_install () {
	global $wpdb;

	$webinars_table = $wpdb->prefix . "webinars"; 
	$code_table = $wpdb->prefix . "webinar_code";
	$purchase_table = $wpdb->prefix . "webinar_purchase";

	$sql = "CREATE TABLE $webinars_table (
	  id int(11) NOT NULL AUTO_INCREMENT,
	  title varchar(255) DEFAULT '' NOT NULL,
	  description text DEFAULT NULL,
	  url varchar(255) DEFAULT '' NOT NULL,
	  duration varchar(255) DEFAULT '90' NOT NULL,
	  price int(11) DEFAULT '70' NOT NULL,
	  member_price int(11) DEFAULT '45' NOT NULL,
	  unlimited tinyint(1) DEFAULT 0 NOT NULL,
	  urlkey varchar(255) DEFAULT '' NOT NULL,
	  UNIQUE KEY id (id)
	);
	CREATE TABLE $code_table (
	id int(11) NOT NULL AUTO_INCREMENT,
	urlkey int(11) NOT NULL,
	transaction_id int(11) NOT NULL,
	expiration DATE DEFAULT NULL,
	UNIQUE KEY id (id)
	);";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}



/********************************************/
/************FIRST PAGE ITEM************/
/********************************************/
require_once('itemFirst2.class.php');
require_once('itemFirst2.list.class.php');
require_once('itemFirst2.list.table.class.php');
require_once('itemFirst2.modify.class.php');
function init_first_item2() { $item_first_Admin2 = new ItemFirstList2(); }
function get_item_first_admin_url2($submit) { global $webgrain2; if($submit) { return 'admin.php?page=' . $webgrain2->main_handle . '&process=true'; } else { return 'admin.php?page=' . $webgrain2->main_handle; } }
function get_item_first_admin_page2() { global $webgrain2; return $webgrain2->main_handle; }



/***********************************************/
/************SECOND PAGE ITEM************/
/***********************************************/
require_once('itemSecond2.class.php');
require_once('itemSecond2.list.class.php');
require_once('itemSecond2.list.table.class.php');
require_once('itemSecond2.modify.class.php');
function init_second_item2() { $item_second_Admin2 = new ItemSecondList2(); }
function get_item_second_admin_url2($submit) { global $webgrain2; if($submit) { return 'admin.php?page=' . $webgrain2->second_handle . '&process=true'; } else { return 'admin.php?page=' . $webgrain2->second_handle; } }
function get_item_second_admin_page2() { global $webgrain2; return $webgrain2->second_handle; }


?>