<?php

namespace Drupal\member_profile\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class GalleryController.
 */
class GalleryController extends ControllerBase {

  /**
   * Gallery.
   *
   * @return string
   *   Return Hello string.
   */
  public function gallery($uid) {
    return array(
      'media' => [
        '#theme' => array('member_gallery'),
        '#media' => $this->getMedia($uid),
        '#uri' => \Drupal::service('path.current')->getPath(),
        '#uid' => $uid,
        '#current_uid' => \Drupal::currentUser()->id(),
      ],

      'pager' => [
        '#type' => 'pager',
      ],
    );
  }

  public function getMedia($uid)
  {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'image_gallery')
      ->condition('status', 1)
      ->condition('uid', $uid)
      ->sort('created', 'DESC')
      ->pager(10)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

}
