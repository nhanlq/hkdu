<?php

namespace Drupal\cme_score\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Score entities.
 */
class ScoreViewsData extends EntityViewsData {

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
