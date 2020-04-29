<?php

namespace Drupal\special_offer\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'SpecialBannerBlock' block.
 *
 * @Block(
 *  id = "special_banner_block",
 *  admin_label = @Translation("Special banner block"),
 * )
 */
class SpecialBannerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
    public function build() {
        $about = $this->getHealthy();
        return [
            '#theme' => ['special_offer_banner_block'],
            '#special_offer' => $about,
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

    public function getHealthy(){
        $current_path = \Drupal::service('path.current')->getPath();
        $about = explode('/',$current_path);
        $id = $about[3];
        $entity = \Drupal\special_offer\Entity\SpecialOffer::load($id);
        return $entity;
    }

}
