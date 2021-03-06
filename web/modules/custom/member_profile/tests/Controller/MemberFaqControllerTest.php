<?php

namespace Drupal\member_profile\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the member_profile module.
 */
class MemberFaqControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "member_profile MemberFaqController's controller functionality",
      'description' => 'Test Unit for module member_profile and controller MemberFaqController.',
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
  public function testMemberFaqController() {
    // Check that the basic functions of module member_profile.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
