<?php

namespace mtv\wp\templatetags\functions;
use mtv\shortcuts;
use Twig_Function_Function;

global $twig;

// Debug
$twig->addFunction('print_r', new Twig_Function_Function('print_r'));

// General, helpful WordPress stuff
$twig->addFunction('apply_filters', new Twig_Function_Function('apply_filters'));
$twig->addFunction('esc_attr', new Twig_Function_Function('esc_attr'));
$twig->addFunction('esc_url', new Twig_Function_Function('esc_url'));
$twig->addFunction('get_option', new Twig_Function_Function('get_option'));
$twig->addFunction('do_action', new Twig_Function_Function('do_action'));
$twig->addFunction('__', new Twig_Function_Function('__'));

// Request flags, conditionals
$twig->addFunction('is_preview', new Twig_Function_Function('is_preview'));
$twig->addFunction('is_single', new Twig_Function_Function('is_single'));
$twig->addFunction('is_page', new Twig_Function_Function('is_page'));
$twig->addFunction('is_archive', new Twig_Function_Function('is_archive'));
$twig->addFunction('is_date', new Twig_Function_Function('is_date'));
$twig->addFunction('is_year', new Twig_Function_Function('is_year'));
$twig->addFunction('is_month', new Twig_Function_Function('is_month'));
$twig->addFunction('is_day', new Twig_Function_Function('is_day'));
$twig->addFunction('is_time', new Twig_Function_Function('is_time'));
$twig->addFunction('is_author', new Twig_Function_Function('is_author'));
$twig->addFunction('is_category', new Twig_Function_Function('is_category'));
$twig->addFunction('is_tag', new Twig_Function_Function('is_tag'));
$twig->addFunction('is_tax', new Twig_Function_Function('is_tax'));
$twig->addFunction('is_search', new Twig_Function_Function('is_search'));
$twig->addFunction('is_feed', new Twig_Function_Function('is_feed'));
$twig->addFunction('is_comment_feed', new Twig_Function_Function('is_comment_feed'));
$twig->addFunction('is_trackback', new Twig_Function_Function('is_trackback'));
$twig->addFunction('is_home', new Twig_Function_Function('is_home'));
$twig->addFunction('is_404', new Twig_Function_Function('is_404'));
$twig->addFunction('is_comments_popup', new Twig_Function_Function('is_comments_popup'));
$twig->addFunction('is_paged', new Twig_Function_Function('is_paged'));
$twig->addFunction('is_admin', new Twig_Function_Function('is_admin'));
$twig->addFunction('is_attachment', new Twig_Function_Function('is_attachment'));
$twig->addFunction('is_singular', new Twig_Function_Function('is_singular'));
$twig->addFunction('is_robots', new Twig_Function_Function('is_robots'));
$twig->addFunction('is_posts_page', new Twig_Function_Function('is_posts_page'));
$twig->addFunction('is_post_type_archive', new Twig_Function_Function('is_post_type_archive'));

// Login/register
$twig->addFunction('wp_login_url', new Twig_Function_Function('wp_login_url'));
$twig->addFunction('is_user_logged_in', new Twig_Function_Function('is_user_logged_in'));

// Author functions
$twig->addFunction('the_author', new Twig_Function_Function('get_the_author'));
$twig->addFunction('the_author_meta', new Twig_Function_Function('get_the_author_meta'));
$twig->addFunction('get_avatar', new Twig_Function_Function('get_avatar', array('is_safe'=>array('html') )) );

// Post functions
$twig->addFunction('get_permalink', new Twig_Function_Function('get_permalink'));
$twig->addFunction('get_post_type', new Twig_Function_Function('get_post_type'));
$twig->addFunction('the_ID', new Twig_Function_Function('get_the_ID'));
$twig->addFunction('the_title', new Twig_Function_Function('get_the_title'));
$twig->addFunction('the_content', new Twig_Function_Function('get_the_content'));
$twig->addFunction('the_excerpt', new Twig_Function_Function('get_the_excerpt'));
$twig->addFunction('the_date', new Twig_Function_Function('get_the_date'));
$twig->addFunction('the_permalink', new Twig_Function_Function('get_permalink'));
$twig->addFunction('the_post_format', new Twig_Function_Function('get_post_format'));
$twig->addFunction('the_post_meta', new Twig_Function_Function('mtv\wp\templatetags\functions\get_the_post_meta'));
$twig->addFunction('wpautop', new Twig_Function_Function('wpautop'));
$twig->addFunction('comment_form', new Twig_Function_Function('mtv\wp\templatetags\functions\comment_form'));
$twig->addFunction('wp_list_comments', new Twig_Function_Function('wp_list_comments'));
$twig->addFunction('get_comments', new Twig_Function_Function('get_comments'));
$twig->addFunction('mysql2date', new Twig_Function_Function('mysql2date'));

