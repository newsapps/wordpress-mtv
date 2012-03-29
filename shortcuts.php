<?php
/**
 * @package MTV
 * @version 1.0
 */

namespace mtv\shortcuts;

use mtv\http\Http404;
use mtv\http\Http500;
use mtv\http\AjaxHttp500;
use mtv\models\wp\PostCollection;
use Exception;

/**
 * Render and display a template
 **/
function display_template( $template_name, $context=array() ) {
    global $twig;

    if ( !is_object($twig) ) throw new Exception('Twig template engine not available');

    $template = $twig->loadTemplate( $template_name );
    $template->display( $context );
}

/**
 * Render and return a template
 **/
function render( $template_name, $context=array() ) {
    global $twig;

    if ( !is_object($twig) ) throw new Exception('Twig template engine not available');

    $template = $twig->loadTemplate( $template_name );
    return $template->render( $context );
}

/**
 * Encode and display JSON
 **/
function display_json($data) {
    header('Cache-Control: no-cache, must-revalidate');
    header('Content-type: application/json');
    print( json_encode( $data ) );
}

/**
 * Check current user for a capability, throw an exception if not allowed
 **/
function require_capability($cap, $kwargs=null) {
    if ( ! empty( $kwargs['blogid'] ) )
        $answer = current_user_can_for_blog( $kwargs['blogid'], $cap );
    else
        $answer = current_user_can( $cap );

    if ( ! $answer ) {
       if ( ! empty($kwargs['ajax']) ) throw new AjaxHttp500("You can't do that");
       else throw new Exception("You can't do that");
    }

    return true;
}

/**
 * Configure the flags in wp_query
 *
 *  List of available WP query flags
 *  $wp_query->is_single
 *  $wp_query->is_page
 *  $wp_query->is_archive
 *  $wp_query->is_date
 *  $wp_query->is_year
 *  $wp_query->is_month
 *  $wp_query->is_day
 *  $wp_query->is_time
 *  $wp_query->is_author
 *  $wp_query->is_category
 *  $wp_query->is_tag
 *  $wp_query->is_tax
 *  $wp_query->is_search
 *  $wp_query->is_home
 *  $wp_query->is_paged
 *  $wp_query->is_admin
 *  $wp_query->is_attachment
 *  $wp_query->is_singular
 *  $wp_query->is_404

 *  No case implemented for these yet:
 *  $wp_query->is_feed
 *  $wp_query->is_comment_feed
 *  $wp_query->is_trackback
 *  $wp_query->is_comments_popup
 *  $wp_query->is_robots
 *  $wp_query->is_posts_page
 *  $wp_query->is_post_type_archive
 *  $wp_query->is_preview
 * 
 **/
function set_query_flags($views=null) {
    global $wp_query;

    if ($wp_query->max_num_pages > 1)
        $wp_query->is_paged = true;

    if (!is_array($views))
        $views = array($views);

    if ($wp_query->query_vars['preview'] == true)
        $wp_query->is_preview = true;

    foreach ($views as $view) {
        switch ($view) {
            case '404':
                $wp_query->is_404 = true;
                break;
            case 'home':
                $wp_query->is_home = true;
                break;
            case 'search':
                $wp_query->is_search = true;
                break;
            case 'date':
                $wp_query->is_date = true;
                $wp_query->is_archive = true;
                break;
            case 'year':
                $wp_query->is_year = true;
                $wp_query->is_archive = true;
                break;
            case 'month':
                $wp_query->is_month = true;
                $wp_query->is_archive = true;
                break;
            case 'day':
                $wp_query->is_day = true;
                $wp_query->is_archive = true;
                break;
            case 'time':
                $wp_query->is_time = true;
                $wp_query->is_archive = true;
                break;
            case 'author':
                $wp_query->is_author = true;
                $wp_query->is_archive = true;
                break;
            case 'category':
                $wp_query->is_category = true;
                $wp_query->is_archive = true;
                break;
            case 'tag':
                $wp_query->is_tag = true;
                $wp_query->is_archive = true;
                break;
            case 'tax':
                $wp_query->is_tax = true;
                $wp_query->is_archive = true;
                break;
            case 'archive':
                $wp_query->is_archive = true;
                break;
            case 'single':
                $wp_query->is_single = true;
                $wp_query->is_singular = true;
                break;
            case 'attachment':
                $wp_query->is_attachment = true;
                break;
            case 'page':
                $wp_query->is_page = true;
                break;
            default:
                // stuff like our directory and pitch 
                // page will end up here.
                $wp_query->is_page = true;
        }
    }
}

/**
 * Reset all of the $wp_query->is_blah flags and clear $wp_query->posts. Preps for
 * our url resolver
 **/
function reset_wp_query() {
    global $wp_query;

    // Reset query flags
    $wp_query->is_single = false;
    $wp_query->is_preview = false;
    $wp_query->is_page = false;
    $wp_query->is_archive = false;
    $wp_query->is_date = false;
    $wp_query->is_year = false;
    $wp_query->is_month = false;
    $wp_query->is_day = false;
    $wp_query->is_time = false;
    $wp_query->is_author = false;
    $wp_query->is_category = false;
    $wp_query->is_tag = false;
    $wp_query->is_tax = false;
    $wp_query->is_search = false;
    $wp_query->is_feed = false;
    $wp_query->is_comment_feed = false;
    $wp_query->is_trackback = false;
    $wp_query->is_home = false;
    $wp_query->is_404 = false;
    $wp_query->is_comments_popup = false;
    $wp_query->is_paged = false;
    $wp_query->is_admin = false;
    $wp_query->is_attachment = false;
    $wp_query->is_singular = false;
    $wp_query->is_robots = false;
    $wp_query->is_posts_page = false;
    $wp_query->is_post_type_archive = false;

    if (!empty($wp_query->posts))
        unset($wp_query->posts);

}

/**
 * Return a Mysql date string or unix timestamp for the current local time
 **/
function current_time( $type, $gmt = 0 ) {
    $t =  ( $gmt ) ? gmdate( 'Y-m-d H:i:s' ) : gmdate( 'Y-m-d H:i:s', ( time() + ( get_option( 'gmt_offset' ) * 3600 ) ) );
    switch ( $type ) {
    case 'mysql':
        return $t;
        break;
    case 'timestamp':
        return strtotime($t);
        break;
    }
}
