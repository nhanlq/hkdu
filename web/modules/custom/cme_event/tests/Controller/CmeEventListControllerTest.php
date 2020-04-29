<?php

namespace Drupal\cme_event\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the cme_event module.
 */
class CmeEventListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "cme_event CmeEventListController's controller functionality",
      'description' => 'Test Unit for module cme_event and controller CmeEventListController.',
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
   * Tests cme_event functionality.
   */
  public function testCmeEventListController() {
    // Check that the basic functions of module cme_event.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
