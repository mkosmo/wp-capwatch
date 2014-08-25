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

function update_duty_position_order() {

	global $wpdb;

	update_option( 'wp_capwatch_duty_position_order', $_POST['order'] );
    
    die();

}
add_action('wp_ajax_update_duty_position_order', 'update_duty_position_order' );