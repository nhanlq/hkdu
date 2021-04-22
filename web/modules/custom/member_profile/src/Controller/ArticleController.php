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
  public function article($uid) {
    return array(
      'abouts' => [
        '#theme' => array('member_article'),
        '#article' => $this->getArticles($uid),
        '#uri' => \Drupal::service('path.current')->getPath(),
        '#uid' => $uid,
        '#current_uid' => \Drupal::currentUser()->id(),
      ],

      'pager' => [
        '#type' => 'pager',
      ],
    );
  }

  public function getArticles($uid)
  {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'member_article')
      ->condition('status', 1)
      ->condition('uid', $uid)
      ->sort('created', 'DESC')
      ->pager(10)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

}
