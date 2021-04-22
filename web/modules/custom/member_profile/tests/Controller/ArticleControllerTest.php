<?php

namespace Drupal\member_profile\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the member_profile module.
 */
class ArticleControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "member_profile ArticleController's controller functionality",
      'description' => 'Test Unit for module member_profile and controller ArticleController.',
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
   * Tests member_profile functionality.
   */
  public function testArticleController() {
    // Check that the basic functions of module member_profile.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
