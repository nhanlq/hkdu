<?php

namespace Drupal\epharm\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class DefaultController.
 */
class DefaultController extends ControllerBase {

  /**
   * Index.
   *
   * @return string
   *   Return Hello string.
   */
  public function index() {
      if(\Drupal::currentUser()->isAnonymous()){
          $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/user/login')->toString());;
          $redirect->send();
      }
    return [
      '#type' => 'markup',
      '#markup' => $this->t('')
    ];
  }

}
