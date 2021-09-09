<?php

namespace Drupal\ads_tracking\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the ads_tracking module.
 */
class TrackControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "ads_tracking TrackController's controller functionality",
      'description' => 'Test Unit for module ads_tracking and controller TrackController.',
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
   * Tests ads_tracking functionality.
   */
  public function testTrackController() {
    // Check that the basic functions of module ads_tracking.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
