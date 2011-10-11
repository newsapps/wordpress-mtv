<?php

namespace twentyeleven\views;
use mtv\wp\models\PostCollection,
    mtv\http\Http404,
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
    $args = array('name' => $request['name'],
            'posts_per_page' => 1,
            'post_status' => 'publish');
    $collection = PostCollection::filter($args);

    if (count($collection) != 1)
        throw new Http404;

    $post = $collection->models[0];

    $template_array = array(
        'post' => $post
    );

    shortcuts\display_template('single.html', $template_array);
}

function page( $request ) {}

function search( $request ) {}

function feed( $request ) {}

function date_archive( $request ) {}

function archive( $request ) {}
