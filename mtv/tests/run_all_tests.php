<?php

/*
 * PHPUnit is required in order to run these tests.
 * You can install PHPUnit by running requirements.sh
 * packaged with MTV.
 *
 * You'll also need to edit the accompanying settings.json file
 * in this directory and update the hostname of the WordPress
 * install you're using to run these tests.
 *
 * Note: if you encounter errors and/or failures when
 * running tests, you should try fixing errors first. They're
 * usually easier to tackle and might resolve failures.
 */

require_once 'lib/cli-load.php';
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Util/ErrorHandler.php';

// All tests
$tests = array();

// Printer object will eventually print results
$printer = new PHPUnit_TextUI_ResultPrinter(
    NULL, true, !stristr(PHP_OS, 'WIN'));

// Results object tracks results for all tests
$result = new PHPUnit_Framework_TestResult;
$result->addListener($printer);

// Test core MTV functions
require_once 'test_core.php';
$tests['MTVCoreTest'] = new PHPUnit_Framework_TestSuite('MTVCoreTest');

// Test MTV's http lib
require_once 'test_http.php';
$tests['MTVHttpTest'] = new PHPUnit_Framework_TestSuite('MTVHttpTest');

// Test Model model
require_once 'test_model.php';
$tests['MTVModelTest'] = new PHPUnit_Framework_TestSuite('MTVModelTest');

// TODO:

// Test Collection model
// Stub

// Test Post model
// Stub

// Test PostCollection model
// Stub

// Test User model
// Stub

// Test UserCollection model
// stub

// Test Site model
// stub

// Test SiteCollection model
// stub

// Add above test suites to master suite and run, printing the result
$suite = new PHPUnit_Framework_TestSuite();
foreach ($tests as $k => $v)
    $suite->addTestSuite($k);
$suite->run($result);
$printer->printResult($result);

