<?php

namespace Drupal\drug_news\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the drug_news module.
 */
class DrugNewsListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "drug_news DrugNewsListController's controller functionality",
      'description' => 'Test Unit for module drug_news and controller DrugNewsListController.',
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
   * Tests drug_news functionality.
   */
  public function testDrugNewsListController() {
    // Check that the basic functions of module drug_news.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
