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

}
