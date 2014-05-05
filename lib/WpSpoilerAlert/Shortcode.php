<?php

namespace WpSpoilerAlert;

class Shortcode {

  protected $didRender = false;

  function render($params, $content) {
    $this->didRender = true;
    return "<div class='spoiler'>$content</div>";
  }

  function isPresent() {
    return $this->didRender;
  }

}
