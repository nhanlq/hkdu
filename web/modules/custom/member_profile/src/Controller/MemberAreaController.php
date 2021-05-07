<?php

namespace Drupal\member_profile\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class MemberAreaController.
 */
class MemberAreaController extends ControllerBase {

  /**
   * Member_area.
   *
   * @return string
   *   Return Hello string.
   */
  public function member_area() {

    return [
        '#theme' => array('member_area'),
        '#news' => $this->getCouncil(),
        '#forum' => $this->getmemberForum(),
        '#bulletin' => $this->getBulletin()
      ];
  }

  /**
   * Get council News
   *
   * @return array
   */
  public function getCouncil(){
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'member_article')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->range(0,3)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

  /**
   * Get member Forum
   *
   * @return array
   */
  public function getmemberForum(){
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'forum')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->range(0,3)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

  /**
   * Get Bulletins
   *
   * @return array
   */
  public function getBulletin(){
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'bulletin')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->range(0,3)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }
}
