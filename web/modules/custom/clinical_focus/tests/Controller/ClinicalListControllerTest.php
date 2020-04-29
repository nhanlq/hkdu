<?php

namespace Drupal\clinical_focus\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the clinical_focus module.
 */
class ClinicalListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "clinical_focus ClinicalListController's controller functionality",
      'description' => 'Test Unit for module clinical_focus and controller ClinicalListController.',
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
   * Tests clinical_focus functionality.
   */
  public function testClinicalListController() {
    // Check that the basic functions of module clinical_focus.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
