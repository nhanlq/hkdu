<?php

namespace Drupal\cme_guidelines\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Guidelines entities.
 */
class GuidelinesViewsData extends EntityViewsData {

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
