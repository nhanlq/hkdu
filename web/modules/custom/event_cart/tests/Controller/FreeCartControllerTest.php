<?php

namespace Drupal\event_cart\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the event_cart module.
 */
class FreeCartControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "event_cart FreeCartController's controller functionality",
      'description' => 'Test Unit for module event_cart and controller FreeCartController.',
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
   * Tests event_cart functionality.
   */
  public function testFreeCartController() {
    // Check that the basic functions of module event_cart.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
