<?php

namespace Drupal\advert\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Adsright' block.
 *
 * @Block(
 *  id = "adsright",
 *  admin_label = @Translation("Adsright"),
 * )
 */
class Adsright extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'adsright';
     $build['adsright']['#markup'] = 'Implement Adsright.';

    return $build;
  }

}
