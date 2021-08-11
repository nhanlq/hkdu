<?php

namespace Drupal\member_profile\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class Committee Controller.
 */
class AdsController extends ControllerBase {

  /**
   * Article.
   *
   * @return array
   *   Return News.
   */
  public function index() {
    $user = \Drupal::currentUser();
    $role = FALSE;
    if (in_array('hkdu_members', $user->getRoles()) || in_array('council_members', $user->getRoles())) {
      $role = TRUE;
    }
    return array(
      'abouts' => [
        '#theme' => array('member_ads'),
        '#article' => $this->getSharing(),
        '#role' => $role,
      ],

      'pager' => [
        '#type' => 'pager',
      ],
      '#cache' => [
        'max-age' => 0,
      ],
    );
  }

  public function getSharing()
  {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'ads')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->pager(10)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

}
