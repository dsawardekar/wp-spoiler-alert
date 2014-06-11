<?php

namespace WpSpoilerAlert\Options;

class Packager extends \Arrow\Options\Packager {

  function onInject($container) {
    parent::onInject($container);

    $container
      ->singleton('optionsValidator', 'WpSpoilerAlert\Options\Validator');
  }

}
