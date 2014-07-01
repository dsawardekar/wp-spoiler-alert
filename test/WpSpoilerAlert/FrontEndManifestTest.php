<?php

namespace WpSpoilerAlert;

use Encase\Container;

class FrontEndManifestTest extends \WP_UnitTestCase {

  public $container;
  public $meta;
  public $store;
  public $manifest;

  function setUp() {
    parent::setUp();

    $this->container = new Container();
    $this->container
      ->object('pluginMeta', new \WpSpoilerAlert\PluginMeta('wp-spoiler-alert.php'))
      ->packager('optionsPackager', 'Arrow\Options\Packager')
      ->singleton('frontEndManifest', 'WpSpoilerAlert\FrontEndManifest');

    $this->meta      = $this->container->lookup('pluginMeta');
    $this->store     = $this->container->lookup('optionsStore');
    $this->manifest  = $this->container->lookup('frontEndManifest');
  }

  function test_it_has_a_plugin_meta() {
    $this->assertSame($this->meta, $this->manifest->pluginMeta);
  }

  function test_it_has_options_store() {
    $this->assertSame($this->store, $this->manifest->optionsStore);
  }

  function test_it_has_a_callable_context() {
    $this->assertTrue($this->manifest->hasContext());
  }

  function test_it_has_scripts() {
    $actual = $this->manifest->getScripts();
    $this->assertEquals(array('jquery-spoiler', 'wp-spoiler-alert-plugin'), $actual);
  }

  function test_it_does_not_have_styles_if_custom_option_is_false() {
    $this->store->setOption('custom', false);
    $this->assertEmpty($this->manifest->getStyles());
  }

  function test_it_does_not_have_styles_if_custom_stylesheet_is_absent() {
    $this->store->setOption('custom', true);
    $meta = \Mockery::mock('WpSpoilerAlert\PluginMeta[hasCustomStylesheet]', array('wp-spoiler-alert.php'));
    $meta->shouldReceive('hasCustomStylesheet')
      ->withNoArgs()
      ->andReturn(false);

    $this->manifest->pluginMeta = $meta;
    $this->assertEmpty($this->manifest->getStyles());
  }

  function test_it_has_custom_styles_if_needed() {
    $this->store->setOption('custom', true);
    $meta = \Mockery::mock('WpSpoilerAlert\PluginMeta[hasCustomStylesheet]', array('wp-spoiler-alert.php'));
    $meta->shouldReceive('hasCustomStylesheet')
      ->withNoArgs()
      ->andReturn(true);

    $this->manifest->pluginMeta = $meta;
    $this->assertEquals(array('theme-custom'), $this->manifest->getStyles());
  }

  function test_it_has_a_frontend_context() {
    $this->store->setOption('max', 100);
    $this->store->setOption('partial', 200);
    $this->store->setOption('tooltip', 'lorem');
    $this->store->setOption('custom', false);

    $actual = $this->manifest->getFrontEndContext();

    $this->assertEquals(100, $actual['max']);
    $this->assertEquals(200, $actual['partial']);
    $this->assertEquals('lorem', $actual['tooltip']);
    $this->assertEquals(false, $actual['custom']);
  }

  function test_it_overrides_custom_if_cannot_load_custom_css() {
    $this->store->setOption('custom', true);
    $meta = \Mockery::mock('WpSpoilerAlert\PluginMeta[hasCustomStylesheet]', array('wp-spoiler-alert.php'));
    $meta
      ->shouldReceive('hasCustomStylesheet')
      ->withNoArgs()
      ->andReturn(false);

    $this->container->inject($meta);
    $this->manifest->pluginMeta = $meta;
    $actual = $this->manifest->getFrontEndContext();
    $this->assertFalse($actual['custom']);
  }

}
