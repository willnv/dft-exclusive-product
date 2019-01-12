<?php
/*
Plugin Name:  Woo Private Product
Description:  Restrict products on your Woocommerce store to unique users.
Version:      0.1
Author:       Willon Nava
Text Domain: woo-private-product
*/

defined( 'ABSPATH' ) || exit;

# Globals
define( 'WPP_PATH', trailingslashit( plugin_dir_url(__FILE__) ) );
define( 'ASSETS_PATH', trailingslashit( plugin_dir_url(__FILE__) . 'assets' ) );

require_once 'classes/WPP_Main.php';
$wpp_main = new WPP_Main();