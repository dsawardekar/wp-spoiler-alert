<?php

namespace Arrow;

class PluginMeta {

  public $version           = '0.0.0';
  public $file              = null;
  public $slug              = null;
  public $dir               = null;
  public $optionsKey        = null;
  public $optionsPageTitle  = null;
  public $displayName       = null;
  public $defaultOptions    = array();
  public $scriptOptions     = array('in_footer' => true);
  public $stylesheetOptions = array('media' => 'all');
  public $minify;
  public $minifyChecks = true;

  function __construct($file) {
    $this->file = $file;
  }

  function getVersion() {
    if ($this->getDebug()) {
      /* for cache busting in development */
      return strval(strtotime('now'));
    } else {
      return $this->version;
    }
  }

  function getFile() {
    return $this->file;
  }

  function getSlug() {
    if (is_null($this->slug)) {
      $this->slug = basename($this->getFile(), '.php');
    }

    return $this->slug;
  }

  function getDir() {
    if (is_null($this->dir)) {
      $this->dir = dirname($this->getFile());
    }

    return $this->dir;
  }

  function getDebug() {
    return defined('WP_DEBUG') && WP_DEBUG === true;
  }

  function getOptionsKey() {
    if (is_null($this->optionsKey)) {
      $this->optionsKey = $this->getSlug() . '-options';
    }

    return $this->optionsKey;
  }

  function getOptionsCapability() {
    return 'manage_options';
  }

  function getDisplayName() {
    if (is_null($this->displayName)) {
      $this->displayName  = str_replace('-', ' ', $this->getSlug());
      $this->displayName  = str_replace('wp', 'WP', $this->displayName);
      $this->displayName  = ucwords($this->displayName);
    }

    return $this->displayName;
  }

  function getOptionsPageTitle() {
    if (is_null($this->optionsPageTitle)) {
      $this->optionsPageTitle .= $this->getDisplayName() . ' | Settings';
    }

    return $this->optionsPageTitle;
  }

  function getOptionsMenuTitle() {
    return $this->getDisplayName();
  }

  function getOptionsPageSlug() {
    return $this->getSlug();
  }

  function getOptionsMenuSlug() {
    return $this->getSlug();
  }

  function getDefaultOptions() {
    return $this->defaultOptions;
  }

  function getOptionsUrl() {
    return admin_url(
      'options-general.php?page=' . $this->getOptionsMenuSlug()
    );
  }

  function getScriptOptions() {
    if (!array_key_exists('version', $this->scriptOptions)) {
      $this->scriptOptions['version'] = $this->getVersion();
    }

    return $this->scriptOptions;
  }

  function getStylesheetOptions() {
    if (!array_key_exists('version', $this->stylesheetOptions)) {
      $this->stylesheetOptions['version'] = $this->getVersion();
    }

    return $this->stylesheetOptions;
  }

  function getCustomStylesheet($name = 'custom.css') {
    return get_stylesheet_directory() . '/' . $this->getSlug() . '/' . $name;
  }

  function hasCustomStylesheet($name = 'custom.css') {
    return file_exists($this->getCustomStylesheet($name));
  }

  function getMinify() {
    if (is_null($this->minify)) {
      $this->minify = $this->getDebug() === false;
    }

    return $this->minify;
  }

  function getMinifyChecks() {
    return $this->minifyChecks;
  }
}
