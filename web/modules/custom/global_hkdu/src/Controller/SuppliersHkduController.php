<?php

namespace Drupal\global_hkdu\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\system\Controller\SystemController;

/**
 * Class SuppliersHkduController.
 */
class SuppliersHkduController extends SystemController {

  /**
   * Supplier.
   *
   * @return string
   *   Return Hello string.
   */
  public function supplier() {
    $build['blocks'] = parent::overview('global_hkdu.admin_epharm_hkdu_suppliers');
    return $build;
  }

}
