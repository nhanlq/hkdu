<?php

namespace Drupal\hospital\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Hospital entity entities.
 */
class HospitalEntityViewsData extends EntityViewsData {

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
