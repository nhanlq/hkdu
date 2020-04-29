<?php

namespace Drupal\epharm\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the epharm module.
 */
class DefaultControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "epharm DefaultController's controller functionality",
      'description' => 'Test Unit for module epharm and controller DefaultController.',
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
   * Tests epharm functionality.
   */
  public function testDefaultController() {
    // Check that the basic functions of module epharm.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
