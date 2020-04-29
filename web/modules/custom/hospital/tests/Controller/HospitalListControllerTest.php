<?php

namespace Drupal\hospital\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the hospital module.
 */
class HospitalListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "hospital HospitalListController's controller functionality",
      'description' => 'Test Unit for module hospital and controller HospitalListController.',
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
   * Tests hospital functionality.
   */
  public function testHospitalListController() {
    // Check that the basic functions of module hospital.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
