<?php

namespace Drupal\drug_search\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the drug_search module.
 */
class SearchControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "drug_search SearchController's controller functionality",
      'description' => 'Test Unit for module drug_search and controller SearchController.',
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
   * Tests drug_search functionality.
   */
  public function testSearchController() {
    // Check that the basic functions of module drug_search.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
