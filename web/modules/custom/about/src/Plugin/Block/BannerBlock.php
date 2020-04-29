<?php

namespace Drupal\about\Plugin\Block;

use Drupal\about\Entity\DefaultEntity;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'BannerBlock' block.
 *
 * @Block(
 *  id = "banner_block",
 *  admin_label = @Translation("Banner block"),
 * )
 */
class BannerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $about = $this->getAbout();
      return [
          '#theme' => ['about_banner_block'],
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
      $entity = DefaultEntity::load($id);
      return $entity;
  }

}
