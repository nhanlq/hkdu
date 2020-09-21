<?php

namespace Drupal\cme_event\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the cme_event module.
 */
class EventCalendarControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "cme_event EventCalendarController's controller functionality",
      'description' => 'Test Unit for module cme_event and controller EventCalendarController.',
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
  public function testEventCalendarController() {
    // Check that the basic functions of module cme_event.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
