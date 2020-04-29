<?php

namespace Drupal\global_hkdu\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class SuppliersHkduController.
 */
class SuppliersHkduController extends ControllerBase {

  /**
   * Supplier.
   *
   * @return string
   *   Return Hello string.
   */
  public function supplier() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: supplier')
    ];
  }

}
