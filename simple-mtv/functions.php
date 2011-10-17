<?php
/* 
* Our PHP functions. Any functions we want to register 
* with Twig can go in templatetags/functions.php 
*/

/*
* Configure MTV
*
* This makes MTV aware of an app. This function doesn't 
* load the app; it simply names the app for later reference.
* All we need to provide is the theme’s name and the path it’s on. 
*/
if ( function_exists('mtv\register_app') )
    mtv\register_app('mtv_theme', __DIR__);

/* 
* Set up enabled apps
* 
* Declares a global variable that contains an array of all our 
* installed apps. The order of items in the array matters. 
* When rendering a template, Twig uses this order to look 
* for templates that match. The wp app that is included 
* with the MTV plugin registers a lot of WordPress functions 
* for you to use in your templates.
*/
global $apps;
$apps = array('mtv_theme', 'wp');

# Widgetize our sidebar
if ( function_exists('register_sidebar') )
    register_sidebar('sidebar-1');