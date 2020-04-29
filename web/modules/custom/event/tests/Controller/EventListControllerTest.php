<?php

namespace Drupal\event\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the event module.
 */
class EventListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "event EventListController's controller functionality",
      'description' => 'Test Unit for module event and controller EventListController.',
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
   * Tests event functionality.
   */
  public function testEventListController() {
    // Check that the basic functions of module event.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
