<?php
/**
 * @package MTV
 * @version 1.0
 */
/*
Plugin Name: Wordpress MTV
Plugin URI: http://blog.apps.chicagotribune.com
Description: A simple framework for building custom apps and features
    on top of wordpress
Author: Ryan Mark, Ryan Nagle
Version: 1.0
*/

include 'mtv.php';

use mtv\shortcuts;

/**
 * Initialize the MTV framework
 **/

# MTV comes with an App for WordPressy stuff
mtv\register_app( 'wp',
    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wp' );

/**
 * Register javascript libraries
 **/
$js_runtime_settings = array(
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
    'current_blog_id' => get_current_blog_id(),
    'DEBUG' => false
);

if ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) {
    wp_register_script('mtv-all',
        plugins_url('/mtv/devjs/mtv.js'),
        array('jquery'),
        MTV_VERSION);

    $js_runtime_settings['DEBUG'] = true;

} else {
    wp_register_script('mtv-all',
        plugins_url('/mtv/mtv.min.js'),
        array('jquery'),
        MTV_VERSION);
}

wp_localize_script('mtv-all', 'WordPress', $js_runtime_settings);
unset($js_runtime_settings);

/**
 * Use the URL resolver for ajax calls
 **/
$handle_ajax = function() {
    // get the url patterns for the current theme
    if ( file_exists( get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'urls.php' ) )
        include get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'urls.php';
    else if ( file_exists( get_template_directory() . DIRECTORY_SEPARATOR . 'urls.php' ) )
        include get_template_directory() . DIRECTORY_SEPARATOR . 'urls.php';
    else
        throw Exception("Can't find a urls.php file in your theme");

    // whatever is in the $apps global is what we're going to load
    global $apps;

    // run MTV
    mtv\run( array(
        'url' => get_default( $_REQUEST, 'path', '' ),
        'url_patterns' => $ajax_url_patterns,
        'apps' => $apps ) );

    // That's all folks
    exit;
};
add_action('wp_ajax_mtv', $handle_ajax);
add_action('wp_ajax_nopriv_mtv', $handle_ajax);

/**
 * Request handling
 **/
add_filter( 'query_vars', function( $vars ) {
    // Where we bless query vars so WP doesn't throw them out
    $vars[] = 'path';
    return $vars;
} );

add_action( 'init', function() {

    /**
     * Is our chosen theme an MTV theme?
     * If not, we don't want to hijack rewrite rules and template selection
     **/
    if ( ! file_exists( get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'urls.php' ) &&
         ! file_exists( get_template_directory() . DIRECTORY_SEPARATOR . 'urls.php' ) )
        return; // nope

    /**
     * generate_rewrite_rules
     * Run immediately after WordPress generates it's rewrite rules. We'll replace all
     * the rules with one of ours. Our rule will route all requests into our url resolver.
     * We set this to run first so plugins can still add their own rules.
     **/
    add_action( 'generate_rewrite_rules', function( $wp_rewrite ) {
        // setup our hijack rule
        $newrules = array();
        $newrules['(.*)'] = 'index.php?path=$matches[1]';

        // We're feeling adventurous, override wordpress' processing with ours
        $wp_rewrite->rules = $newrules;
    }, 1, 1);

    /**
     * redirect_canonical
     * Correct opinionated wordpress redirects
     **/
    add_filter('redirect_canonical', function($redirect_url, $requested_url) {
        # Don't add trailing slashes to files.

        # if $redirect_url ends in '/' then
        if ( substr($redirect_url, -1) ) {
            $ext = pathinfo($requested_url,PATHINFO_EXTENSION);
        #   if $requested_url ends in '.xml' or '.html' etc. then
            if ( in_array($ext, array('xml', 'html')) )
                return false;
        }

    }, 10, 2);

    /**
     * Reroute the rest of the application through our url resolver
     **/
    add_action( 'template_redirect', function() {
        // Where we figure out which view to use on the front end
        global $wp_query;

        // check for the path queryvar. That means we're on!
        if ( !($wp_query->query_vars['path'] === NULL) ) { // will work for the root path
            // reset wp_query's is_whatever flags and posts
            shortcuts\reset_wp_query();

            // get the url patterns for the current theme
            if ( file_exists( get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'urls.php' ) )
                include get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'urls.php';
            else if ( file_exists( get_template_directory() . DIRECTORY_SEPARATOR . 'urls.php' ) )
                include get_template_directory() . DIRECTORY_SEPARATOR . 'urls.php';
            else
                throw new Exception("Can't find a urls.php file in your theme");

            // whatever is in the $apps global is what we're going to load
            global $apps;

            // run MTV
            mtv\run( array(
                'url' => $wp_query->query_vars['path'],
                'url_patterns' => $url_patterns,
                'apps' => $apps ) );

            // That's all folks
            exit;
        }
    });
});
