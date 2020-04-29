<?php

namespace Drupal\cme\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the cme module.
 */
class DefaultControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "cme DefaultController's controller functionality",
      'description' => 'Test Unit for module cme and controller DefaultController.',
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
   * Tests cme functionality.
   */
  public function testDefaultController() {
    // Check that the basic functions of module cme.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
