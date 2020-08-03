<?php

namespace Drupal\cme_user\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the cme_user module.
 */
class UserQrcodeControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "cme_user UserQrcodeController's controller functionality",
      'description' => 'Test Unit for module cme_user and controller UserQrcodeController.',
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
   * Tests cme_user functionality.
   */
  public function testUserQrcodeController() {
    // Check that the basic functions of module cme_user.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
