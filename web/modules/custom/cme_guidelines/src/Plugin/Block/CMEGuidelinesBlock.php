<?php

namespace Drupal\cme_guidelines\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'CMEGuidelinesBlock' block.
 *
 * @Block(
 *  id = "cmeguidelines_block",
 *  admin_label = @Translation("CME Guidelines Block"),
 * )
 */
class CMEGuidelinesBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
    public function build() {
        $about = $this->getAbout();
        return [
            '#theme' => ['cme_guidelines_banner_block'],
            '#about' => $about,
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

    public function getAbout(){
        $current_path = \Drupal::service('path.current')->getPath();
        $about = explode('/',$current_path);
        $id = $about[3];
        $entity = \Drupal\cme_guidelines\Entity\Guidelines::load($id);
        return $entity;
    }


}
