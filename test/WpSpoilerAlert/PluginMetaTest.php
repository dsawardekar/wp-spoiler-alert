<?php

namespace WpSpoilerAlert;

use Encase\Container;

class PluginMetaTest extends \WP_UnitTestCase {

  public $container;
  public $meta;

  function setUp() {
    parent::setUp();

    $this->container = new Container();
    $this->container
      ->object('pluginMeta', new \WpSpoilerAlert\PluginMeta('wp-spoiler-alert.php'))
      ->packager('optionsPackager', 'Arrow\Options\Packager');

    $this->meta = $this->container->lookup('pluginMeta');
    $this->store = $this->container->lookup('optionsStore');
  }

  function test_it_is_an_arrow_plugin_meta() {
    $this->assertInstanceOf('Arrow\PluginMeta', $this->meta);
  }

  function test_it_has_a_version() {
    $this->assertEquals(Version::$version, $this->meta->version);
  }

  function test_it_has_valid_default_options() {
    $actual = $this->meta->getDefaultOptions();

    $this->assertArrayHasKey('max', $actual);
    $this->assertArrayHasKey('partial', $actual);
    $this->assertArrayHasKey('tooltip', $actual);
    $this->assertArrayHasKey('custom', $actual);
  }

  function test_it_knows_it_cannot_load_custom_css_if_custom_is_false() {
    $this->store->setOption('custom', false);
    $this->assertFalse($this->meta->canLoadCustomCSS());
  }

  function test_it_knows_it_can_load_custom_stylesheet_is_absent() {
    $this->store->setOption('custom', true);
    $this->assertFalse($this->meta->canLoadCustomCSS());
  }

  function test_it_knows_it_can_load_custom_stylesheet_if_custom_has_stylesheet_is_present() {
    $this->store->setOption('custom', true);
    $meta = \Mockery::mock('WpSpoilerAlert\PluginMeta[hasCustomStylesheet]', array('wp-spoiler-alert.php'));
    $meta
      ->shouldReceive('hasCustomStylesheet')
      ->withNoArgs()
      ->andReturn(true);

    $this->container->inject($meta);
    $this->assertTrue($meta->canLoadCustomCSS());
  }

  function test_it_has_an_options_context() {
    $this->store->setOption('max', 100);
    $this->store->setOption('partial', 200);
    $this->store->setOption('tooltip', 'lorem');
    $this->store->setOption('custom', false);

    $actual = $this->meta->getOptionsContext();

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
    $actual = $meta->getOptionsContext();
    $this->assertFalse($actual['custom']);
  }

}
