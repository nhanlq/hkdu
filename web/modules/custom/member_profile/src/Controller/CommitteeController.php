<?php

namespace Drupal\member_profile\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class Committee Controller.
 */
class CommitteeController extends ControllerBase {

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
        '#article' => $this->getCommittee(),
      ],

      'pager' => [
        '#type' => 'pager',
      ],
    );
  }

  public function getCommittee()
  {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'committee_news')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->pager(10)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

}
