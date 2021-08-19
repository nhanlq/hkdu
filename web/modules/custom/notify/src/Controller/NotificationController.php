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
      ->condition('field_user',$user->id())
      ->range(0,20)
      ->sort('created','DESC')
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
