<?php

/*
 * This script load WordPress for use in cli
 */

if ( !defined('STDIN') ) 
  die("Please run this script from the command line."); 

$settings_file = 'settings.json';

global $settings;
$settings_path = dirname( __DIR__ ) .'/'. $settings_file;
$settings = json_decode( file_get_contents( $settings_path ), true );

define('DOING_AJAX', true);
define('WP_USE_THEMES', false);

global $_SERVER;
$_SERVER["HTTP_HOST"] = $settings['hostname'];
$_SERVER["SERVER_NAME"] = $settings['hostname'];
$_SERVER["REQUEST_URI"] = "/cli";
$_SERVER["REQUEST_METHOD"] = "GET";

$abspath = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
define( 'ABSPATH', $abspath . '/' );

require_once(ABSPATH.'wp-config.php');

if ( ! defined('WP_SITEURL') )
    define( 'WP_SITEURL', "http://".$settings['hostname']);

