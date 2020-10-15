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
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: report with parameter(s): $id'),
    ];
  }

}
