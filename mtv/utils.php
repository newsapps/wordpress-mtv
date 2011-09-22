<?php
/**
 * @package MTV
 * @version 1.0
 */

// function exception_error_handler($errno, $errstr, $errfile, $errline ) {
//     throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
// }
// set_error_handler("exception_error_handler");

/**
 * Check for an array item, return $default if it's empty
 **/
function get_default($array, $key, $default="") {
    return empty($array[$key]) ? $default : $array[$key];
}

/**
 * Dump the contents of a variable to the error log. works
 * like var_dump.
 **/
function var_log( $stuff ) {
    error_log( stripslashes(var_export( $stuff, true )) );
}

class NotImplementedException extends LogicException {}

class WPException extends JsonableException {
    public $wp_error;
    public function __construct( $wp_error ) {
        parent::__construct( $wp_error->get_error_message() );
        $this->wp_error = $wp_error;
    }
    public function __call( $method, $args ) {
        return call_user_func_array(array($this->wp_error, $name), $args);
    }
    public function to_json() {
        return array_merge(parent::to_json(), array(
            'codes' => $this->wp_error->get_error_codes(),
            'messages' => $this->wp_error->get_error_messages()
        ));
    }
}

class JsonableException extends Exception {
    public function to_json() {
        return array(
            'message' => $this->message,
            'code'    => $this->code
        );
    }
}
