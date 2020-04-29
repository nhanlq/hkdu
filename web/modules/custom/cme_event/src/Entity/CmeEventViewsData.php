<?php

namespace Drupal\cme_event\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for CME Event entities.
 */
class CmeEventViewsData extends EntityViewsData {

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
