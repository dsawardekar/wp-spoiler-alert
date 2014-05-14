<?php

namespace WpSpoilerAlert;

class Shortcode {

  public $optionsStore;

  protected $didRender = false;
  protected $defaults = array(
    'mode' => 'block'
  );

  function needs() {
    return array('optionsStore');
  }

  function render($params, $content) {
    $params = $this->parse($params);
    $tag    = $this->tagFor($params['mode']);
    $title  = $this->optionsStore->getOption('tooltip');

    $this->didRender = true;

    return $this->wrap($content, $tag, $title);
  }

  function wrap($content, $tag, $title) {
    return "<$tag class='spoiler-hidden' title='$title'>$content</$tag>";
  }

  function parse($params) {
    return shortcode_atts($this->defaults, $params);
  }

  function tagFor($mode) {
    return $mode === 'block' ? 'div' : 'span';
  }

  function isPresent() {
    return $this->didRender;
  }

}
