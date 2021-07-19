<?php

namespace Drupal\adama_country_selector\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class CountrySelectorController.
 */
class CountrySelectorController extends ControllerBase {

  /**
   * Index.
   *
   * @return string
   *   Return Hello string.
   */
  public function index() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: index')
    ];
  }

}
