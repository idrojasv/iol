<?php
/*
Plugin Name: Thim Portfolio
Plugin URI: https://thimpress.com
Description: A plugin that allows you to show off your portfolio.
Author: ThimPress
Version: 1.3
Author URI: https://thimpress.com
Text Domain: tp-portfolio
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

if (!defined('THIM_PORTFOLIO_VERSION')) {
	define('THIM_PORTFOLIO_VERSION', '1.0');
}

if (!defined('CORE_PLUGIN_URL')) {
	define('CORE_PLUGIN_URL', untrailingslashit(plugins_url('/', __FILE__)));
}

if (!defined('CORE_PLUGIN_PATH')) {
	define('CORE_PLUGIN_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
}

require_once 'tp-portfolio.php';
require_once CORE_PLUGIN_PATH . "/lib/thim-functions.php";
