<?php

namespace Drupal\cme_event\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class FreeEventController.
 */
class FreeEventController extends ControllerBase {

  /**
   * Free.
   *
   * @return string
   *   Return Hello string.
   */
  public function free($eventId) {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: free with parameter(s): $eventId'),
    ];
  }

}
