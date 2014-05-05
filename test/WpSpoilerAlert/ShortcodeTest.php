<?php

namespace WpSpoilerAlert;

class ShortcodeTest extends \WP_UnitTestCase {

  function setUp() {
    parent::setUp();
    $this->shortcode = new Shortcode();
  }

  function test_it_can_wrap_text_with_spoiler() {
    $html = "<b>foo</b>";
    $actual = $this->shortcode->render(array(), $html);
    $matcher = array(
      'tag' => 'div',
      'attributes' => array('class' => 'spoiler')
    );

    $this->assertTag($matcher, $actual);
  }

  function test_it_can_wrap_image_with_spoiler() {
    $html = "<p><img src='foo.jpg' /></p>";
    $actual = $this->shortcode->render(array(), $html);
    $matcher = array(
      'tag' => 'div',
      'attributes' => array('class' => 'spoiler')
    );

    $this->assertTag($matcher, $actual);
  }

  function test_it_knows_if_shortcode_is_present() {
    $this->shortcode->render(array(), 'foo');
    $this->assertTrue($this->shortcode->isPresent());
  }

  function test_it_knows_if_shortcode_is_absent() {
    $this->assertFalse($this->shortcode->isPresent());
  }

}
