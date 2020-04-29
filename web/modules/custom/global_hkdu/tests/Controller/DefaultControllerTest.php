<?php

namespace Drupal\global_hkdu\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the global_hkdu module.
 */
class DefaultControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "global_hkdu DefaultController's controller functionality",
      'description' => 'Test Unit for module global_hkdu and controller DefaultController.',
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
  public function testDefaultController() {
    // Check that the basic functions of module global_hkdu.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
