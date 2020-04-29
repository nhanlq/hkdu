<?php

namespace Drupal\special_offer\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the special_offer module.
 */
class SpecialListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "special_offer SpecialListController's controller functionality",
      'description' => 'Test Unit for module special_offer and controller SpecialListController.',
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
   * Tests special_offer functionality.
   */
  public function testSpecialListController() {
    // Check that the basic functions of module special_offer.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
