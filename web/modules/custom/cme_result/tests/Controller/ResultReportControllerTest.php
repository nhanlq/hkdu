<?php

namespace Drupal\cme_result\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the cme_result module.
 */
class ResultReportControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "cme_result ResultReportController's controller functionality",
      'description' => 'Test Unit for module cme_result and controller ResultReportController.',
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
   * Tests cme_result functionality.
   */
  public function testResultReportController() {
    // Check that the basic functions of module cme_result.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
