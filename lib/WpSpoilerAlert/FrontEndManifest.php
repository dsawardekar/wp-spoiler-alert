<?php

namespace WpSpoilerAlert;

class FrontEndManifest extends \Arrow\Asset\Manifest\Manifest {

  public $pluginMeta;
  public $shortcode;
  public $optionsStore;

  function needs() {
    return array_merge(
      parent::needs(),
      array('shortcode', 'pluginMeta', 'optionsStore')
    );
  }

  function getScripts() {
    if ($this->shortcode->isPresent()) {
      return array($this->pluginMeta->getSlug());
    } else {
      return array();
    }
  }

  function getStyles() {
    if ($this->hasStyles()) {
      return array('theme-custom');
    } else {
      return array();
    }
  }

  function hasShortcode() {
    return $this->shortcode->isPresent();
  }

  function hasScripts() {
    return $this->hasShortcode();
  }

  function hasStyles() {
    return $this->hasShortcode() &&
      $this->optionsStore->getOption('custom') &&
      $this->pluginMeta->hasCustomStylesheet();
  }

}
