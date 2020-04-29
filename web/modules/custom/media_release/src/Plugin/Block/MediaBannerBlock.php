<?php

namespace Drupal\media_release\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\media_release\Entity\MediaEntity;
/**
 * Provides a 'MediaBannerBlock' block.
 *
 * @Block(
 *  id = "media_banner_block",
 *  admin_label = @Translation("Media banner block"),
 * )
 */
class MediaBannerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
    public function build() {
        $about = $this->getAbout();
        return [
            '#theme' => ['media_banner_block'],
            '#media' => $about,
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

    public function getAbout(){
        $current_path = \Drupal::service('path.current')->getPath();
        $about = explode('/',$current_path);
        $id = $about[3];
        $entity = MediaEntity::load($id);
        return $entity;
    }

}
