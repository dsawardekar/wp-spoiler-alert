<?php

namespace WpSpoilerAlert;

class PluginMeta extends \Arrow\PluginMeta {

  function getVersion() {
    return Version::$version;
  }

  function getDefaultOptions() {
    return array(
      'max' => 6,
      'partial' => 3,
      'tooltip' => 'Click for Spoilers!',
      'custom' => false
    );
  }

}
