<?php

namespace WpSpoilerAlert;

use WordPress\Logger;

class OptionPage {

  function needs() {
    return array('twigHelper', 'optionStore', 'pluginSlug');
  }

  function getPageTitle() {
    return $this->pluginSlug . ' | Settings';
  }

  function getMenuTitle() {
    return $this->pluginSlug;
  }

  function getCapability() {
    return 'manage_options';
  }

  function getMenuSlug() {
    return $this->pluginSlug;
  }

  function register() {
    add_options_page(
      $this->getPageTitle(),
      $this->getMenuTitle(),
      $this->getCapability(),
      $this->getMenuSlug(),
      array($this, 'show')
    );
  }

  function show() {
    $context = $this->getTemplateContext();
    $this->twigHelper->display('options_form', $context);
  }

  function getTemplateContext() {
    $context = array(
      'settings_fields' => $this->getSettingsFields($this->pluginSlug),
      'maxBlur' => $this->optionStore->getOption('maxBlur'),
      'partialBlur' => $this->optionStore->getOption('partialBlur'),
    );

    return $context;
  }

  function getChecked($option) {
    $value = $this->optionStore->getOption($option);
    return $value ? 'checked' : '';
  }

  function getSettingsFields($slug) {
    ob_start();
    settings_fields($slug);
    return ob_get_clean();
  }
}
