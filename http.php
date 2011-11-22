<?php
/**
 * @package MTV
 * @version 1.0
 */

namespace mtv\http;
use Exception, 
    BadFunctionCallException,
    mtv\shortcuts;

global $url_patterns;
$url_patterns = array();

/**
 * Match a url to the associated function, then call it
 * Takes:
 *   $url - url to run on, probably $_REQUEST['path'] or something
 *   $url_patterns - url regexes and functions to pass them to
 **/
function urlresolver( $kwargs ) {
    extract( $kwargs );

    if ( !isset($url) )
        $url = get_default($_REQUEST, 'url', '');

    if ( !isset($url_patterns) )
        throw new BadFunctionCallException('url_patterns keyword argument required');

    try {

        // Start buffering so we can toss output in case we get an exception
        ob_start();

        if ( resolve( $url, $url_patterns ) ) {
            // everything worked! Flush the buffer and return
            ob_end_flush();
            return true;
        } else
            // We didn't find any matching patterns :( So... 404!
            if (defined('DOING_AJAX') && DOING_AJAX)
                throw new AjaxHttp404;
            else
                throw new Http404;

    } catch (HttpException $e) {
        ob_end_clean();
        // Our view threw an HttpException, so display it
        $e->display();
    } catch (Exception $e) {
        ob_end_clean();
        // Somebody threw some sort of exception, so display 500
        if (defined('DOING_AJAX') && DOING_AJAX)
            $http_ex = new AjaxHttp500($e->getMessage(), $e->getCode());
        else
            $http_ex = new Http500($e->getMessage(), $e->getCode());

        $http_ex->display();
    }

    return false; // We had some errors, so return false
}

function resolve($url, $url_patterns) {
    // cycle through our patterns in order to find a view to execute
    foreach ($url_patterns as $pattern => $view) {
        if ( is_array( $view ) ) resolve($url, $view);
        else if ( preg_match($pattern, $url, $matches) > 0 ) {
            // we found a match!

            // Check to see if the function exists
            if ( ! function_exists( $view ) ) throw new BadFunctionCallException("Can't find view function: $view");

            // pass the match array to the view function
            call_user_func( $view, array_slice($matches, 1) );
            return true; // We're all done, so return
        }
    }
    return false;

}

function include_urls_for($app_name) {
    global $registered_apps;

    if ( isset($registered_apps[$app_name]['urls']) ) {
        include $registered_apps[$app_name]['urls'];
        return $url_patterns;
    } else
        throw new Exception("MTV App $app_name has no urls.php");
}

class HttpException extends Exception {
    public $message = 'HTTP Error!';
    public $code;
    public $headers;
    public $error_data;

    public function __construct( $message=null, $error_data=null ) {
        if ( ! empty($message) ) $this->message = $message;
        $this->error_data = $error_data;
    }

    public function display_header() {
        switch ( $this->code ) {
            case '404':
                header( "HTTP/1.1 404 Not Found" );
                break;
            default:
                header( "HTTP/1.1 500 Internal Server Error" );
        }
    }

    public function display_message() {
        print( $this->message );
    }

    public function display() {
        $this->display_header();
        $this->display_message();
    }
}

class Http404 extends HttpException {
    public $code = '404';

    public function display_message() {
        global $wp_query;
        $wp_query->is_404 = true;

        shortcuts\set_query_flags('page');
        shortcuts\display_template(
            '404.html',
            array(
                'message' => $this->message,
                'data'    => $this->error_data,
                'code'    => $this->code,
                'globals' => $GLOBALS,
                'post'    => $_POST,
                'get'     => $_GET,
                'server'  => $_SERVER
            )
        );
        exit;
    }
}

class Http500 extends HttpException {
    public $code = '500';

    public function display_message() {
        shortcuts\set_query_flags('page');
        shortcuts\display_template(
            '500.html',
            array(
                'message' => $this->message,
                'data'    => $this->error_data,
                'code'    => $this->code,
                'globals' => $GLOBALS,
                'post'    => $_POST,
                'get'     => $_GET,
                'server'  => $_SERVER
            )
        );
        exit;
    }
}

class AjaxHttp500 extends HttpException {
    public $code = '500';
    public function display_message() {
        $response = array(
            'error' => $this->message,
            'data'  => $this->error_data
        );
        shortcuts\display_json($response);
    }
}

class AjaxHttp404 extends HttpException {
    public $code = '404';
    public function display_message() {
        $response = array(
            'error' => 'Callback not found',
        );
        shortcuts\display_json($response);
    }
}
