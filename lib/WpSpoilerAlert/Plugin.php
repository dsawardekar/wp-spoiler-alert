<?php

namespace WpSpoilerAlert;

use Encase\Container;
use WordPress\Script;
use WordPress\ScriptLoader;
use WordPress\Stylesheet;
use WordPress\StylesheetLoader;
use WordPress\Logger;

class Plugin {

  static $instance = null;
  static function create($file) {
    if (is_null(self::$instance)) {
      self::$instance = new Plugin($file);
    }

    return self::$instance;
  }

  static function getInstance() {
    return self::$instance;
  }

  public $container;

  function __construct($pluginFile) {
    $container = new Container();
    $container
      ->object('pluginFile', $pluginFile)
      ->object('pluginSlug', 'wp_spoiler_alert')
      ->object('pluginDir', untrailingslashit(plugin_dir_path($pluginFile)))
      ->object('pluginVersion', time())
      ->object('defaultOptions', $this->getDefaultOptions())
      ->object('optionName', 'wp_spoiler_alert_options')

      ->factory('script', 'WordPress\Script')
      ->factory('stylesheet', 'WordPress\Stylesheet')
      ->singleton('scriptLoader', 'WordPress\ScriptLoader')
      ->singleton('stylesheetLoader', 'WordPress\StylesheetLoader')

      ->singleton('twigHelper', 'WordPress\TwigHelper')
      ->initializer('twigHelper', array($this, 'initTwigHelper'))

      ->singleton('optionStore', 'WpSpoilerAlert\OptionStore')
      ->singleton('optionPage', 'WpSpoilerAlert\OptionPage')
      ->singleton('optionSanitizer', 'WpSpoilerAlert\OptionSanitizer')
      ->singleton('adminScriptLoader', 'WordPress\AdminScriptLoader');


    $this->container = $container;
  }

  function lookup($key) {
    return $this->container->lookup($key);
  }

  function enable() {
    add_action('init', array($this, 'initFrontEnd'));
    add_action('admin_init', array($this, 'initOptionStore'));
    add_action('admin_menu', array($this, 'initOptionPage'));
  }

  function getDefaultOptions() {
    return array(
      'maxBlur' => 10,
      'partialBlur' => 4
    );
  }

  function initOptionStore() {
    $this->lookup('optionStore')->register();
  }

  function initOptionPage() {
    $this->lookup('optionPage')->register();
    $this->initAdminScripts();
  }

  function initAdminScripts() {
    $loader = $this->lookup('adminScriptLoader');
    $loader->schedule('wp-spoiler-alert-options', $this->getScriptOptions());
    $loader->load();
  }

  function initTwigHelper($twigHelper, $container) {
    $twigHelper->setBaseDir($container->lookup('pluginDir'));
  }

  function initFrontEnd() {
    add_action('wp_footer', array($this, 'loadSpoilerJS'));
  }

  function loadSpoilerJS() {
    $shortcode = $this->lookup('shortcode');

    if ($shortcode->isPresent()) {
      $options = $this->getScriptOptions();
      $options['localizer'] = array($this, 'getPluginOptions');

      $loader = $this->lookup('scriptLoader');
      $loader = $this->stream('spoiler', $options);
    }
  }

  function hasCustomStylesheet() {
    return file_exists($this->getCustomThemePath());
  }

  function getCustomThemePath() {
    $path  = get_stylesheet_directory();
    $path .= '/wp-spoiler-alert/custom.css';

    return $path;
  }

  function getPluginOptions($script) {
    $options = $this->lookup('optionStore')->getOptions();
    return $options;
  }

  function getStylesheetOptions() {
    return array(
      'version' => $this->lookup('pluginVersion'),
      'media' => 'all'
    );
  }

  function getScriptOptions() {
    return array(
      'version' => $this->lookup('pluginVersion'),
      'in_footer' => true
    );
  }
}
