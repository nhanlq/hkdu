<?php

namespace Drupal\notify\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class NotificationController.
 */
class NotificationController extends ControllerBase {

  /**
   * Notification.
   *
   * @return string
   *   Return Hello string.
   */
  public function notification() {
    $user = \Drupal::currentUser();
    $ids = \Drupal::entityQuery('notify')
      ->condition('status',1)
      ->condition('field_user',$user->id())
      ->execute();
    $notify = \Drupal\notify\Entity\Notify::loadMultiple($ids);
    return [
      '#theme' => ['notify_list'],
      '#notifies' => $notify,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

}
