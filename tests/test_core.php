<?php

require_once 'lib/mtv_test_classes.php';

class MTVCoreTest extends MTVTest {

    public function setUp() {
        check_before_wreck();
        reset_app_globals();
    }

    public function test_register_app() {
        global $registered_apps;

        \mtv\register_app('test_app', dirname(__DIR__) . '/test_app');

        // Should have one app
        $this->assertEquals(count($registered_apps), 1);

        // App name should be 'test_app'
        $this->assertTrue(array_key_exists('test_app', $registered_apps));
    }

    public function test_load() {
        global $twig, $apps;

        // Register and load test_app
        \mtv\register_app('test_app', dirname(__FILE__) . '/test_app');

        $apps = array('test_app');
        \mtv\load($apps);

        // Make sure test_app home view function is loaded
        $this->assertTrue(function_exists('test_app\views\home'));
        // Make sure test_app TestModel class is loaded
        $this->assertTrue(class_exists('test_app\models\TestModel'));
        // Make sure Twig was reinitialized
        $this->assertTrue(!empty($twig));
    }

    public function test_run() {
        /*
         * NOTE: This test checks functionality of
         * the http.php lib. If these tests pass,
         * we can consider the http lib functionally sound.
         * TODO: Unit tests for http.php functions.
         */

        // Request the home page
        $result = $this->http_get('/');
        $this->assertEquals($result, 200);

        // Request non-existent page
        $result = $this->http_get('/404');
        $this->assertEquals($result, 404);
    }

}

