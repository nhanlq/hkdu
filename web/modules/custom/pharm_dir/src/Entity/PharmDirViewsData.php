<?php

namespace Drupal\pharm_dir\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Pharm dir entities.
 */
class PharmDirViewsData extends EntityViewsData {

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
