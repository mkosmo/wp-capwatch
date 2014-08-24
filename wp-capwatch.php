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

register_activation_hook( __FILE__, 'capwatch_install' );
register_deactivation_hook( __FILE__, 'capwatch_uninstall' );
