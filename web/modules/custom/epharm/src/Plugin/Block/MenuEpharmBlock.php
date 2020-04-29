<?php

namespace Drupal\epharm\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'MenuEpharmBlock' block.
 *
 * @Block(
 *  id = "menu_epharm_block",
 *  admin_label = @Translation("Menu epharm block"),
 * )
 */
class MenuEpharmBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
      $epharm = false;
      $cme = false;
      $current_path = \Drupal::service('path.current')->getPath();
      $ep = explode('/',$current_path);
      $id = $ep[1];
      if($id=='e-pharm'){
          $epharm = $id;
      }
      if($id=='cme'){
          $cme = $id;
      }
      return [
          '#theme' => ['epharm_menu_block'],
          '#epharm' => $epharm,
          '#cme' => $cme,
          '#attached' => [
              'library' => [
                  'epharm/epharm_menu',
              ],
          ],
          '#cache' => [
              'max-age' => 0,
          ],
      ];
  }

}
