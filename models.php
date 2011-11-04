<?php
/**
 * Models
 * A bunch of classes that standardize access to WordPress data. Model based, similar 
 * to Django, Backbone or Rails. Heavily inspired by Backbone's models and collections.
 * TODO: Use more references in order to save memory
 **/

namespace mtv\models;

use Iterator, ArrayAccess, Countable, Exception, LogicException, BadMethodCallException, JsonableException;
use mtv\shortcuts;

/**
 * Model class
 *
 * You can access the fields on a model object in the following ways:
 *   $post->field_name;
 *   $post->attributes['field_name'];
 *
 * If you need an array of the fields on a model object, do this:
 *   $data = $post->attributes;
 **/
class Model {
    // Where the model's data is stored
    public $attributes           = array();
    // Default model data used when a new model is created
    public $defaults             = array();
    // Attributes that are OK to send over the wire
    public $json_fields          = array();
    // Attributes that can be updated over the wire
    public $editable_json_fields = array();

    public static $collection = 'mtv\models\Collection';

    // Is this model's data sync'd with the DB?
    protected $_synchronized = false;
    // Attributes that have been changed since the last save or fetch
    protected $_previous_attributes = array();

    public function __construct( $kwargs=array() ) {
        $this->initialize( $kwargs );
        if ( empty($this->defaults) )
            $this->set( $kwargs );
        else
            $this->set( array_merge($this->defaults, $kwargs) );
    }

    // Write the data in this model to permanent storage
    public function save() {
        throw new \NotImplementedException();
    }

    // Validate the data in this model
    public function validate() {
        throw new \NotImplementedException();
    }

    // Call initialize when the model is created
    public function initialize( $kwargs ) {}

    public function __toString() {
        return get_called_class();
    }

    public function __get($name) {
        return $this->attributes[$name];
    }

    public function __set( $name, $val ) {
        $this->set( array( $name => $val ) );
    }

    public function __unset( $name ) {
        $this->clear( $name );
    }

    public function __isset( $name ) {
        return isset($this->attributes[$name]);
    }

    // Delete all the attributes in this model
    public function clear() {
        // TODO: update $this->_previous_attributes with removed items
        foreach ( func_get_args() as $arg ) {
            $this->_previous_attributes[$arg] = $this->attributes[$arg];
            unset( $this->attributes[$arg] );
        }
    }

    // Set a bunch of attributes at once
    public function set( $kwargs, $fetching=false ) {
        $this->attributes = array_merge( $this->attributes, (array) $kwargs );
    }

    // Populate this model from permanent storage
    public function fetch() {
        // get my attributes from the db
        // $from_db = new Object;
        // pass the results to reload
        // $this->reload( $from_db );
        throw new \NotImplementedException();
    }

    /**
     * parse
     * Process the raw data from permanent storage
     **/
    public function parse( $data ) {
        // Make sure we have an array and not an object
        if ( is_object($data) ) $data = (array) $data;

        if ( !empty($data['ID']) ) {
            $data['id'] = $data['ID'];
            unset($data['ID']);
        }

        return $data;
    }

    // Update this model with data from permanent storage
    public function reload( $data ) {
        // Parse raw data from the DB
        $tmp =& $this->parse($data);

        // Reset any change tracking
        $this->_previous_attributes = array();
        $this->_synchronized = true;

        // Set the attributes
        $this->set( $tmp, true );
    }

    // Returns an assoc array of this model's data to send over the wire
    public function to_json() {
        // decide which fields to send over the wire
        if ( empty( $this->json_fields ) )
            return $this->attributes;
        else {
            $ret = array();
            foreach ( $this->json_fields as $k )
                $ret[$k] = $this->attributes[$k];

            return $ret;
        }
    }

    // Create a new model from json data sent over the wire
    public static function from_json( $data ) {
        $class = get_called_class();
        $obj = new $class;
        $obj->set_from_json($data);
        return $obj;
    }

    // Update this model from json data sent over the wire
    public function set_from_json( $data ) {
        // decide which fields to use from the wire
        $new_data = (array) json_decode( stripslashes( $data ) );
        if ( empty( $this->editable_json_fields ) )
            $this->set( $new_data );
        else {
            foreach ( $new_data as $k=>$v )
                if ( in_array($k, $this->editable_json_fields) )
                    $this->$k = $v;
        }
    }

}

class Collection implements Iterator, ArrayAccess, Countable {
    public $models = array();
    public static $model  = 'mtv\models\Model';

    public function __construct( $array=array() ) {
        foreach ( $array as $kwargs ) {
            array_push( $this->models, new static::$model( $kwargs ) );
        }
    }

    // Iterator interface
    public function current() {
        return current( $this->models );
    }
    public function next() {
        return next( $this->models );
    }
    public function key() {
        return key( $this->models);
    }
    public function valid() {}
    public function rewind() {
        return prev( $this->models );
    }

    // ArrayAccess interface
    public function offsetExists( $offset ) {
        return isset( $this->models[$offset] );
    }
    public function offsetGet( $offset ) {
        return isset( $this->models[$offset] ) ? $this->models[$offset] : null;
    }
    public function offsetSet( $offset, $value ) {
        if (is_null($offset)) $this->models[] = $value;
        else $this->models[$offset] = $value;
    }
    public function offsetUnset( $offset ) {
        unset($this->models[$offset]);
    }

    // Countable interface
    public function count() {
        return count($this->models);
    }

    // Collection methods
    public function add( $model ) {
        $this->models[] =& $model;
        // array_push( $this->models, $model );
    }

    public function clear() {
        $this->models = array();
    }

    public function to_json() {
        $tmp_array = array();
        foreach ($this->models as $model) {
            array_push($tmp_array, $model->to_json());
        }
        return $tmp_array;
    }

    public static function get( $kwargs ) {
        $model = new static::$model( $kwargs );
        $model->fetch();
        return $model;
    }

    public static function get_by( $kwargs ) { throw new \NotImplementedException(); }
    public static function filter( $kwargs ) { throw new \NotImplementedException(); }
}

/**
 * Model exceptions
 **/
class ModelParseException extends JsonableException {}

class ModelNotFound extends JsonableException {
    public $class_name;

    public function __construct( $class_name, $message = "", $code = 0, $previous = NULL ) {
        parent::__construct( $message, $code, $previous );
        $this->class_name = $class_name;
    }

    public function to_json() {
        return array(
            'message' => $this->message,
            'code'    => $this->code,
            'class'   => $this->class
        );
    }
}

