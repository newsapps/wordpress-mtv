<?php

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Util/ErrorHandler.php';

class MTVTest extends PHPUnit_Framework_TestCase {

    protected $backupGlobals = false;

    protected $backupStaticAttributes = false;

    public function setUp() {
        // Make sure we have a WP install and MTV is active
        check_before_wreck();
    }

    public function http_get($route = '') {
        global $settings;

        $ch = curl_init();

        curl_setopt(
            $ch, CURLOPT_URL, 'http://' . $settings['hostname'] . '/' . $route);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_exec($ch);

        $result = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $result;
    }

}

class MTVOutputTest extends PHPUnit_Extensions_OutputTestCase {

    protected $backupGlobals = false;

    protected $backupStaticAttributes = false;

    public function setUp() {
        // Make sure we have a WP install and MTV is active
        check_before_wreck();
    }

}

function check_before_wreck() {
    require_once dirname(__FILE__) . '/lib/cli-load.php';

    $plugin_file = ABSPATH . 'wp-admin/includes/plugin.php';

    // Check for WordPress
    if (file_exists($plugin_file))
        require_once $plugin_file;
    else
        throw new Exception('Tests require a working installation of WordPress.');

    // Make sure plugin is active
    if (!is_plugin_active('mtv/wp-plugin.php'))
        throw new Exception('Tests require MTV plugin is installed and activated.');
}

function reset_app_globals() {
    global $registered_apps, $twig, $apps;

    // Reset registered and installed apps
    $apps = array();
    $registered_apps = array();
    $twig = null;
}

