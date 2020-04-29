<?php

namespace Drupal\cme_link\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the cme_link module.
 */
class LinksControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "cme_link LinksController's controller functionality",
      'description' => 'Test Unit for module cme_link and controller LinksController.',
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
   * Tests cme_link functionality.
   */
  public function testLinksController() {
    // Check that the basic functions of module cme_link.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
