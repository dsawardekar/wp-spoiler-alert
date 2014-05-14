<?php
/*
Plugin Name: wp-spoiler-alert
Description: Shortcode to hide Spoilers in your Posts
Version: 0.2.4
Author: Darshan Sawardekar
Author URI: http://pressing-matters.io/
Plugin URI: http://wordpress.org/plugins/wp-spoiler-alert
License: GPLv2
*/

require_once(__DIR__ . '/vendor/dsawardekar/wp-requirements/lib/Requirements.php');

function wp_spoiler_alert_main() {
  $requirements = new WP_Requirements();

  if ($requirements->satisfied()) {
    wp_spoiler_alert_register();
  } else {
    $plugin = new WP_Faux_Plugin('WP Spoiler Alert', $requirements->getResults());
    $plugin->activate(__FILE__);
  }
}

function wp_spoiler_alert_register() {
  require_once(__DIR__ . '/vendor/dsawardekar/arrow/lib/Arrow/ArrowPluginLoader.php');

  $loader = ArrowPluginLoader::getInstance();
  $loader->register('wp-spoiler-alert', '0.4.1', 'wp_spoiler_alert_load');
}

function wp_spoiler_alert_load() {
  require_once(__DIR__ . '/vendor/autoload.php');

  $plugin = \WpSpoilerAlert\Plugin::create(__FILE__);
  $plugin->enable();
}

wp_spoiler_alert_main();
