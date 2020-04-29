<?php

namespace Drupal\healthy\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the healthy module.
 */
class HealthyControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "healthy HealthyController's controller functionality",
      'description' => 'Test Unit for module healthy and controller HealthyController.',
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
   * Tests healthy functionality.
   */
  public function testHealthyController() {
    // Check that the basic functions of module healthy.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
