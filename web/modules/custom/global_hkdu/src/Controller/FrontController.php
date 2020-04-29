<?php

namespace Drupal\global_hkdu\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class FrontController.
 */
class FrontController extends ControllerBase {

  /**
   * Front.
   *
   * @return string
   *   Return Hello string.
   */
  public function front() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('')
    ];
  }

}
