<?php

namespace Drupal\member_profile\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class MemberFaqController.
 */
class MemberFaqController extends ControllerBase {

  /**
   * Faq.
   *
   * @return string
   *   Return Hello string.
   */
  public function faq($uid) {
    return [
      '#theme' => ['member_faq'],
      '#faq' => $this->getFAQ($uid),
      '#uri' => \Drupal::service('path.current')->getPath(),
      '#uid' => $uid,
      '#current_uid' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * @param $uid
   *
   * @return \Drupal\node\Entity\Node
   */
  public function getFAQ($uid) {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'faq')
      ->condition('uid', $uid)
      ->condition('status', 1)
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $nodes;
  }

}
