<?php

namespace Drupal\doctor\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Doctor entity entities.
 */
class DoctorEntityViewsData extends EntityViewsData {

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
