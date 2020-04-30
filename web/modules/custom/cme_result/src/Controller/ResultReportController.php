<?php

namespace Drupal\cme_result\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ResultReportController.
 */
class ResultReportController extends ControllerBase {

  /**
   * Report.
   *
   * @return string
   *   Return Hello string.
   */
  public function report() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: report')
    ];
  }

}
