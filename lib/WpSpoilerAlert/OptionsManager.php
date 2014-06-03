<?php

namespace WpSpoilerAlert;

class OptionsManager extends \Arrow\Ember\OptionsManager {

  function __construct($container) {
    parent::__construct($container);

    $container
      ->singleton('optionsValidator', 'WpSpoilerAlert\OptionsValidator');
  }

}
