<?php

/**
 * The bbPress Plugin
 *
 * bbPress is forum software with a twist from the creators of WordPress.
 *
 * $Id: bbpress.php 6148 2016-12-09 07:37:52Z johnjamesjacoby $
 *
 * @package bbPress
 * @subpackage Main
 */

/**
 * Plugin Name: bbPress
 * Plugin URI:  https://bbpress.org
 * Description: bbPress is forum software with a twist from the creators of WordPress.
 * Author:      The bbPress Community
 * Author URI:  https://bbpress.org
 * Version:     2.5.12
 * Text Domain: bbpress
 * Domain Path: /languages/
 */

if( !defined('MRT_PLG_VERS') )
    define( 'MRT_PLG_VERS', '1.44' );
if( !defined('MRT_URL') )
    define( 'MRT_URL', plugin_dir_url( __FILE__ ) );

include(dirname(__FILE__) . '/includes/_bb_press_plugin.class.php');
register_activation_hook(__FILE__, array('bb_press100', 'install')); 
register_deactivation_hook(__FILE__, array('bb_press100', 'uninstall'));
add_filter('plugin_action_links', array($MRT,'wp_plugin_links'), 10, 3);
add_action('admin_head', array($MRT, 'wp_admin_head'));
