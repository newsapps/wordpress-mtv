<?php
/**
 * @package MTV
 * @version 1.0
 */

namespace mtv\wp\models;

use mtv\models\Model,
    mtv\models\Collection,
    mtv\models\ModelParseException,
    mtv\models\ModelNotFound,
    WPException,
    JsonableException,
    BadMethodCallException,
    WP_Query;

/**
 * Wordpress models
 **/

/**
 * Post Model
 *
 * Core post fields directly from WordPress:
 *   id             // ID of the post
 *   post_author    // ID of the post author
 *   post_date      // timestamp in local time
 *   post_date_gmt  // timestamp in gmt time
 *   post_content   // Full body of the post
 *   post_title     // title of the post
 *   post_excerpt   // excerpt field of the post, caption if attachment
 *   post_status    // post status: publish, new, pending, draft, auto-draft, future, private, inherit, trash
 *   comment_status // comment status: open, closed
 *   ping_status    // ping/trackback status
 *   post_password  // password of the post
 *   post_name      // post slug, string to use in the URL
 *   to_ping        // to ping ??
 *   pinged         // pinged ??
 *   post_modified  // timestamp in local time
 *   post_modified_gmt // timestatmp in gmt tim
 *   post_content_filtered // filtered content ??
 *   post_parent    // id of the parent post, if attachment, id of the post that uses this image
 *   guid           // global unique id of the post
 *   menu_order     // menu order
 *   post_type      // type of post: post, page, attachment, or custom string
 *   post_mime_type // mime type for attachment posts
 *   comment_count  // number of comments
 *   filter         // filter ??
 *
 * Special MTV fields:
 *   post_meta      // an array containing all of the meta for this post
 *   blogid         // id number of the blog this post lives on
 *   post_format    // the post_format for this post
 *   url            // attachments only, url of the original uploaded image or whatever
 *   thumb_url      // attachments only, url of the thumbnail image, if thumbnails are enabled
 *
 * Post object functions
 *   password_required()
 *     Whether post requires password and correct password has been provided.
 *   is_sticky()
 *     Check if post is sticky.
 *   post_class()
 *     Retrieve the classes for the post div as an array.
 *   permalink()
 *     permalink for this post, from WP get_permalink()
 *   categories()
 *     returns an array of categories that are associated with this post
 *   tags()
 *     returns an array of tags that are associated with this post
 *   featured_image()
 *     Returns a Post object representing the featured image
 *   attachments( $extra_query_args )
 *     Returns a PostCollection object representing attachments (gallery images)
 *   the_time( $format )
 *     Returns a formatted date string. Works like WordPress's 'the_time'.
 *   the_date( $format )
 *     Returns a formatted date string. Works like WordPress's 'the_date'.
 *   make_excerpt( $more_text )
 *     Returns a generated excerpt. Simliar to how WordPress makes excerpts in The Loop.
 **/
class Post extends Model {

    public $cache_groups =  array(
        'posts', 'post_meta', 'post_ancestors', 'post_format_relationships');

    public function __toString() {
        return $this->attributes['post_title'];
    }

    public function save() {
        $data = $this->attributes;

        if ( ! empty($data['blogid']) ) {
            $blogid =& $data['blogid'];
            unset( $data['blogid'] );
        } else $blogid = get_current_blog_id();

        if ( ! empty($data['post_meta']) ) {
            $meta =& $data['post_meta'];
            unset( $data['post_meta'] );
        }

        if ( isset($data['post_format']) ) {
            $post_format =& $data['post_format'];
            unset( $data['post_format'] );
        }

        if ( ! empty( $data['id'] ) ) {
            $data['ID'] =& $data['id'];
            unset($data['id']);
        }

        switch_to_blog( $blogid );

        $postid = wp_insert_post( $data, true );

        if ( is_wp_error( $postid ) )
            throw new WPException( $postid );
        else if ( $postid == 0 )
            throw new JsonableException(__("Couldn't update the post", 'mtv'));

        if ( ! empty( $meta ) ) {
            foreach ( $meta as $key => $val ) 
                update_post_meta( $postid, $key, $val );
        }

        if ( isset($post_format) ) {
            $result = set_post_format( $postid, $post_format );
            if ( is_wp_error( $result ) )
                throw new WPException( $result );
        }

        restore_current_blog();

        $this->id = $postid;

        # Invalidate cached data for this Post
        foreach ( $this->cache_groups as $cache_group )
            wp_cache_delete($this->id, $cache_group);

        $this->fetch(); // We refresh the post in case any filters changed the content
    }

