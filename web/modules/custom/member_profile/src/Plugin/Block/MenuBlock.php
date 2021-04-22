<?php

namespace Drupal\member_profile\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'MenuBlock' block.
 *
 * @Block(
 *  id = "member_menu_block",
 *  admin_label = @Translation("Member Menu block"),
 * )
 */
class MenuBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_path = \Drupal::service('path.current')->getPath();
    $path = explode('/', $current_path);
    $build = [
      '#theme' => 'member_menu',
      '#uid' => $path[2],
    ];

    return $build;
  }

}
