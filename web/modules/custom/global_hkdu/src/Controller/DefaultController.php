<?php

namespace Drupal\global_hkdu\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\system\Controller\SystemController;
/**
 * Class DefaultController.
 */
class DefaultController extends SystemController {

  /**
   * Index.
   *
   * @return string
   *   Return Hello string.
   */
  public function index() {
      $build['blocks'] = parent::overview('global_hkdu.admin_hkdu');
      return $build;
  }

}
