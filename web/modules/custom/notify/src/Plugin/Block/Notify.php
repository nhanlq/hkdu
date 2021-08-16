<?php

namespace Drupal\notify\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Notify' block.
 *
 * @Block(
 *  id = "notify",
 *  admin_label = @Translation("Notify"),
 * )
 */
class Notify extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => ['notify_alert'],
      '#count' => $this->getCountNotify(),
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * @return int|void
   */
  public function getCountNotify(){
    $user = \Drupal::currentUser();
    $ids = \Drupal::entityQuery('notify')
      ->condition('status',1)
      ->condition('field_user',$user->id())
      ->execute();
    return count($ids);
  }

}
