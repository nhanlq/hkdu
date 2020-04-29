<?php

namespace Drupal\gallery\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the gallery module.
 */
class GalleryListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "gallery GalleryListController's controller functionality",
      'description' => 'Test Unit for module gallery and controller GalleryListController.',
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
   * Tests gallery functionality.
   */
  public function testGalleryListController() {
    // Check that the basic functions of module gallery.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
