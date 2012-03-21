<?php

namespace mtv;

require 'Twig/Autoloader.php';

use Twig_Autoloader, Twig_Environment, Twig_Loader_Filesystem;

use mtv\http;

define("MTV_VERSION", "1.0.0");

# For a plugin or theme, i need to load:
#   urls
#   models
#   views
#   templates
$GLOBALS['registered_apps'] = array();

function register_app( $name, $path ) {
    $views_file   = $path . '/views.php';
    $urls_file    = $path . '/urls.php';
    $models_file  = $path . '/models.php';

    $template_dir = $path . '/templates';
    $tags_file    = $path . '/templatetags/tags.php';
    $funcs_file   = $path . '/templatetags/functions.php';
    $filter_file  = $path . '/templatetags/filters.php';

    $app_data = array();

    if ( file_exists($views_file) ) $app_data['views'] = $views_file;
    if ( file_exists($urls_file) ) $app_data['urls'] = $urls_file;
    if ( file_exists($models_file) ) $app_data['models'] = $models_file;

    if ( file_exists($template_dir) ) $app_data['templates'] = $template_dir;
    if ( file_exists($tags_file) ) $app_data['tags'] = $tags_file;
    if ( file_exists($funcs_file) ) $app_data['functions'] = $funcs_file;
    if ( file_exists($filter_file) ) $app_data['filters'] = $filter_file;

    global $registered_apps;

    $registered_apps[$name] = $app_data;

}

/**
 * load MTV
 * Takes:
 *   $apps - MTV apps to load. Apps must be registered. Loads in order.
 **/
function load( $apps ) {
    global $registered_apps;

    # load our models, views and templates
    $template_dirs = array();
    foreach ( $apps as $name ) {
        $app = $registered_apps[$name];

        if ( $app['views'] ) include_once $app['views'];
        if ( $app['models'] ) include_once $app['models'];

        if ( $app['templates'] ) array_push($template_dirs, $app['templates']);
    }

    # Time to initialize our template engine
    Twig_Autoloader::register();

    global $twig;

    if (empty($twig)) {
        $loader =  new Twig_Loader_Filesystem($template_dirs);

        if ( DEPLOYMENT_TARGET == "development" ) {
            $twig = new Twig_Environment($loader, array('debug' => true));
        } else {
            $cache_dir = '/tmp/mtv_tmpl_cache';
            if ( ini_get('safe_mode') ) {
                $cache_dir = __DIR__.'/tmp/mtv_tmpl_cache';
            }
            # TODO: get a temp directory from php to use for caching
            $twig = new Twig_Environment($loader, array(
                'cache' => $cache_dir,
                'auto_reload' => true
            ));
        }
    }

    # now that we have a template engine, load some goodies
    foreach ( $apps as $name ) {
        $app = $registered_apps[$name];
        if ( $app['tags'] ) include_once $app['tags'];
        if ( $app['functions'] ) include_once $app['functions'];
        if ( $app['filters'] ) include_once $app['filters'];
    }

}

/**
 * run MTV
 * Takes:
 *   $url - url to run on, probably $_REQUEST['path'] or something
 *   $apps - MTV apps to load. Apps must be registered. Loads in order.
 **/
function run( $kwargs ) {
    global $registered_apps;

    extract( $kwargs );

    load( $apps );

    # What's the url for this request?
    if ( ! $url )
        $url = $_REQUEST['path'];

    $all_url_patterns = array();
    foreach ( $apps as $name ) {
        $app = $registered_apps[$name];
        if ( $app['urls'] ) include_once $app['urls'];
        if (is_array($url_patterns)) {
            $all_url_patterns = array_merge($all_url_patterns, $url_patterns);
        }
    }

    var_dump($all_url_patterns);
    exit;

    # globalize our $url_patterns
    $GLOBALS['url_patterns'] = $all_url_patterns;

    # oh, right, we gotta do something with our url
    http\urlresolver( array('url'=>$url, 'url_patterns'=>$url_patterns) );

    # Smell ya later.
    exit;
}

# load the rest of MTV
include('utils.php');
include('http.php');
include('shortcuts.php');
include('models.php');

