<?php

namespace Drupal\external_link\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for External link entities.
 */
class ExternalLinkViewsData extends EntityViewsData {

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
