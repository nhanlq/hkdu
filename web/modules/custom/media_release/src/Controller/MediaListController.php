<?php

namespace Drupal\media_release\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\media_release\Entity\MediaEntity;

/**
 * Class MediaListController.
 */
class MediaListController extends ControllerBase {

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
                '#theme' => 'media_list',
                '#medias' => $this->getAllMedia(),
            ],
            'pager' => [
                '#type' => 'pager',
            ],
        ];
    }

    public function getAllMedia()
    {
        $ids = \Drupal::entityQuery('media_entity')
            ->condition('status', 1)
            ->pager(15)
            ->execute();
        $result = MediaEntity::loadMultiple($ids);
        return $result;
    }

}
