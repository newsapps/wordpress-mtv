<?php
namespace mtv\filters;

// Twig filters go here

// A not-so-useful example:
// Author name filter functions
function author_displayname($id) {
    return get_the_author_meta('display_name', $id);
}
$twig->addFilter('author_displayname',
            new \Twig_Filter_Function('chicagonow\filters\author_displayname'));

function pluralize($array) {
    if (count($array) != 1) return "s";
    else return "";
}
$twig->addFilter('pluralize',
            new \Twig_Filter_Function('chicagonow\filters\pluralize'));

function collection_contains($collection, $entity) {
    return (bool)$collection->contains( $entity );
}
$twig->addFilter('collection_contains',
            new \Twig_Filter_Function('chicagonow\filters\collection_contains'));

function bloginfo($blog_entity_or_id, $key) {
    if ( is_numeric($blog_entity_or_id) )
        $id = $blog_entity_or_id;
    else $id = $blog_entity_or_id->id;

    \switch_to_blog($id);
    $ret = \get_bloginfo($key);
    \restore_current_blog();

    return $ret;
}
$twig->addFilter('bloginfo',
            new \Twig_Filter_Function('chicagonow\filters\bloginfo'));

function linebreaks($content) {
    return wpautop($content, false);
}
$twig->addFilter('linebreaks',
            new \Twig_Filter_Function('chicagonow\filters\linebreaks', array('is_safe'=>array('html') )));

function meta($post_or_id, $key) {
    if ( is_numeric($post_or_id) )
        $id = $post_or_id;
    else $id = $post_or_id->ID;

    return get_post_meta($id, $key, true);
}
$twig->addFilter('meta',
            new \Twig_Filter_Function('chicagonow\filters\meta'));
            
function url_decode($str) {
    $str = urldecode($str);
    return $str;
}
$twig->addFilter('url_decode',
            new \Twig_Filter_Function('chicagonow\filters\url_decode'));

