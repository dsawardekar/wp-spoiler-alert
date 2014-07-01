<?php

namespace WpSpoilerAlert;

use Encase\Container;

class FrontEndManifestTest extends \WP_UnitTestCase {

  public $container;
  public $meta;
  public $store;
  public $shortcode;
  public $manifest;

  function setUp() {
    parent::setUp();

    $this->container = new Container();
    $this->container
      ->object('pluginMeta', new \WpSpoilerAlert\PluginMeta('wp-spoiler-alert.php'))
      ->packager('optionsPackager', 'Arrow\Options\Packager')
      ->singleton('shortcode', 'WpSpoilerAlert\Shortcode')
      ->singleton('frontEndManifest', 'WpSpoilerAlert\FrontEndManifest');

    $this->meta      = $this->container->lookup('pluginMeta');
    $this->store     = $this->container->lookup('optionsStore');
    $this->shortcode = $this->container->lookup('shortcode');
    $this->manifest  = $this->container->lookup('frontEndManifest');
  }

  function test_it_has_a_plugin_meta() {
    $this->assertSame($this->meta, $this->manifest->pluginMeta);
  }

  function test_it_has_options_store() {
    $this->assertSame($this->store, $this->manifest->optionsStore);
  }

  function test_it_has_shortcode() {
    $this->assertSame($this->shortcode, $this->manifest->shortcode);
  }

  function test_it_knows_if_shortcode_is_absent() {
    $this->assertFalse($this->manifest->hasShortcode());
  }

  function test_it_knows_if_shortcode_is_present() {
    $this->shortcode->render(array(), 'foo');
    $this->assertTrue($this->manifest->hasShortcode());
  }

  function test_it_does_not_have_scripts_if_shortcode_is_absent() {
    $this->assertEmpty($this->manifest->getScripts());
  }

  function test_it_has_scripts_if_shortcode_is_present() {
    $this->shortcode->render(array(), 'foo');
    $actual = $this->manifest->getScripts();
    $this->assertEquals(array('wp-spoiler-alert'), $actual);
  }

  function test_it_does_not_have_styles_if_shortcode_is_absent() {
    $this->assertEmpty($this->manifest->getStyles());
  }

  function test_it_does_not_have_styles_if_custom_option_is_false() {
    $this->shortcode->render(array(), 'foo');
    $this->store->setOption('custom', false);
    $this->assertEmpty($this->manifest->getStyles());
  }

  function test_it_does_not_have_styles_if_custom_stylesheet_is_absent() {
    $this->shortcode->render(array(), 'foo');
    $this->store->setOption('custom', true);
    $meta = \Mockery::mock('WpSpoilerAlert\PluginMeta[hasCustomStylesheet]', array('wp-spoiler-alert.php'));
    $meta->shouldReceive('hasCustomStylesheet')
      ->withNoArgs()
      ->andReturn(false);

    $this->manifest->pluginMeta = $meta;
    $this->assertEmpty($this->manifest->getStyles());
  }

  function test_it_has_custom_styles_if_needed() {
    $this->shortcode->render(array(), 'foo');
    $this->store->setOption('custom', true);
    $meta = \Mockery::mock('WpSpoilerAlert\PluginMeta[hasCustomStylesheet]', array('wp-spoiler-alert.php'));
    $meta->shouldReceive('hasCustomStylesheet')
      ->withNoArgs()
      ->andReturn(true);

    $this->manifest->pluginMeta = $meta;
    $this->assertEquals(array('theme-custom'), $this->manifest->getStyles());
  }

}