    public function fetch() {
        if ( empty($this->attributes['blogid']) || empty($this->attributes['id']) )
            throw new BadMethodCallException(__("Need a blogid and post id to fetch a post", 'mtv'));
        switch_to_blog( $this->attributes['blogid'] );
        $post = get_post( $this->attributes['id'] );
        if ( $post === NULL ) {
            restore_current_blog();
            throw new ModelNotFound("Post", __("Post not found", 'mtv'));
        }
        $this->reload( $post );

        restore_current_blog();
    }

    public function parse( &$postdata ) {
        # Use the parent parse
        $ret =& parent::parse( $postdata );

        # gonna pick a case
        if ( !empty($ret['ID']) ) {
            $ret['id'] =& $ret['ID'];
            unset($ret['ID']);
        }

        # Take only the fields we need, put them in a temp array
        # TODO: current_blog may not be correct
        $ret['blogid'] = get_current_blog_id();

        # Fill up the meta attribute with post meta
        $ret['post_meta'] = array();
        $meta_keys = get_post_custom_keys($ret['id']);
        if ( is_array( $meta_keys ) )
            foreach( $meta_keys as $key )
                $ret['post_meta'][$key] = get_post_meta($ret['id'], $key, true);

        if ( $ret['post_type'] == 'post' )
            $ret['post_format'] = get_post_format( $ret['id'] );

        return $ret;
    }

    public function password_required() {
        if ( empty($this->post_password) )
            return false;

        if ( !isset($_COOKIE['wp-postpass_' . COOKIEHASH]) )
            return true;

        if ( $_COOKIE['wp-postpass_' . COOKIEHASH] != $this->post_password )
            return true;

        return false;
    }

    public function is_sticky() {
        return is_sticky($this->id);
    }

    public function post_class( $class='' ) {
        return get_post_class($class, $this->id);
    }

    public function permalink() {
        if ( get_current_blog_id() !== $this->blogid ) {
            switch_to_blog($this->blogid);

            $permalink = get_permalink($this->id);

            restore_current_blog();

            return $permalink;
        } else
            return get_permalink($this->id);
    }

    public function categories() {
        return get_the_category($this->id);
    }

    public function tags() {
        return get_the_tags($this->id);
    }

    public function featured_image() {
        if ( !empty( $this->post_meta['_thumbnail_id'] ) )
            return AttachmentCollection::get(array(
                'id' => $this->post_meta['_thumbnail_id'],
                'blogid' => $this->blogid
            ));
        else return null;
    }

    public function get_attachments() {
        return AttachmentCollection::for_post( $this->id );
    }

    # TODO: optimize with SQL
    public function clear_attachments() {
        foreach ( $this->get_attachments() as $attachment ) {
            $attachment->post_parent = null;
            $attachment->menu_order = null;
            $attachment->save();
        }
    }

    public function set_attachments( $attachments ) {
        $this->clear_attachments();
        $menu_order = 0;
        foreach ( $attachments as $attachment ) {
            $attachment->post_parent = $this->id;
            $attachment->menu_order = $menu_order;
            $attachment->save();
            $menu_order++;
        }
    }

    public function the_time($format = null) {
        if ($format)
            return mysql2date($format, $this->post_date);
        else
            return mysql2date(get_option('time_format'), $this->post_date);
    }

    public function the_date($format = null) {
        if ($format)
            return mysql2date($format, $this->post_date);
        else
            return mysql2date(get_option('date_format'), $this->post_date);
    }

    public function the_content() {
        return str_replace(']]>', ']]&gt;', apply_filters('the_content', $this->post_content) );
    }

