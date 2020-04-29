<?php

namespace Drupal\cme_event_organizer\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Event Organizer entities.
 */
class EventOrgViewsData extends EntityViewsData {

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
