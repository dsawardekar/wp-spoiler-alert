<?php

namespace WpSpoilerAlert;

use Encase\Container;
use Arrow\AssetManager\AssetManager;

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

  function __construct($file) {
    $this->container = new Container();
    $this->container
      ->object('pluginMeta', new PluginMeta($file))
      ->object('assetManager', new \Arrow\AssetManager\AssetManager($this->container))
      ->object('optionsManager', new OptionsManager($this->container))
      ->singleton('shortcode', 'WpSpoilerAlert\Shortcode');
  }

  function lookup($key) {
    return $this->container->lookup($key);
  }

  function enable() {
    add_action('init', array($this, 'initFrontEnd'));
    add_action('admin_init', array($this, 'initAdmin'));
    add_action('admin_menu', array($this, 'initAdminMenu'));
  }

  function initAdmin() {
    $this->lookup('optionsPostHandler')->enable();
  }

  function initAdminMenu() {
    $this->lookup('optionsPage')->register();
  }

  function initFrontEnd() {
    $shortcode = $this->lookup('shortcode');

    add_shortcode('spoiler', array($shortcode, 'render'));
    add_action('wp_footer', array($this, 'loadSpoilerJS'));
  }

  function loadSpoilerJS() {
    $shortcode = $this->lookup('shortcode');

    if ($shortcode->isPresent()) {
      $options = array();
      $options['dependencies'] = array('jquery');

      $loader = $this->lookup('scriptLoader');
      $loader->stream('spoiler', $options);

      $options = array();
      $options['dependencies'] = array('spoiler');
      $options['localizer'] = array($this, 'getPluginOptions');

      if ($this->canLoadCustomCSS()) {
        $this->loadCustomCSS();
      }

      $loader->stream('spoiler-options', $options);
    }
  }

  function canLoadCustomCSS() {
    return $this->lookup('optionsStore')->getOption('custom') &&
      $this->lookup('pluginMeta')->hasCustomStylesheet();
  }

  function loadCustomCSS() {
    $loader = $this->lookup('stylesheetLoader');
    $loader->stream('theme-custom');
  }

  function getPluginOptions($script) {
    $options = $this->lookup('optionsStore')->getOptions();
    if ($options['custom'] && !$this->canLoadCustomCSS()) {
      $options['custom'] = false;
    }

    return $options;
  }

}
