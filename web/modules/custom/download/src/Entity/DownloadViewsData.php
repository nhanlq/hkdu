<?php

namespace Drupal\download\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Download entities.
 */
class DownloadViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
