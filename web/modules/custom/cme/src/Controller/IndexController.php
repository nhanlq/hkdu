<?php

namespace Drupal\cme\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class IndexController.
 */
class IndexController extends ControllerBase {

  /**
   * Index.
   *
   * @return string
   *   Return Hello string.
   */
  public function index() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('')
    ];
  }

}
