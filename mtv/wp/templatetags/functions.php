<?php

namespace mtv\wp\templatetags\functions;
use mtv\shortcuts;
use Twig_Function_Function;

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
$twig->addFunction('get_avatar', new Twig_Function_Function('get_avatar', array('is_safe'=>array('html') )) );
$twig->addFunction('get_the_author_meta', new Twig_Function_Function('get_the_author_meta'));

// Post functions
$twig->addFunction('get_edit_post_link', new Twig_Function_Function('get_edit_post_link'));
$twig->addFunction('wpautop', new Twig_Function_Function('wpautop'));
$twig->addFunction('get_comments', new Twig_Function_Function('get_comments'));
$twig->addFunction('mysql2date', new Twig_Function_Function('mysql2date'));

// Comment form
$twig->addFunction('cancel_comment_reply_link', new Twig_Function_Function('cancel_comment_reply_link'));
$twig->addFunction('comment_form_title', new Twig_Function_Function('comment_form_title'));
$twig->addFunction('get_comment_reply_link', new Twig_Function_Function('get_comment_reply_link'));

// Thumbnails
$twig->addFunction('has_post_thumbnail', new Twig_Function_Function('has_post_thumbnail'));
$twig->addFunction('get_the_post_thumbnail', new Twig_Function_Function('get_the_post_thumbnail'));

// Galleries
$twig->addFunction('get_attachment_url', new Twig_Function_Function('wp_get_attachment_url'));
$twig->addFunction('get_attachment_thumb_url', new Twig_Function_Function('wp_get_attachment_thumb_url'));

// Post attachments

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

// Template functions
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
$twig->addFunction('this_year',
    new Twig_Function_Function('mtv\wp\templatetags\functions\this_year'));
$twig->addFunction('wp_nav_menu',
    new Twig_Function_Function('wp_nav_menu', array('is_safe'=>array('html'))));
$twig->addFunction('is_active_sidebar', new Twig_Function_Function('is_active_sidebar'));

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
