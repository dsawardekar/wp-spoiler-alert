<?php

namespace WpSpoilerAlert;

class PluginTest extends \WP_UnitTestCase {

  function setUp() {
    parent::setUp();

    $this->plugin = new Plugin('wp-spoiler-alert.php');
  }

  function test_it_is_an_arrow_plugin() {
    $this->assertInstanceOf('Arrow\Plugin', $this->plugin);
  }

  function test_it_can_be_enabled() {
    $this->plugin->enable();
    $this->assertEquals(10, has_action('init', array($this->plugin, 'initFrontEnd')));
  }

  function test_it_can_register_spoiler_shortcode() {
    $this->plugin->initFrontEnd();
    $this->assertTrue(shortcode_exists('spoiler'));
  }

  function test_it_can_register_on_footer_action() {
    $this->plugin->initFrontEnd();
    $this->assertEquals(10, has_action('wp_footer', array($this->plugin, 'onFooter')));
  }

  function test_it_does_not_load_front_end_manifest_if_shortcode_was_not_used() {
    $scriptLoader = $this->plugin->lookup('scriptLoader');
    $this->plugin->onFooter();

    $this->assertFalse($scriptLoader->loaded());
  }

  function test_it_loads_front_end_manifest_if_shortcode_is_present() {
    $shortcode = $this->plugin->lookup('shortcode');
    $shortcode->render(array(), 'foo');

    $scriptLoader = $this->plugin->lookup('scriptLoader');

    $this->plugin->onFooter();
    $this->assertTrue($scriptLoader->loaded());
  }

}
