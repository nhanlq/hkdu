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
    $ads = '';
    $user = \Drupal::currentUser();
    if (in_array('hkdu_members', $user->getRoles()) || in_array('council_members', $user->getRoles())) {
      $ads = '<li><a href="/member/classified-ads">Manage Classified Ads</a></li>';
    }
    $build = [
      '#theme' => 'member_menu',
      '#uid' => $path[2],
      '#ads' => $ads,
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    return $build;
  }

}
