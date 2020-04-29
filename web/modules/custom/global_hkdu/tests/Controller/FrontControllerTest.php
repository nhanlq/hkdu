<?php

namespace Drupal\global_hkdu\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the global_hkdu module.
 */
class FrontControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "global_hkdu FrontController's controller functionality",
      'description' => 'Test Unit for module global_hkdu and controller FrontController.',
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
   * Tests global_hkdu functionality.
   */
  public function testFrontController() {
    // Check that the basic functions of module global_hkdu.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
