<?php
/*
Plugin Name: E-Mail Broadcasting
Author: Adrian Dimitrov <dimitrov.adrian@gmail.com>
Author URI: http://www.facebook.com/dimitrov.adrian
Plugin URI: http://wordpress.org/extend/plugins/e-mail-broadcasting/
Description: Easy for use bulk email sender. If you have online magazine alike blog or website and want to send users annoncments, this plugin is for you.
Version: 0.2.2
License: GPLv2 or later
*/


// get relative path to the plugin
// TODO: have to refactor number and names of these constants
define('DIR_E_MAIL_BROADCASTING', dirname(__FILE__));
define('URL_E_MAIL_BROADCASTING',  basename(dirname(__FILE__)));
define('PATH_E_MAIL_BROADCASTING', URL_E_MAIL_BROADCASTING.'/'.basename(__FILE__));

// Load textdomain for plugin
load_plugin_textdomain(URL_E_MAIL_BROADCASTING);

// Load main class
include 'class.e-mail-broadcasting.php';

// Load the controller
include 'controller.php';

// Load the goodies
include 'goodies.php';

register_uninstall_hook(PATH_E_MAIL_BROADCASTING, array('EMailBroadcastingInit', 'on_uninstall'));
register_activation_hook(PATH_E_MAIL_BROADCASTING, array('EMailBroadcastingInit', 'on_activate'));
register_deactivation_hook(PATH_E_MAIL_BROADCASTING, array('EMailBroadcastingInit', 'on_deactivate'));

$controller = new EmailBroadcastingController;

add_action('init',  array($controller, 'bootstrap')); //plugins_loaded