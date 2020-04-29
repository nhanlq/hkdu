<?php

namespace Drupal\epharm\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class AdminController.
 */
class AdminController extends ControllerBase {

  /**
   * Admin.
   *
   * @return string
   *   Return Hello string.
   */
  public function admin() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: admin')
    ];
  }

}
