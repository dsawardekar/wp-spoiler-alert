<?php

namespace WpSpoilerAlert;

class OptionsManager extends \Arrow\OptionsManager\OptionsManager {

  function __construct($container) {
    parent::__construct($container);

    $container
      ->singleton('optionsValidator', 'WpSpoilerAlert\OptionsValidator')
      ->singleton('optionsPage', 'WpSpoilerAlert\OptionsPage');
  }

}
