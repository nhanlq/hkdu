<?php

namespace Drupal\notify\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Notify entities.
 */
class NotifyViewsData extends EntityViewsData {

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
