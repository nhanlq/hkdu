<?php

namespace Drupal\cme_event_organizer\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the cme_event_organizer module.
 */
class OrgListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "cme_event_organizer OrgListController's controller functionality",
      'description' => 'Test Unit for module cme_event_organizer and controller OrgListController.',
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
   * Tests cme_event_organizer functionality.
   */
  public function testOrgListController() {
    // Check that the basic functions of module cme_event_organizer.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
