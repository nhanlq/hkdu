<?php

namespace Drupal\cme\Controller;

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
      $build['blocks'] = parent::overview('cme.admin_cme');
      return $build;
  }

}
