<?php

namespace Drupal\member_profile\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ArticleController.
 */
class BulletinController extends ControllerBase {

  /**
   * Article.
   *
   * @return string
   *   Return Hello string.
   */
  public function index() {
    return array(
      'abouts' => [
        '#theme' => array('member_bulletin'),
        '#bulletin' => $this->getBulletin(),
      ],

      'pager' => [
        '#type' => 'pager',
      ],
    );
  }

  public function getBulletin()
  {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'bulletin')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->pager(12)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

}