$twig->addFunction('formatted_date', new Twig_Function_Function('mtv\wp\templatetags\functions\formatted_date'));
$twig->addFunction('relative_time', new Twig_Function_Function('mtv\wp\templatetags\functions\relative_time'));

// Comment form
$twig->addFunction('comments_open', new Twig_Function_Function('comments_open'));
$twig->addFunction('site_url', new Twig_Function_Function('site_url'));
$twig->addFunction('comment_id_fields', new Twig_Function_Function('comment_id_fields'));
$twig->addFunction('cancel_comment_reply_link', new Twig_Function_Function('cancel_comment_reply_link'));
$twig->addFunction('comment_form_title', new Twig_Function_Function('comment_form_title'));
$twig->addFunction('get_comment_reply_link', new Twig_Function_Function('get_comment_reply_link'));
$twig->addFunction('comment_author', new Twig_Function_Function('comment_author'));

// Thumbnails
$twig->addFunction('has_post_thumbnail', new Twig_Function_Function('has_post_thumbnail'));
$twig->addFunction('has_post_thumbnail_caption', new Twig_Function_Function('mtv\wp\templatetags\functions\has_post_thumbnail_caption'));
$twig->addFunction('get_the_post_thumbnail', new Twig_Function_Function('get_the_post_thumbnail'));
$twig->addFunction('get_the_post_thumbnail_caption', new Twig_Function_Function('mtv\wp\templatetags\functions\get_the_post_thumbnail_caption'));
$twig->addFunction('get_thumbnail_image_src', new Twig_Function_Function('mtv\wp\templatetags\functions\get_thumbnail_image_src'));
$twig->addFunction('get_featured_image_src', new Twig_Function_Function('mtv\wp\templatetags\functions\get_featured_image_src'));

// Galleries
$twig->addFunction('get_attachment_url', new Twig_Function_Function('wp_get_attachment_url'));
$twig->addFunction('get_attachment_thumb_url', new Twig_Function_Function('wp_get_attachment_thumb_url'));

// Post attachments
$twig->addFunction('has_featured_image', new Twig_Function_Function('chicagonow\shortcuts\has_featured_image'));
$twig->addFunction('get_featured_image', new Twig_Function_Function('chicagonow\shortcuts\get_featured_image'));
$twig->addFunction('wp_get_attachment_image_src', new Twig_Function_Function('wp_get_attachment_image_src'));

// Theme functions
// CSS
$twig->addFunction('get_stylesheet', new Twig_Function_Function('get_stylesheet'));
$twig->addFunction('get_stylesheet_directory', new Twig_Function_Function('get_stylesheet_directory'));
$twig->addFunction('get_stylesheet_directory_uri', new Twig_Function_Function('get_stylesheet_directory_uri'));
$twig->addFunction('get_stylesheet_uri', new Twig_Function_Function('get_stylesheet_uri'));
$twig->addFunction('get_template_directory_uri', new Twig_Function_Function('get_template_directory_uri'));
$twig->addFunction('get_theme_root', new Twig_Function_Function('get_theme_root'));

// Blog functions
$twig->addFunction('get_blog_details', new Twig_Function_Function('get_blog_details'));
$twig->addFunction('get_current_blog_id', new Twig_Function_Function('get_current_blog_id'));
$twig->addFunction('get_site_url', new Twig_Function_Function('get_site_url'));
$twig->addFunction('get_home_url', new Twig_Function_Function('get_home_url'));
$twig->addFunction('get_bloginfo', new Twig_Function_Function('get_bloginfo'));
$twig->addFunction('get_header_image', new Twig_Function_Function('get_header_image'));
$twig->addFunction('get_header_textcolor', new Twig_Function_Function('get_header_textcolor'));

