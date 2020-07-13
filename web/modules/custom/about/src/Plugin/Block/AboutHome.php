<?php

namespace Drupal\about\Plugin\Block;

use Drupal\about\Entity\DefaultEntity;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'BannerBlock' block.
 *
 * @Block(
 *  id = "about_home_block",
 *  admin_label = @Translation("About Home block"),
 * )
 */
class AboutHome extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $about = $this->getAbout();
      return [
          '#theme' => ['about_home'],
          '#abouts' => $about,
          '#cache' => [
              'max-age' => 0,
          ],
      ];
  }

    public function getAbout()
    {
        $ids = \Drupal::entityQuery('about')
            ->condition('status', 1)
            ->sort('field_weight', 'ASC')
            ->sort('field_publish_date', 'DESC')
            ->range(0,3)
            ->execute();
        $result = DefaultEntity::loadMultiple($ids);
        return $result;
    }
}