    public function make_excerpt($more_text = null) {
        // Author inserted a <!--more--> tag
        $parts = get_extended($this->post_content);
        if (!empty($parts['extended'])) {
            $ret = trim($parts['main']);

            // Conditionally add a read more link and
            // clean up punctuation and ellipsis at end of excerpt
            $wc_excerpt = str_word_count($ret);
            $wc_content = str_word_count($this->post_content);
            if ($wc_excerpt < $wc_content) {
                $ret = preg_replace('/([\.,;:!?\'"]{4,})$/', '...', $ret . '...');

                if (!empty($more_text))
                    $ret = $ret . ' <a href="'. $this->permalink .'" class="more-link">'. $more_text .'</a>';
            }
        }

        // Excerpt is empty, so generate one
        if (empty($parts['extended'])) {
            $text = strip_shortcodes( $this->post_content );

            $text = apply_filters('the_content', $text);
            $text = str_replace(']]>', ']]&gt;', $text);
            $text = strip_tags($text);
            $excerpt_length = apply_filters('excerpt_length', 55);
            $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
            $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
            if ( count($words) > $excerpt_length ) {
                array_pop($words);
                $text = implode(' ', $words);
                $text = $text . $excerpt_more;
            } else {
                $text = implode(' ', $words);
            }
            $ret = apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
        }

        /* TODO: cache results of this function */

        return $ret;
    }
}

class PostCollection extends Collection {
    public static $model = 'mtv\wp\models\Post';

    public static $default_filter = array(
        'post_type' => 'post',
        'posts_per_page' => 10,
        'order' => 'DESC',
        'paged' => '1'
    );
    public $wp_query;

    public static function filter( $args ) {
        $class = get_called_class();

        $ret = new $class();
        $ret->wp_query = new WP_Query( array_merge(static::$default_filter, $args) );
        $ret->wp_query->get_posts();

        foreach( $ret->wp_query->posts as $post ) {

            $p = new static::$model();

            try {
                $p->reload($post);
                $ret->add($p);
            } catch(ModelParseException $e) {
                # post is bad for some reason, skip it
                continue;
            }
        }

        return $ret;
    }

    /**
     * Make this collection's wp_query the magical global
     * WordPress one. This should make a bunch of native WP
     * stuff work.
     **/
    public function globalize_wp_query() {
        $GLOBALS['wp_the_query'] = $this->wp_query;
        wp_reset_query();
    }
}

class PageCollection extends PostCollection {
    public static $default_filter = array(
        'post_type' => 'page',
        'posts_per_page' => -1,
        'post_status' => 'inherit',
        'orderby' => 'menu_order',
        'order' => 'ASC'
    );
}

class Attachment extends Post {

    public function parse( &$postdata ) {
        # Use the parent parse
        $ret =& parent::parse( $postdata );

        # If this isn't an attachment, we haven't found what we're looking for
        if ( $ret['post_type'] != "attachment" )
            throw new ModelParseException(__("Post is not an attachment", 'mtv'));

        # Add some special fields depending on the post type
        $ret['url'] = wp_get_attachment_url($ret['id']);
        $ret['thumb_url'] = wp_get_attachment_thumb_url($ret['id']);

        return $ret;
    }

}

class AttachmentCollection extends PostCollection {
    public static $model = 'mtv\wp\models\Attachment';

    public static $default_filter = array(
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'post_status' => 'inherit',
        'orderby' => 'menu_order',
        'order' => 'ASC'
    );

    public static function for_post( $post_id ) {
        return static::filter( array('post_parent' => $post_id) );
    }
}

# TODO: make user meta work like it does in Post
class User extends Model {
    public $defaults = array(
        'id' => '0',
        'user_login' => 'guest',
        'sites' => array(),
    );
    public $json_fields = array( "id", "user_login",
        "user_url", "user_email", "display_name", "nickname", "first_name",
        "last_name", "user_description", "can_reblog", "zip", "capabilities",
        "jabber", "aim", "yim", "primary_blog", "user_level", "avatar");
    public $editable_json_fields = array( "user_url", "user_email", "nickname",
        "first_name", "last_name", "user_description", "zip",
        "user_pass", "jabber", "aim", "yim", "userpic", "userthumb" );

    public static $collection = 'mtv\wp\models\UserCollection';