// Template functions
$twig->addFunction('wp_title', new Twig_Function_Function('wp_title'));
$twig->addFunction('wp_get_archives', new Twig_Function_Function('wp_get_archives'));
$twig->addFunction('wp_register', new Twig_Function_Function('wp_register'));
$twig->addFunction('wp_loginout', new Twig_Function_Function('wp_loginout'));
$twig->addFunction('wp_meta', new Twig_Function_Function('wp_meta'));
$twig->addFunction('wp_head', 
    new Twig_Function_Function('mtv\wp\templatetags\functions\get_wp_head', array('is_safe'=>array('html')))
);
$twig->addFunction('wp_footer', 
    new Twig_Function_Function('mtv\wp\templatetags\functions\get_wp_footer', array('is_safe'=>array('html')))
);
$twig->addFunction('body_class', 
    new Twig_Function_Function('mtv\wp\templatetags\functions\get_body_class', array('is_safe'=>array('html')))
);
$twig->addFunction('language_attributes', 
    new Twig_Function_Function('mtv\wp\templatetags\functions\get_language_attributes', array('is_safe'=>array('html')))
);
$twig->addFunction('get_header', 
    new Twig_Function_Function('mtv\wp\templatetags\functions\get_header', array('is_safe'=>array('html')))
);
$twig->addFunction('get_footer', 
    new Twig_Function_Function('mtv\wp\templatetags\functions\get_footer', array('is_safe'=>array('html')))
);
$twig->addFunction('dynamic_sidebar', 
    new Twig_Function_Function('mtv\wp\templatetags\functions\dynamic_sidebar', array('is_safe'=>array('html')))
);
$twig->addFunction('sidebar_is_populated', 
    new Twig_Function_Function('mtv\wp\templatetags\functions\sidebar_is_populated', array('is_safe'=>array('html')))
);
$twig->addFunction('is_active_sidebar', new Twig_Function_Function('is_active_sidebar'));

$twig->addFunction('this_year', new Twig_Function_Function('mtv\wp\templatetags\functions\this_year'));

// Nav menus
$twig->addFunction('wp_nav_menu', new Twig_Function_Function('wp_nav_menu', array('is_safe'=>array('html'))));
$twig->addFunction('has_nav_menu', 
    new Twig_Function_Function('mtv\wp\templatetags\functions\has_nav_menu', array('is_safe'=>array('html'))));

