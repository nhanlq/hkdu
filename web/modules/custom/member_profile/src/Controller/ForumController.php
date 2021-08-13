<?php

namespace Drupal\member_profile\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ArticleController.
 */
class ForumController extends ControllerBase {

  /**
   * Article.
   *
   * @return string
   *   Return Hello string.
   */
  public function forum() {
    $user = \Drupal::currentUser();
    $role = FALSE;
    if (in_array('hkdu_members', $user->getRoles()) || in_array('council_members', $user->getRoles()) || in_array('admins', $user->getRoles()) || in_array('administrator', $user->getRoles())) {
      $role = TRUE;
    }
    return [
      'abouts' => [
        '#theme' => ['member_forum'],
        '#article' => $this->getForum(),
        '#role' => $role
      ],

      'pager' => [
        '#type' => 'pager',
      ],
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  public function getForum() {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'forum')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->pager(10)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

}
