<?php
/*
Plugin Name: wp-spoiler-alert
Description: Blurs out parts of your post containing spoilers.
Version: 0.1.0
Author: Darshan Sawardekar
Author URI: http://pressing-matters.io/
Plugin URI: http://wordpress.org/plugins/wp-spoiler-alert
License: GPLv2
*/

require_once(__DIR__ .  '/lib/WpSpoilerAlert/Requirements.php');

use WpSpoilerAlert\MinRequirements;
use WpSpoilerAlert\FauxPlugin;
use WpSpoilerAlert\Plugin;

function wp_spoiler_alert_load() {
  require_once(__DIR__ . '/vendor/autoload.php');

  $plugin = Plugin::create(__FILE__);
  $plugin->enable();
}

function wp_spoiler_alert_faux_load($requirements) {
  $plugin = new FauxPlugin('wp-spoiler-alert', $requirements->getResults());
  $plugin->activate(__FILE__);
}

function wp_spoiler_alert_main() {
  $requirements = new MinRequirements();

  if ($requirements->satisfied()) {
    wp_spoiler_alert_load();
  } else {
    wp_spoiler_alert_faux_load($requirements);
  }
}

wp_spoiler_alert_main();
