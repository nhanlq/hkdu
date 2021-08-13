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
    $show_clinic = false;
    if (in_array('hkdu_members', $user->getRoles()) || in_array('council_members', $user->getRoles())) {
      $ads = '<li><a href="/user/member/classified-ads">Manage Classified Ads</a></li><li><a href="/user/member/forum">Manage Forum</a></li>';
      $show_clinic = true;
    }
    if(in_array('doctor', $user->getRoles())){
      $show_clinic = true;
    }
    $hkdu_member = false;
    if(in_array('hkdu_members', $user->getRoles())){
      $hkdu_member = true;
    }
    $build = [
      '#theme' => 'member_menu',
      '#uid' => is_numeric($path[2]) ? $path[2] : $user->id() ,
      '#ads' => $ads,
      '#clinic' => $show_clinic,
      '#hkdu_member' => $hkdu_member,
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    return $build;
  }

}
