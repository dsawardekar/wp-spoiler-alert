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
      ->object('pluginVersion', Version::$version)
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
      ->singleton('adminScriptLoader', 'WordPress\AdminScriptLoader')

      ->singleton('shortcode', 'WpSpoilerAlert\Shortcode');

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
      'max' => 6,
      'partial' => 3,
      'tooltip' => 'Click for Spoilers!',
      'custom' => false
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
    $shortcode = $this->lookup('shortcode');

    add_shortcode('spoiler', array($shortcode, 'render'));
    add_action('wp_footer', array($this, 'loadSpoilerJS'));
  }

  function loadSpoilerJS() {
    $shortcode = $this->lookup('shortcode');

    if ($shortcode->isPresent()) {
      $options = $this->getScriptOptions();
      $options['dependencies'] = array('jquery');

      $loader = $this->lookup('scriptLoader');
      $loader->stream('spoiler', $options);

      $options = $this->getScriptOptions();
      $options['dependencies'] = array('spoiler');
      $options['localizer'] = array($this, 'getPluginOptions');

      if ($this->canLoadCustomCSS()) {
        $this->loadCustomCSS();
      }

      $loader->stream('spoiler-run', $options);
    }
  }

  function canLoadCustomCSS() {
    return $this->lookup('optionStore')->getOption('custom') &&
      $this->hasCustomStylesheet();
  }

  function loadCustomCSS() {
    $loader = $this->lookup('stylesheetLoader');
    $loader->stream('theme-custom', $this->getStylesheetOptions());
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
    if ($options['custom'] && !$this->canLoadCustomCSS()) {
      $options['custom'] = false;
    }

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
