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

  /**
   * Index.
   *
   * @return string
   *   Return Hello string.
   */
  public function public() {
    $build['blocks'] = parent::overview('global_hkdu.admin_hkdu_public');
    return $build;
  }

  /**
   * Index.
   *
   * @return string
   *   Return Hello string.
   */
  public function global_config() {
    $build['blocks'] = parent::overview('global_hkdu.admin_hkdu_global_config');
    return $build;
  }

}
