<?php

namespace Drupal\gallery\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\gallery\Entity\Gallery;

/**
 * Class GalleryListController.
 */
class GalleryListController extends ControllerBase {

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
    public function list()
    {

        return [
            'results' => [
                '#theme' => 'gallery_list',
                '#gallerys' => $this->getGallerys(),
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getGallerys()
    {
        $ids = \Drupal::entityQuery('gallery')
            ->condition('status', 1)
            ->sort('field_weight','ASC')
            ->sort('created','DESC')
            ->pager(15)
            ->execute();
        $result = Gallery::loadMultiple($ids);
        return $result;
    }

}
