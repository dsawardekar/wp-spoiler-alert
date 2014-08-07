<?php
/*
Plugin Name: wp-spoiler-alert
Description: Shortcode to hide Spoilers in your Posts
Version: 0.8.3
Author: Darshan Sawardekar
Author URI: http://pressing-matters.io/
Plugin URI: http://wordpress.org/plugins/wp-spoiler-alert
License: GPLv2
*/

require_once(__DIR__ . '/vendor/dsawardekar/arrow/lib/Arrow/ArrowPluginLoader.php');

function wp_spoiler_alert_main() {
  $options = array(
    'plugin' => 'WpSpoilerAlert\Plugin',
    'arrowVersion' => '1.8.0'
  );

  ArrowPluginLoader::load(__FILE__, $options);
}

wp_spoiler_alert_main();
