<?php

namespace Drupal\media_release\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the media_release module.
 */
class MediaListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "media_release MediaListController's controller functionality",
      'description' => 'Test Unit for module media_release and controller MediaListController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests media_release functionality.
   */
  public function testMediaListController() {
    // Check that the basic functions of module media_release.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
