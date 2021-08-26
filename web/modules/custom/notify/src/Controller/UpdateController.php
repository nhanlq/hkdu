<?php

namespace Drupal\notify\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
    $url = $_GET['destination'];
    $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput($url)->toString());;
    $redirect->send();
    return [
      '#type' => 'markup',
      '#markup' => 'ok'
    ];
  }

}
