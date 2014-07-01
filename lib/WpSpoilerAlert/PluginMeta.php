<?php

namespace WpSpoilerAlert;

class PluginMeta extends \Arrow\PluginMeta {

  function __construct($file) {
    parent::__construct($file);

    $this->version = Version::$version;
  }

  function getDefaultOptions() {
    return array(
      'max' => 6,
      'partial' => 3,
      'tooltip' => 'Click for Spoilers!',
      'custom' => false
    );
  }

  function getOptionsContext() {
    $optionsStore = $this->lookup('optionsStore');
    $options      = $optionsStore->getOptions();

    if ($options['custom'] && !$this->canLoadCustomCSS()) {
      $options['custom'] = false;
    }

    return $options;
  }

  function canLoadCustomCSS() {
    return $this->lookup('optionsStore')->getOption('custom') &&
      $this->hasCustomStylesheet();
  }

}
