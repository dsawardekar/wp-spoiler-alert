<?php

namespace WpSpoilerAlert;

class OptionsPage extends \Arrow\OptionsManager\OptionsPage {

  function getTemplateContext() {
    $context = array(
      'max'     => $this->getOption('max'),
      'partial' => $this->getOption('partial'),
      'tooltip' => $this->getOption('tooltip'),
      'custom'  => $this->getOption('custom')
    );

    return $context;
  }

}
