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
  public function gallery() {
    return array(
      'media' => [
        '#theme' => array('member_gallery'),
        '#media' => $this->getMedia(),
      ],

      'pager' => [
        '#type' => 'pager',
      ],
      '#cache' => [
        'max-age' => 0,
      ],
    );
  }

  public function getMedia()
  {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'image_gallery')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->pager(12)
      ->execute();
    $result = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $result;
  }

}
