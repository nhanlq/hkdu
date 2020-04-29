<?php

namespace Drupal\download\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the download module.
 */
class DownloadListControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "download DownloadListController's controller functionality",
      'description' => 'Test Unit for module download and controller DownloadListController.',
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
   * Tests download functionality.
   */
  public function testDownloadListController() {
    // Check that the basic functions of module download.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
