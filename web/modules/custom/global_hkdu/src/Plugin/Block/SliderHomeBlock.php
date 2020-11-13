<?php

namespace Drupal\global_hkdu\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
/**
 * Provides a 'SliderHomeBlock' block.
 *
 * @Block(
 *  id = "slider_home_block",
 *  admin_label = @Translation("Slider home block"),
 * )
 */
class SliderHomeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
      $banner = $this->getSliderHome();
      return [
          '#theme' => ['global_banner_home'],
          '#nodes' => $banner,
          '#attached' => [
              'library' => [
                  'global_hkdu/global_hkdu_slider',
              ],
          ],
          '#cache' => [
              'max-age' => 0,
          ],
      ];
  }

  public function getSliderHome(){
      $current_path = \Drupal::service('path.current')->getPath();
      $path = explode('/',$current_path);
      if($path[1]=='e-pharm'){
          $ids = \Drupal::entityQuery('node')
              ->condition('status', 1)
              ->condition('type','banner_home')
              ->condition('field_page','e-pharm')
              ->execute();
      }else{
          $ids = \Drupal::entityQuery('node')
              ->condition('status', 1)
              ->condition('type','banner_home')
              ->execute();
      }


      $result = Node::loadMultiple($ids);
      return $result;
  }

}
