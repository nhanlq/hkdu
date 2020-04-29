<?php

namespace Drupal\media_release\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Media entity entities.
 */
class MediaEntityViewsData extends EntityViewsData {

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
