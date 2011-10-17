<?php
/* 
* Our business code. The view layer is the switching yard 
* for all the requests your site will receive. 
*/

/* 
* The paths to the functions served in urls.php are based 
* on the namespace, defined below. 
*/
namespace simple_mtv\views;

/* 
* The PostCollection model grabs data from The Loop and 
* makes it available for MTV. With a couple twists, 
* PostCollection really is just a thin wrapper around 
* WordPress's WP_Query object.
*
* Our template loader function is in “shortcuts.”
*/
use mtv\wp\models\PostCollection,
    mtv\shortcuts;

/*
* These functions assemble data and serve it to the assigned template.
* They're called in urls.php.
*/

# $request passes the data captured by our URL groupings to the function.
function home( $request ) {
	/* 
	* We reset conditional tags when we break The Loop, 
	* so here we reset the query flag. You can set more than one query 
	* flag by using an array (see date_archive for an example).
	*/
    shortcuts\set_query_flags('home');

    # Get which page of posts we're on.
    $page_num = ($request['page_num'])? $request['page_num']: 1;

    # Define an array of arguments to pass to PostCollection so we only get posts we want.
    $args = array('post_type' => 'post',
                  'posts_per_page' => 5,
                  'order' => 'DESC',
                  'paged' => $page_num);
    $posts = PostCollection::filter($args);

    # Figure out how many pages of posts match our request
    $max_pages = $posts->wp_query->max_num_pages;
    # See whether we're at the last page of responses or not
    $more_posts = ($max_pages > 1 && $max_pages != $page_num)? true:false;

    /*
    * Create an array of the data we’re interested in exposing in the template.
    * Templates only have access to variables that you provide in $template_array. 
    */	
    $template_array = array(
        'page_num' => $page_num,
        'more_posts' => $more_posts,
        'posts' => $posts->models
    );

    # Load a template file and pass the array to it.
    shortcuts\display_template('index.html', $template_array);
}

# Serve a single post.
function single( $request ) {
	shortcuts\set_query_flags('single');
	
	$post_name = $request['name'];

	$post_id = (int) $request['post_id'];
	
	$page_id = 'single';
	
	$args = array('name' => $post_name,
        'posts_per_page' => 1,
        'post_status' => 'publish');
	$collection = PostCollection::filter($args);
	
	if ( count($collection) != 1 )
        throw new Http404;

    $p = $collection->models[0]->attributes;
	
	$cat_obj = get_the_category($post_id);
    $tag_obj = get_the_tags($post_id);

	
	$template_array = array(
        'page_id' => $page_id,
	    'post' => $p,
		'categories' => $cat_obj,
        'tags' => $tag_obj
    );

	global $post;
    $tmp_post = $post;
    $post = get_post($p['id']);

    shortcuts\display_template('single.html', $template_array);
}

function page( $request ) {
	shortcuts\set_query_flags(array('page', 'single'));

	$page_name = $request['slug'];
	$page_id = (int) $request['page_id'];

	if ( !empty($page_id) ) { // Preview

	    $page = functions\get_preview($page_id, 'page');
	    $preview = true;

	} else { // Live page
	    $args = array('pagename' => $page_name,
	                  'posts_per_page' => 1,
	                  'post_status' => 'publish');

	    $collection = PostCollection::filter($args);

	    if ( count($collection) != 1 )
	        throw new Http404;
	    $page = $collection->models[0];
	}

	$template_array = array(
	    'page_id' => $page_id,
	    'post' => $page
	);

	shortcuts\display_template('page.html', $template_array);
}

function archive( $request ) {
	shortcuts\set_query_flags(array('page', 'single'));
    
    $page_id = 'archive';

    $args = array(
        'type' => 'monthly',
        'limit' => '12',
        'echo' => false
    );
    $archives = wp_get_archives($args);

    $template_array = array(
	    'page_id' => $page_id,
	    'archive_list' => $archives
	);
	
	shortcuts\display_template('archive.html', $template_array);
}

function date_archive( $request ) {	
	$page_id = 'date';
	$page_num = (!empty($request['page_num']))? $request['page_num']:1;

	$query_flags = array();

    $year = $request['year'];
    $month = $request['month'];
	
	array_push($query_flags, 'single');
	
    $args = array(
        'year' => $year,
        'paged' => $page_num,
        'posts_per_page' => 5
    );
    array_push($query_flags, 'year');

    if (!empty($month)) {
        array_push($query_flags, 'month');
        $args['monthnum'] = $month;

        switch ($month) {
            case '01':
                $month_str = 'January';
                break;
            case '02':
                $month_str = 'February';
                break;
            case '03':
                $month_str = 'March';
                break;
            case '04':
                $month_str = 'April';
                break;
            case '05':
                $month_str = 'May';
                break;
            case '06':
                $month_str = 'June';
                break;
            case '07':
                $month_str = 'July';
                break;
            case '08':
                $month_str = 'August';
                break;
            case '09':
                $month_str = 'September';
                break;
            case '10':
                $month_str = 'October';
                break;
            case '11':
                $month_str = 'November';
                break;
            case '12':
                $month_str = 'December';
                break;
        }
        $title_str = "$month_str $year";
    } else {
        $title_str = $year;
    }

    $posts = PostCollection::filter($args)->models;

    $q = new \WP_Query($args);
    $more_posts = ($q->max_num_pages > 1 && $q->max_num_pages != $page_num)? true:false;

	$location = preg_replace('/page\/\d\/?$/', '', get_site_url());

    $template_array = array(
        'location' => $location,
        'page_id' => $page_id,
        'page_num' => $page_num,
        'more_posts' => $more_posts,
        'posts' => $posts,
        'archive_title' => $title_str
    );

    shortcuts\set_query_flags($query_flags);

	shortcuts\display_template('date_archive.html', $template_array);
}