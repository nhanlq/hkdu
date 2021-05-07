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
    return array(
      'abouts' => [
        '#theme' => array('member_article'),
        '#article' => $this->getSharing(),
      ],

      'pager' => [
        '#type' => 'pager',
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
