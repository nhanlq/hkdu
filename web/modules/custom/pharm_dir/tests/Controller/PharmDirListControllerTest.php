<?php

namespace Drupal\pharm_dir\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the pharm_dir module.
 */
class PharmDirListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "pharm_dir PharmDirListController's controller functionality",
      'description' => 'Test Unit for module pharm_dir and controller PharmDirListController.',
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
   * Tests pharm_dir functionality.
   */
  public function testPharmDirListController() {
    // Check that the basic functions of module pharm_dir.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
