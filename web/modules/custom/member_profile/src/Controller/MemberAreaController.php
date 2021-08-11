<?php

namespace Drupal\member_profile\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
    $user = \Drupal::currentUser();
    if (in_array('drug_suppliers', $user->getRoles())) {
      $redirect = new RedirectResponse(\Drupal\Core\Url::fromUserInput('/member-area/drug-databases')->toString());
      $redirect->send();
    }

    return [
      '#theme' => ['member_area'],
      '#news' => $this->getCouncil(),
      '#forum' => $this->getmemberForum(),
      '#bulletin' => $this->getBulletin(),
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * Get council News
   *
   * @return array
   */
  public function getCouncil() {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'member_article')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->range(0, 3)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

  /**
   * Get member Forum
   *
   * @return array
   */
  public function getmemberForum() {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'forum')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->range(0, 3)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

  /**
   * Get Bulletins
   *
   * @return array
   */
  public function getBulletin() {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'bulletin')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->range(0, 3)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

  public function memberadmin() {
    return [
      '#theme' => ['member_admin'],
    ];
  }
}
