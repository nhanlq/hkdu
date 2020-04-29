<?php

namespace Drupal\banner\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Banner entity entities.
 */
class BannerEntityViewsData extends EntityViewsData {

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