function has_nav_menu() {
    if (is_nav_menu('Blog Navigation')) {
        $nav = wp_get_nav_menu_object('Blog Navigation');
        $items = wp_get_nav_menu_items($nav->name);

        if (count($items) > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function has_post_thumbnail_caption($post_id) {
    $attachment = get_post( get_post_thumbnail_id($post_id) );
    return (bool)$attachment->post_excerpt;
}

function get_the_post_thumbnail_caption($post_id) {
    $attachment = get_post( get_post_thumbnail_id($post_id) );
    return $attachment->post_excerpt;
}

function get_thumbnail_image_src($post_id = null, $size = 'post-thumbnail') {
    $post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
    $post_thumbnail_id = get_post_thumbnail_id( $post_id );
    $size = apply_filters( 'post_thumbnail_size', $size );

    $imgsrc = wp_get_attachment_image_src( $post_thumbnail_id, $size, false );
    $imgsrc = $imgsrc[0]; // Only want uri of the image

    return $imgsrc;
}

function get_featured_image_src($post_id = null, $size = null) {
    $post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
    $post_thumbnail_id = get_post_thumbnail_id( $post_id );

    if (empty($size)) {
        $meta = wp_get_attachment_metadata( $post_thumbnail_id );
        if ((int) $meta['width'] > 500)
            $size = array(624,9999);
        else
            $size = array((int) $meta['width'], (int) $meta['height']);
    }

    $imgsrc = wp_get_attachment_image_src( $post_thumbnail_id, $size, false );
    $imgsrc = $imgsrc[0]; // Only want uri of the image

    return $imgsrc;
}

function get_the_post_meta($key) {
    global $post;
    return get_post_meta($post->ID, $key, true);
}

function get_wp_head() {
    ob_start();
    wp_head();
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}

function get_wp_footer() {
    ob_start();
    wp_footer();
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}

function get_body_class() {
    ob_start();
    body_class();
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}

function get_language_attributes() {
    ob_start();
    language_attributes();
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}

function get_header() {
    ob_start();
    do_action( 'get_header', null );
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}

function get_footer() {
    ob_start();
    do_action( 'get_footer', null );
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}

function get_sidebar() {
    ob_start();
    do_action( 'get_sidebar', null );
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}

function dynamic_sidebar($id) {
    ob_start();
    \dynamic_sidebar($id);
    ob_end_flush();
}

function sidebar_is_populated($id) {
    ob_start();
    $ret = \dynamic_sidebar($id);
    ob_end_clean();
    return $ret;
}

function this_year() {
    return date('Y', shortcuts\current_time("timestamp"));
}

function formatted_date($post_date) {
    $ts = strtotime($post_date);

    $now = shortcuts\current_time("timestamp");
    $today_start = strtotime(date('D, d M Y 00:00:00 O', $now));
    $week_start = strtotime('last monday');

    if ($ts > $today_start) {
        $d = 'today at ' . date('g:i a', $ts);
    } elseif ($ts > $week_start) {
        $d = date('l \a\t g:i a', $ts);
    } else {
        $d = date('F j, Y \a\t g:i a', $ts);
    }
    return $d;
}

function relative_time($datetime) {
    // Make sure we localize the timestamp
    $datetime = mysql2date('Y-m-d H:i:s', $datetime, $translate=true);

    // Pass it a 'YYYY-MM-DD HH:MM:SS' and it will return something
    // like "Two hours ago", "Last week", etc.

    // http://maniacalrage.net/projects/relative/

    if (!preg_match("/\d\d\d\d-\d\d-\d\d \d\d\:\d\d\:\d\d/", $datetime)) {
        return '';
    }

    $in_seconds = strtotime($datetime);
    $now = shortcuts\current_time("timestamp");

    # The clock on the DB server could be fast
    if ( $in_seconds >= $now )
        return 'just now';

    $diff  =  $now - $in_seconds;
    $months   =  floor($diff/2419200);
    $diff     -= $months * 2419200;
    $weeks    =  floor($diff/604800);
    $diff     -= $weeks*604800;
    $days     =  floor($diff/86400);
    $diff     -= $days * 86400;
    $hours    =  floor($diff/3600);
    $diff     -= $hours * 3600;
    $minutes = floor($diff/60);
    $diff    -= $minutes * 60;
    $seconds = $diff;


    if ($months > 0) {
        // Over a month old, just show the actual date.
        $date = substr($datetime, 0, 10);
        return formatted_date($date);

    } else {
        $relative_date = '';
        if ($weeks > 0) {
            // Weeks and days
            $relative_date .= ($relative_date?', ':'').$weeks.' week'.($weeks>1?'s':'');
            $relative_date .= $days>0?($relative_date?', ':'').$days.' day'.($days>1?'s':''):'';
        } elseif ($days > 0) {
            // days and hours
            $relative_date .= ($relative_date?', ':'').$days.' day'.($days>1?'s':'');
            $relative_date .= $hours>0?($relative_date?', ':'').$hours.' hour'.($hours>1?'s':''):'';
        } elseif ($hours > 0) {
            // hours and minutes
            $relative_date .= ($relative_date?', ':'').$hours.' hour'.($hours>1?'s':'');
            $relative_date .= $minutes>0?($relative_date?', ':'').$minutes.' minute'.($minutes>1?'s':''):'';
        } elseif ($minutes > 0) {
            // minutes only
            $relative_date .= ($relative_date?', ':'').$minutes.' minute'.($minutes>1?'s':'');
        } else {
            // seconds only
            $relative_date .= ($relative_date?', ':'').$seconds.' second'.($seconds>1?'s':'');
        }
    }

    // Return relative date and add proper verbiage
    return $relative_date.' ago';

}
