<?php

namespace Drupal\member_profile\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ArticleController.
 */
class ArticleController extends ControllerBase {

  /**
   * Article.
   *
   * @return string
   *   Return Hello string.
   */
  public function article() {
    return array(
      'abouts' => [
        '#theme' => array('member_article'),
        '#article' => $this->getArticles(),
      ],

      'pager' => [
        '#type' => 'pager',
      ],
    );
  }

  public function getArticles()
  {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'member_article')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->pager(10)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

}
