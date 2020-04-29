<?php

namespace Drupal\cme_guidelines\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the cme_guidelines module.
 */
class GuideListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "cme_guidelines GuideListController's controller functionality",
      'description' => 'Test Unit for module cme_guidelines and controller GuideListController.',
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
   * Tests cme_guidelines functionality.
   */
  public function testGuideListController() {
    // Check that the basic functions of module cme_guidelines.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
