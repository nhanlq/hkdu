<?php

namespace Drupal\epharm\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the epharm module.
 */
class AdminControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "epharm AdminController's controller functionality",
      'description' => 'Test Unit for module epharm and controller AdminController.',
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
  public function testAdminController() {
    // Check that the basic functions of module epharm.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
