<?php

namespace WpSpoilerAlert;

use Encase\Container;

class ShortcodeTest extends \WP_UnitTestCase {

  public $container;
  public $shortcode;
  public $pluginMeta;

  function setUp() {
    parent::setUp();

    $this->pluginMeta = new PluginMeta('wp-spoiler-alert.php');
    $this->container = new Container();
    $this->container
      ->object('pluginMeta', $this->pluginMeta)
      ->object('optionsManager', new \Arrow\OptionsManager\OptionsManager($this->container))
      ->singleton('shortcode', 'WpSpoilerAlert\Shortcode');


    $this->shortcode = $this->container->lookup('shortcode');
  }

  function test_it_can_wrap_text_with_spoiler() {
    $html = "<b>foo</b>";
    $actual = $this->shortcode->render(array(), $html);
    $matcher = array(
      'tag' => 'div',
      'attributes' => array('class' => 'spoiler-hidden')
    );

    $this->assertTag($matcher, $actual);
  }

  function test_it_can_wrap_image_with_spoiler() {
    $html = "<p><img src='foo.jpg' /></p>";
    $actual = $this->shortcode->render(array(), $html);
    $matcher = array(
      'tag' => 'div',
      'attributes' => array('class' => 'spoiler-hidden')
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
