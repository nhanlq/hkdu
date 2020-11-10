<?php

namespace Drupal\cme_user\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ReportController.
 */
class ReportController extends ControllerBase {

  /**
   * Report.
   *
   * @return string
   *   Return Hello string.
   */
  public function report($uid) {
    return [
      '#theme' => 'user_report',
     // '#user' => $user,
     // '#qrcode' => $code,
    ];
  }

}
