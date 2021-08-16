<?php

namespace Drupal\notify\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class UpdateController.
 */
class UpdateController extends ControllerBase {

  /**
   * Update.
   *
   * @return string
   *   Return Hello string.
   */
  public function update($id) {
    $notify = \Drupal\notify\Entity\Notify::load($id);
    $notify->set('status', 0);
    $notify->save();
    return [
      '#type' => 'markup',
      '#markup' => 'ok'
    ];
  }

}