    public function initialize( $attrs ) {
        if ( empty($attrs['avatar']) )
            $this->avatar = get_avatar($attrs['id']);
    }

    public function __toString() {
        return $this->display_name;
    }

    public function validate() {
        // Register
        if ( empty($this->id) ) {
            // Validate username and email
            $result = wpmu_validate_user_signup($this->user_login, $this->user_email);
            if ( $result['errors']->get_error_code() )
                throw new WPException($result['errors']);
        // Update
        } else {
            // Don't accidently set our password to empty
            if ( isset($this->user_pass) && trim($this->user_pass) == '' )
                unset( $this->user_pass );
        }
    }

    public function register() {
        $this->validate();

        // split incoming data and keep the stuff we can pass to
        // update_user_meta. Set the user's password as meta so that we don't
        // haveto ask the user for it again after activation.
        $this->user_meta = array_merge(
            array_diff_assoc($this->attributes, parse_user($this->attributes)),
            array('user_pass' => wp_hash_password($this->user_pass))
        );

        wpmu_signup_user($this->user_login, $this->user_email, $this->user_meta);
    }

    public function save() {

        $this->validate();

        // split the incoming data into stuff we can pass to wp_update_user and
        // stuff we have to add with update_user_meta
        $userdata = parse_user( $this->attributes );
        $usermeta = array_diff_assoc( $this->attributes, $userdata );
        $removemeta = array_diff(
            array_keys($this->_previous_attributes),
            array_keys($this->attributes)
        );

        unset($usermeta['id']); // make sure we don't accidently save the id as meta

        // Create
        if ( empty($this->id) ) {
            // create the new user with all the basic data
            // wp_update_user has bugs that doesn't let you create a user with it
            // http://core.trac.wordpress.org/ticket/17009
            // TODO: just use wp_update_user once the bug is fixed
            $user_id = wp_create_user( $userdata['user_login'], $userdata['user_pass'], $userdata['user_email'] );
            if ( is_wp_error($user_id) )
                throw new WPException($user_id);

            // We should keep track of our user id
            $userdata['ID'] = $user_id;
            $this->id = $user_id;

        // Update
        } else {
            // Check which data has changed
            $data_to_diff = get_userdata( $this->id );
            $data_to_update = array_diff_assoc(
                $userdata, (array) $data_to_diff->data );

            // If we don't have any changes, we don't have to update!
            if ( empty( $data_to_update ) ) $userdata = false;
            else $userdata = array_merge( array('ID' => $this->id), $data_to_update);
        }

        // If we don't have any userdata, we don't have to update!
        if ( $userdata ) {
            $user_id = wp_update_user( $userdata );
            if ( is_wp_error($user_id) )
                throw new WPException($user_id);
        }

        // Update user meta with leftover data
        foreach ( $usermeta as $key => $val )
            update_user_meta( $this->id, $key, $val);

        // Remove any deleted meta
        foreach ( $removemeta as $key )
            delete_user_meta( $this->id, $key );

        $this->fetch();
    }

    public function fetch() {
        // Get userdata (this is a WP_User object in WP 3.3+)
        $user = get_userdata( $this->id );
        $userdata = (array) $user->data;

        // Fetch all user meta, flatten the returned array
        $usermeta = array_map(
            function($x) { return $x[0]; }, get_user_meta( $this->id ));

        // Set value for each user meta key not in userdata
        foreach ( $usermeta as $k => $v ) {
            if ( !in_array($k, array_keys($userdata)) )
                $userdata[$k] = $v;
        }

        $this->reload( $userdata );
    }

    public function parse( &$userdata ) {
        // Use the parent parse
        $ret =& parent::parse( $userdata );

        // Pick a case for the id attribute
        if ( !empty($ret['ID']) ) {
            $ret['id'] = $ret['ID'];
            unset($ret['ID']);
        }

        // get the html to display the users avatar
        $ret['avatar'] = get_avatar( $ret['id'] );

        // Get user capabilities for each blog in the network
        global $wpdb;
        $blogs = $wpdb->get_col("select blog_id from $wpdb->blogs");

        $ret['capabilities'] = array();
        foreach ( $blogs as $k => $v ) {
            if ( $k == 0 ) {
                $_caps = get_user_meta($ret['id'], 'wp_capabilities', true);
                if ( !empty($_caps) )
                    $ret['capabilities'][$v] = array_shift(array_keys((array) maybe_unserialize($_caps)));
            } else {
                $_caps = get_user_meta($ret['id'], 'wp_' . $v . '_capabilities', true );
                if ( !empty($_caps) )
                    $ret['capabilities'][$v] = array_shift(array_keys((array) maybe_unserialize($_caps)));
            }
        }

        return $ret;
    }

