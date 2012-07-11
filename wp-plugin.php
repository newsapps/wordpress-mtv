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
    else if ( ! empty( $GLOBALS['ajax_url_patterns'] ) )
        global $ajax_url_patterns;
    else
        throw new Exception("Can't find a urls.php file in your theme");

    // Since we're doing ajax, we've already loaded $registered_apps in
    // our init callback and only need to resolve the url
    \mtv\http\resolve(get_default( $_REQUEST, 'path', ''), $ajax_url_patterns);

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

    load_plugin_textdomain('mtv', false, basename(__DIR__) . '/locale/');

    /**
     * Is our chosen theme an MTV theme?
     * If not, we don't want to hijack rewrite rules and template selection
     **/
    if ( ! file_exists( get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'urls.php' ) &&
         ! file_exists( get_template_directory() . DIRECTORY_SEPARATOR . 'urls.php' ) )
        return; // nope

    /**
     * *_rewrite_rules
     * Extra "permastruct" rules are run through this filter. Extra "permastruct" rules are added
     * to $wp_rewrite->extra_rules_top and include rewrite rules for tags, categories, and post_formats.
     * This only happens while generate_rewrite_rules runs. $wp_rewrite->extra_rules_top looks like
     * it's typically used for 3rd party stuff, just not always.
     *
     * Anyway, it messes up our generate_rewrite_rules hook, so we have to prevent that stuff from
     * getting added to $wp_rewrite->extra_rules_top
     **/
    global $wp_rewrite;
    foreach ( array_keys( $wp_rewrite->extra_permastructs ) as $permastructname )
        add_filter( $permastructname . '_rewrite_rules', function() { return array(); } );

    /**
     * generate_rewrite_rules
     * Run immediately after WordPress generates it's rewrite rules. We'll replace all
     * the rules with one of ours. Our rule will route all requests into our url resolver.
     * We set this to run first so plugins can still add their own rules.
     *
     * P.S. $wp_rewrite is a object, so gets passed in by reference
     **/
    add_action( 'generate_rewrite_rules', function( $wp_rewrite ) {
        # setup our hijack rules
        $mtv_rules = array();
        $mtv_rules['$'] = 'index.php?path'; // Fix WP 3.3 home rewrite rule
        $mtv_rules['(.*)'] = 'index.php?path=$matches[1]';

        # We're feeling adventurous, override wordpress' processing with ours
        # If we just relace $wp_rewrite->rules, we lose stuff added by other plugins
        # we just want to replace WordPress's default builtin stuff
        $wp_rewrite->rules = array_merge($wp_rewrite->extra_rules_top, $mtv_rules, $wp_rewrite->extra_rules);

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

    /**
     * If we're doing ajax, load MTV here in the init callback so
     * that it will be available throughout the request
     **/
    if ( is_admin() && defined('DOING_AJAX') && DOING_AJAX == true ) {
        if ( !empty( $GLOBALS['apps'] ) )
            mtv\load( $GLOBALS['apps'] );
    }

}, 999);
