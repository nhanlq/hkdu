<?php

namespace Drupal\about\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the about module.
 */
class AboutControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "about AboutController's controller functionality",
      'description' => 'Test Unit for module about and controller AboutController.',
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
   * Tests about functionality.
   */
  public function testAboutController() {
    // Check that the basic functions of module about.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
