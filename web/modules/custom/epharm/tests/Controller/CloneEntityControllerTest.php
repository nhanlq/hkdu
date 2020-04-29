<?php

namespace Drupal\epharm\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the epharm module.
 */
class CloneEntityControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "epharm CloneEntityController's controller functionality",
      'description' => 'Test Unit for module epharm and controller CloneEntityController.',
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
  public function testCloneEntityController() {
    // Check that the basic functions of module epharm.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
