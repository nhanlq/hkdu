<?php

namespace Drupal\news\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the news module.
 */
class NewsListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "news NewsListController's controller functionality",
      'description' => 'Test Unit for module news and controller NewsListController.',
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
   * Tests news functionality.
   */
  public function testNewsListController() {
    // Check that the basic functions of module news.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
