<?php

namespace twentyeleven\views;
use mtv\wp\models\PostCollection,
    mtv\shortcuts;

function home( $request ) {
    shortcuts\set_query_flags('home');

    $page_num = ($request['page_num'])? $request['page_num']: 1;

    $args = array('post_type' => 'post',
                  'posts_per_page' => 10,
                  'order' => 'DESC',
                  'paged' => $page_num);
    $posts = PostCollection::filter($args);

    $max_pages = $posts->wp_query->max_num_pages;
    $more_posts = ($max_pages > 1 && $max_pages != $page_num)? true:false;

    $template_array = array(
        'page_num' => $page_num,
        'more_posts' => $more_posts,
        'posts' => $posts->models
    );

    shortcuts\display_template('index.html', $template_array);
}

function single( $request ) {
    
    print "weeeee";
    
}

function page( $request ) {}

function search( $request ) {}

function feed( $request ) {}

function date_archive( $request ) {}

function archive( $request ) {}
