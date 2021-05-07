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
  public function faq() {
    return [
      '#theme' => ['member_faq'],
      '#faq' => $this->getFAQ(),
    ];
  }

  /**
   * @param $uid
   *
   * @return \Drupal\node\Entity\Node
   */
  public function getFAQ() {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'faq')
      ->condition('status', 1)
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $nodes;
  }

}
