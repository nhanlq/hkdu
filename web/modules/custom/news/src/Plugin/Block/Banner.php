<?php

namespace Drupal\news\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Banner' block.
 *
 * @Block(
 *  id = "banner",
 *  admin_label = @Translation("News Banner"),
 * )
 */
class Banner extends BlockBase {

  /**
   * {@inheritdoc}
   */
    public function build() {
        $about = $this->getHealthy();
        return [
            '#theme' => ['news_banner_block'],
            '#news' => $about,
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

    public function getHealthy(){
        $current_path = \Drupal::service('path.current')->getPath();
        $about = explode('/',$current_path);
        $id = $about[2];
        $entity = \Drupal\news\Entity\News::load($id);
        return $entity;
    }

}
