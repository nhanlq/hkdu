<?php

namespace Drupal\clinical_focus\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ClinicalBannerBlock' block.
 *
 * @Block(
 *  id = "clinical_banner_block",
 *  admin_label = @Translation("Clinical banner block"),
 * )
 */
class ClinicalBannerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
    public function build() {
        $about = $this->getHealthy();
        return [
            '#theme' => ['clinical_focus_banner_block'],
            '#clinical_focus' => $about,
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

    public function getHealthy(){
        $current_path = \Drupal::service('path.current')->getPath();
        $about = explode('/',$current_path);
        $id = $about[3];
        $entity = \Drupal\clinical_focus\Entity\ClinicalFocus::load($id);
        return $entity;
    }

}
