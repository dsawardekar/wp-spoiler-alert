<?php

namespace WpSpoilerAlert;

class Plugin extends \Arrow\Plugin {

  function __construct($file) {
    parent::__construct($file);

    $this->container
      ->object('pluginMeta'        ,  new PluginMeta($file))
      ->packager('assetPackager'   ,  'Arrow\Asset\Packager')
      ->packager('optionsPackager' ,  'Arrow\Options\Packager')
      ->singleton('optionsController', 'WpSpoilerAlert\OptionsController')
      ->singleton('shortcode'      ,  'WpSpoilerAlert\Shortcode')
      ->singleton('frontEndManifest', 'WpSpoilerAlert\FrontEndManifest');
  }

  function enable() {
    add_action('init', array($this, 'initFrontEnd'));
  }

  function initFrontEnd() {
    $shortcode = $this->lookup('shortcode');

    add_shortcode('spoiler', array($shortcode, 'render'));
    add_action('wp_footer', array($this, 'onFooter'));
  }

  function onFooter() {
    if ($this->lookup('shortcode')->isPresent()) {
      $this->lookup('frontEndManifest')->load(false);
    }
  }

}
