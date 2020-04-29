<?php

namespace Drupal\tools\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the tools module.
 */
class ToolListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "tools ToolListController's controller functionality",
      'description' => 'Test Unit for module tools and controller ToolListController.',
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
   * Tests tools functionality.
   */
  public function testToolListController() {
    // Check that the basic functions of module tools.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
