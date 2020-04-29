<?php

namespace Drupal\drug_news\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'DrugBannerBlock' block.
 *
 * @Block(
 *  id = "drug_banner_block",
 *  admin_label = @Translation("Drug banner block"),
 * )
 */
class DrugBannerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
    public function build() {
        $about = $this->getHealthy();
        return [
            '#theme' => ['drug_news_banner_block'],
            '#drug_news' => $about,
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

    public function getHealthy(){
        $current_path = \Drupal::service('path.current')->getPath();
        $about = explode('/',$current_path);
        $id = $about[3];
        $entity = \Drupal\drug_news\Entity\DrugNews::load($id);
        return $entity;
    }

}
