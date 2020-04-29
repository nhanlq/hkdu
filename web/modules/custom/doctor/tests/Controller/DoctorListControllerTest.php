<?php

namespace Drupal\doctor\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the doctor module.
 */
class DoctorListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "doctor DoctorListController's controller functionality",
      'description' => 'Test Unit for module doctor and controller DoctorListController.',
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
  public function testDoctorListController() {
    // Check that the basic functions of module doctor.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
