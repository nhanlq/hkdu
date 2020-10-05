<?php

namespace Drupal\cme_user\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class UserQrcodeController.
 */
class UserQrcodeController extends ControllerBase {

  /**
   * Qrcode.
   *
   * @return string
   *   Return Hello string.
   */
  public function qrcode($uid) {
      $user = \Drupal\user\Entity\User::load($uid);

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: qrcode with parameter(s): $uid'),
    ];
  }

}
