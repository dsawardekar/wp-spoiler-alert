<?php

namespace WpSpoilerAlert;

class FrontEndManifest extends \Arrow\Asset\Manifest\Manifest {

  public $pluginMeta;
  public $optionsStore;

  function __construct() {
    $this->setContext(array($this, 'getFrontEndContext'));
  }

  function needs() {
    return array_merge(
      parent::needs(),
      array('pluginMeta', 'optionsStore')
    );
  }

  function getScripts() {
    return array($this->pluginMeta->getSlug());
  }

  function getStyles() {
    if ($this->hasStyles()) {
      return array('theme-custom');
    } else {
      return array();
    }
  }

  function hasStyles() {
    return $this->optionsStore->getOption('custom') &&
      $this->pluginMeta->hasCustomStylesheet();
  }

  function getFrontEndContext() {
    $options = $this->optionsStore->getOptions();

    if ($options['custom'] && !$this->hasStyles()) {
      $options['custom'] = false;
    }

    return $options;
  }

}
