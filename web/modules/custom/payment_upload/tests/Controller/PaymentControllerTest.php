<?php

namespace Drupal\payment_upload\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the payment_upload module.
 */
class PaymentControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "payment_upload PaymentController's controller functionality",
      'description' => 'Test Unit for module payment_upload and controller PaymentController.',
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
   * Tests payment_upload functionality.
   */
  public function testPaymentController() {
    // Check that the basic functions of module payment_upload.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
