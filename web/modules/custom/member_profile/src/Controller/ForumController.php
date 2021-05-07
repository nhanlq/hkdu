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
    return array(
      'abouts' => [
        '#theme' => array('member_article'),
        '#article' => $this->getForum(),
      ],

      'pager' => [
        '#type' => 'pager',
      ],
    );
  }

  public function getForum()
  {
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
