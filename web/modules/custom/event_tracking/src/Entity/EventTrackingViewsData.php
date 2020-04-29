<?php

namespace Drupal\event_tracking\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Event tracking entities.
 */
class EventTrackingViewsData extends EntityViewsData {

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
