<?php

namespace WpSpoilerAlert;

use WordPress\Logger;

class OptionPage {

  function needs() {
    return array('twigHelper', 'optionStore', 'pluginSlug');
  }

  function getPageTitle() {
    return 'WP Spoiler Alert | Settings';
  }

  function getMenuTitle() {
    return 'WP Spoiler Alert';
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
      'max'     => $this->optionStore->getOption('max'),
      'partial' => $this->optionStore->getOption('partial'),
      'tooltip' => $this->optionStore->getOption('tooltip'),
      'custom'  => $this->optionStore->getOption('custom') ? 'checked' : '',
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
