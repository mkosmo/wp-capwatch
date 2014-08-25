<?php
/**
 * Plugin Name: wp-CAPWATCH for Unit Websites
 * Plugin URI: http://www.inick.net
 * Description: This plugin uses CAPWATCH data to populate fields on a unit website with membership data.
 * Version: 0.1.0
 * Author: Nick McLarty
 * Author URI: https://www.inick.net
 * License: GPLv3
 */

defined('ABSPATH') or die("No script kiddies please!");

include_once plugin_dir_path( __FILE__ ) . 'options.php';
include_once plugin_dir_path( __FILE__ ) . 'install.php';
include_once plugin_dir_path( __FILE__ ) . 'shortcodes.php';
include_once plugin_dir_path( __FILE__ ) . 'upload.php';

register_activation_hook( __FILE__, 'capwatch_install' );
register_deactivation_hook( __FILE__, 'capwatch_uninstall' );

function wp_capwatch_enqueue_scripts() {
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_style( 'wp-capwatch', plugin_dir_url( __FILE__ ) . 'wp-capwatch.css', NULL, '0.1.0' );
}
add_action( 'admin_init', 'wp_capwatch_enqueue_scripts' );


function wp_capwatch_update_duty_position_order() {

	global $wpdb;

	update_option( 'wp_capwatch_duty_position_order', $_POST['order'] );
    
    die();

}
add_action( 'wp_ajax_update_duty_position_order', 'wp_capwatch_update_duty_position_order' );


function wp_capwatch_send_member_email() {

	global $wpdb;

	$table_prefix = $wpdb->prefix . 'capwatch_';

	$qry = $wpdb->get_results( "
	SELECT NameLast, NameFirst, Rank, Contact 
	FROM {$table_prefix}member mbr
	INNER JOIN {$table_prefix}member_contact cont
		ON mbr.CAPID = cont.CAPID 
	WHERE sha1( mbr.CAPID ) = '{$_POST['contact']}' 
		AND cont.Type = 'EMAIL' 
		AND cont.Priority = 'PRIMARY'
	" );

	$headers[] = "From: {$_POST['from']} <{$_POST['email']}>";
	$subject = get_bloginfo( 'name' ) . " Submission: {$_POST['subject']}";
	$body = "You have received a message via the '{$_POST['form_name']}' email form:\n\n{$_POST['message']}";
	wp_mail( $qry[0]->Contact, $subject, $body, $headers );

	die();

}
add_action( 'wp_ajax_send_member_email', 'wp_capwatch_send_member_email' );