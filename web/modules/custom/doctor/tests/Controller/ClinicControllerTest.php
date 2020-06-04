<?php

namespace Drupal\doctor\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the doctor module.
 */
class ClinicControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "doctor ClinicController's controller functionality",
      'description' => 'Test Unit for module doctor and controller ClinicController.',
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
   * Tests doctor functionality.
   */
  public function testClinicController() {
    // Check that the basic functions of module doctor.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
