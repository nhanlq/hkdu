<?php

namespace Drupal\epharm\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\system\Controller\SystemController;

/**
 * Class AdminController.
 */
class AdminController extends SystemController {

  /**
   * Admin.
   *
   * @return string
   *   Return Hello string.
   */
  public function admin() {
    $build['blocks'] = parent::overview('global_hkdu.admin_epharm_hkdu');
    return $build;
  }

}
