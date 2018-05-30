<?php
/*
Plugin Name:  Drift Exclusive Product
Description:  Shows specified products to pre-selected users only
Version:      1.0
Author:       DriftWeb - Willon Nava
Author URI:   https://driftweb.com.br/
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
    

include_once( trailingslashit( plugin_dir_path(__FILE__) ) . 'classes/DftExProd.php' );

$dftExProd = new DftExProd();