    /**
     * signon
     * Takes an array of keyword arguments, attempts to find the user and sign him/her on.
     **/
    public static function signon( $kwargs ) {
        $creds = array();

        if ( ! empty( $kwargs['user_login'] ) ) {
            $creds['user_login'] = $kwargs['user_login'];
        } elseif ( ! empty( $kwargs['user_email'] ) ) {
            $collection = static::$collection;
            $user = $collection::get_by( array( 'user_email' => $kwargs['user_email'] ) );
            $creds['user_login'] = $user->user_login;
        } else throw new JsonableException(__("Please enter your user name or email address.", 'mtv'));

        if ( empty( $kwargs['user_pass'] ) ) throw new JsonableException(__('Please enter your password.', 'mtv'));

        $creds['user_password'] = $kwargs['user_pass'];

        $result = wp_signon($creds, true);

        if ( is_wp_error($result) ) {
            throw new WPException($result);
        } else {
            // TODO:
            // Implement "remember me" functionality with wp_set_auth_cookie
            wp_set_auth_cookie($result->ID, true);
            // wp_set_current_user($result->ID);
        }

        $user = new static( array('id' => $result->ID) );
        $user->fetch();
        return $user;
    }

    public static function activate($key) {
        $result = activate_signup($key);

        if (is_wp_error($result))
            throw new WPException($result);

        $collection = static::$collection;
        $user = $collection::get(array('id' => $result['user_id']));

        return $user;
    }


    public function get_avatar() {
        return get_avatar( $this->id );
    }


    /**
     * sites
     * Returns a SiteCollection containing all the sites this user is connected to.
     **/
    public function sites() {
        if ( empty($this->_sites) )
            $this->_sites = SiteCollection::for_user(array('user_id'=>$this->id));
        return $this->_sites;
    }

    public function to_json() {
        return parent::to_json() + array('sites'=>$this->sites()->to_json());
    }
}

class UserCollection extends Collection {
    public static $model = 'mtv\wp\models\User';

    public static function get_by( $kwargs ) {
        if ( isset($kwargs['user_email']) ) {
            $userid = get_user_id_from_string($kwargs['user_email']);
            if ( $userid === 0 ) throw new JsonableException(__("I don't know that email address", 'mtv'));
        } else if ( isset($kwargs['user_login']) ) {
            $userid = get_user_id_from_string($kwargs['user_login']);
            if ( $userid === 0 ) throw new JsonableException(__("I don't know that user name", 'mtv'));
        } else throw new NotImplementedException();

        $user = new static::$model( array( 'id'=>$userid ) );
        $user->fetch();
        return $user;
    }

    public static function get_current() {
        $userid = get_current_user_id();
        if ( empty($userid) )
            return new static::$model();
        else {
            $user = new static::$model( array( 'id'=>$userid ) );
            $user->fetch();
            return $user;
        }
    }

    /**
     * Returns an array of users based on criteria
     * Proxies to the WordPress get_users function internally. Use this function as you would use
     * get_users.
     *
     * http://codex.wordpress.org/Function_Reference/get_users
     **/
    public static function filter( $kwargs ) {
        # We set the blog_id query param to zero, so WordPress doesn't filter
        # the query based on the permissions for this blog. Hacky.
        if ( !isset($kwargs['blog_id']) ) $kwargs['blog_id'] = 0;

        $class = get_called_class();
        $users = get_users( $kwargs );
        $collection = new $class();
        foreach ($users as $u) {
            $new_user = new static::$model();
            $new_user->reload( $u );
            $collection->add( $new_user );
        }
        return $collection;
    }
}

