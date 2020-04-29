<?php

namespace Drupal\drug_search\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Drug search entities.
 */
class DrugSearchViewsData extends EntityViewsData {

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
