<?php

namespace Drupal\healthy\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\healthy\Entity\Healthy;

/**
 * Provides a 'HealthyBannerBlock' block.
 *
 * @Block(
 *  id = "healthy_banner_block",
 *  admin_label = @Translation("Healthy banner block"),
 * )
 */
class HealthyBannerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
    public function build() {
        $about = $this->getHealthy();
        return [
            '#theme' => ['healthy_banner_block'],
            '#healthy' => $about,
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

    public function getHealthy(){
        $current_path = \Drupal::service('path.current')->getPath();
        $about = explode('/',$current_path);
        $id = $about[3];
        $entity = Healthy::load($id);
        return $entity;
    }

}
