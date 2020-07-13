<?php

namespace Drupal\media_release\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\media_release\Entity\MediaEntity;
/**
 * Provides a 'MediaBannerBlock' block.
 *
 * @Block(
 *  id = "media_home_block",
 *  admin_label = @Translation("Media home block"),
 * )
 */
class MediaHome extends BlockBase {

  /**
   * {@inheritdoc}
   */
    public function build() {
        $about = $this->getAllMedia();
        return [
            '#theme' => ['media_home'],
            '#medias' => $about,
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

    public function getAllMedia()
    {
        $ids = \Drupal::entityQuery('media_entity')
            ->condition('status', 1)
            ->sort('field_weight','ASC')
            ->sort('field_publish_date','DESC')
            ->range(0,3)
            ->execute();
        $result = MediaEntity::loadMultiple($ids);
        return $result;
    }

}
