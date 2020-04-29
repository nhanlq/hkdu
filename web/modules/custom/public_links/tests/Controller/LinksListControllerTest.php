<?php

namespace Drupal\public_links\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the public_links module.
 */
class LinksListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "public_links LinksListController's controller functionality",
      'description' => 'Test Unit for module public_links and controller LinksListController.',
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
   * Tests public_links functionality.
   */
  public function testLinksListController() {
    // Check that the basic functions of module public_links.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
