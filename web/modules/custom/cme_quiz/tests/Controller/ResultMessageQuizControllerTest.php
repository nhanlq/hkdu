<?php

namespace Drupal\cme_quiz\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the cme_quiz module.
 */
class ResultMessageQuizControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "cme_quiz ResultMessageQuizController's controller functionality",
      'description' => 'Test Unit for module cme_quiz and controller ResultMessageQuizController.',
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
   * Tests cme_quiz functionality.
   */
  public function testResultMessageQuizController() {
    // Check that the basic functions of module cme_quiz.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