class Site extends Model {

    public function fetch() {
        $this->reload( get_blog_details( $this->id ) );
    }

    public function parse( &$data ) {
        // Use the parent parse
        $ret =& parent::parse( $data );

        // figure out where the id is
        if ( !empty($ret['userblog_id']) ) {
            $ret['id'] =& $ret['userblog_id'];
            unset($ret['userblog_id']);
        } else if ( !empty($ret['blog_id']) ) {
            $ret['id'] =& $ret['blog_id'];
            unset($ret['blog_id']);
        }

        return $ret;
    }

}

class SiteCollection extends Collection {
    public static $model = 'mtv\wp\models\Site';

    public static function for_user( $kwargs ) {
        if ( isset($kwargs['user_id']) ) {
            $userid = $kwargs['user_id'];
        } else if ( isset($kwargs['user_email']) ) {
            $userid = get_user_id_from_string($kwargs['user_email']);
            if ( $userid === 0 ) throw new JsonableException(__("I don't know that email address", 'mtv'));
        } else if ( isset($kwargs['user_login']) ) {
            $userid = get_user_id_from_string($kwargs['user_login']);
            if ( $userid === 0 ) throw new JsonableException(__("I don't know that username", 'mtv'));
        } else throw new NotImplementedException();

        $class = get_called_class();
        $blogdata = get_blogs_of_user($userid);
        $sites = new $class();
        if ( !empty($blogdata) ) {
            foreach($blogdata as $b) {
                $site = new static::$model();
                $site->reload($b);
                $sites->add($site);
            }
        }
        return $sites;
    }

    public static function published( ) {
        global $wpdb;

        $class = get_called_class();
        $query = "select * from wp_blogs where public='1' and archived='0' and spam='0' and deleted='0' and mature='0'";
        return new $class($wpdb->get_results($query, ARRAY_A));
    }
}

# this returns an array of fields that wordpress's update_user can handle
function parse_user( $userdata ) {
    $user_fields = array(
        "ID",
        "user_pass",
        "user_login",
        "user_nicename",
        "user_url",
        "user_email",
        "display_name",
        "nickname",
        "first_name",
        "last_name",
        "description",
        "rich_editing",
        "user_registered",
        "role",
        "jabber",
        "aim",
        "yim"
    );
    foreach( $userdata as $key => $val )
        if ( ! in_array( $key, $user_fields ) )
            unset( $userdata[$key] );
    return $userdata;
}

# activate user signup, avoid sending a second
# email with username and password in plaintext.
function activate_signup($key) {
    global $wpdb;

    $signup = $wpdb->get_row(
        $wpdb->prepare("select * from $wpdb->signups where activation_key = %s", $key)
    );

    if (empty($signup))
        return new \WP_Error('invalid_key', __('Invalid activation key.', 'mtv'));

    if ($signup->active)
        return new \WP_Error('already_active', __('This account is already activated.', 'mtv'), $signup );

    $user_meta  = unserialize($signup->meta);
    $user_login = $wpdb->escape($signup->user_login);
    $user_email = $wpdb->escape($signup->user_email);
    $user_pass  = $user_meta['user_pass'];
    $user_id    = username_exists($user_login);

    if (!$user_id)
        $user_id = wpmu_create_user($user_login, wp_generate_password( 12, false ), $user_email);

    if (!$user_id)
        return new \WP_Error('create_user', __('Could not create user', 'mtv'), $signup);

    // Be sure to unset the user pass because
    // we don't want to store it as meta once
    // the user is activated
    unset($user_meta['user_pass']);
    foreach ($user_meta as $k => $v)
        update_user_meta($user_id, $k, $v);

    $wpdb->update($wpdb->users, array(
        'user_pass' => $user_pass,
        'user_activation_key' => ''
    ), array('ID' => $user_id));

    $wpdb->update($wpdb->signups, array(
        'active' => 1,
        'activated' => current_time('mysql', true),
        'meta' => ''
    ), array('activation_key' => $key));

    add_new_user_to_blog($user_id, $user_email, '');

    return array(
        'user_id' => $user_id,
        'password' => $password,
        'meta' => $meta
    );
}
