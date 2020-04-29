<?php

namespace Drupal\external_link\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the external_link module.
 */
class ExternalLinkListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "external_link ExternalLinkListController's controller functionality",
      'description' => 'Test Unit for module external_link and controller ExternalLinkListController.',
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
   * Tests external_link functionality.
   */
  public function testExternalLinkListController() {
    // Check that the basic functions of module external_link.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
