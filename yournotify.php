<?php

/*
Plugin Name: Yournotify
Plugin URI: https://wordpress.org/plugins/yournotify/
Description: Yournotify API integration that allows you to select which Yournotify list you want your visitors to subscribe to. Go to your widgets page for setup, use [yournotify] for shortcode anywhere in your page
Version: 1.1.0
Tested up to: 6.1
Author: Yournotify
Author URI: https://yournotify.com
License: GPL3
License URI: http://www.gnu.org/licenses/gpl.html
Text Domain: yournotify
Domain Path: /languages
*/

// Block direct access to the main plugin file.
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Main plugin class with initialization tasks.
 */
class YOURNOTIFY_Plugin
{
	/**
	 * Constructor for this class.
	 */
	public function __construct()
	{
		// Set plugin constants.
		add_action('plugins_loaded', 'YOURNOTIFY_Plugin::set_plugin_constants');

		// Register widgets.
		add_action('widgets_init', 'YOURNOTIFY_Plugin::register_widgets');

		// Enqueue plugin admin assets.
		add_action('admin_enqueue_scripts', 'YOURNOTIFY_Plugin::enqueue_admin_scripts');

		// Enqueue plugin frontend assets.
		add_action('wp_enqueue_scripts', 'YOURNOTIFY_Plugin::enqueue_frontend_scripts');

	}

	/**
	 * Set plugin constants.
	 *
	 * Path/URL to root of this plugin, with trailing slash and plugin version.
	 */
	public static function set_plugin_constants()
	{
		// Path/URL to root of this plugin, with trailing slash.
		if (!defined('YOURNOTIFY_PATH')) {
			define('YOURNOTIFY_PATH', plugin_dir_path(__FILE__));
		}

		if (!defined('YOURNOTIFY_URL')) {
			define('YOURNOTIFY_URL', plugin_dir_url(__FILE__));
		}

		// The plugin version.
		if (!defined('YOURNOTIFY_VERSION')) {
			define('YOURNOTIFY_VERSION', '1.1.0');
		}
	}

	/**
	 * Register the widgets in the 'widgets_init' action hook.
	 */
	public static function register_widgets()
	{
		if (!class_exists('Yournotify_Subscribe')) {
			require_once YOURNOTIFY_PATH . 'inc/yournotify-subscribe.php';
		}

		register_widget('Yournotify_Subscribe');
	}

	/**
	 * Enqueue admin scripts.
	 */
	public static function enqueue_admin_scripts()
	{
		// Enqueue admin JS.
		wp_enqueue_script('yournotify-admin-js', YOURNOTIFY_URL . 'assets/js/admin.js', array('jquery'), YOURNOTIFY_VERSION, true);

		// Provide the global variable to the 'yournotify-admin-js'.
		wp_localize_script('yournotify-admin-js', 'YournotifyAdminVars', array(
			'ajax_url'    => admin_url('admin-ajax.php'),
			'ajax_nonce'  => wp_create_nonce('yournotify-ajax-verification'),
			'text'        => array(
				'ajax_error'        => esc_html__('An error occurred while retrieving data via the AJAX request!', 'yournotify'),
				'no_api_key'        => esc_html__('Please input the Yournotify API key!', 'yournotify'),
				'incorrect_api_key' => esc_html__('This Yournotify API key is not formatted correctly, please copy the whole API key from the Yournotify dashboard!', 'yournotify'),
			)
		));
	}

	/**
	 * Enqueue frontend scripts.
	 */
	public static function enqueue_frontend_scripts()
	{
		if (!apply_filters('yournotify/disable_frontend_styles', false)) {
			wp_enqueue_style('yournotify-main-css', YOURNOTIFY_URL . 'assets/css/main.css', array(), YOURNOTIFY_VERSION);
		}

		// Enqueue frontend JS.
		wp_enqueue_script('yournotify-frontend-js', YOURNOTIFY_URL . 'assets/js/frontend.js', array('jquery'), YOURNOTIFY_VERSION, true);
	}

}

$ptmcw_plugin = new YOURNOTIFY_Plugin();
