<?php

namespace Drupal\notify\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the notify module.
 */
class UpdateControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "notify UpdateController's controller functionality",
      'description' => 'Test Unit for module notify and controller UpdateController.',
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
   * Tests notify functionality.
   */
  public function testUpdateController() {
    // Check that the basic functions of module notify.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
