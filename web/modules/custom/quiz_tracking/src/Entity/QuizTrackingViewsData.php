<?php

namespace Drupal\quiz_tracking\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Quiz tracking entities.
 */
class QuizTrackingViewsData extends EntityViewsData {

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
