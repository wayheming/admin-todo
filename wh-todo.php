<?php
/**
 * Plugin Name: Admin TODO list.
 * Plugin URI: https://github.com/wayheming/wh-todo
 * Description: Admin TODO list plugin.
 * Author: Ernest Beginov
 * Version: 0.0.1
 * Author URI: https://github.com/wayheming
 */

defined( 'ABSPATH' ) || exit;

/**
 * Path to the plugin root directory.
 */
define( 'WH_TODO_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Url to the plugin root directory.
 */
define( 'WH_TODO_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin version.
 */
define( 'WH_TODO_VERSION', '0.0.1' );

require_once WH_TODO_PATH . 'class-autoload.php';

WHTodo\Includes\Plugin::get_instance();
