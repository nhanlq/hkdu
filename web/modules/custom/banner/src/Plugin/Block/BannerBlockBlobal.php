<?php

namespace Drupal\banner\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\banner\Entity\BannerEntity;

/**
 * Provides a 'BannerBlockBlobal' block.
 *
 * @Block(
 *  id = "banner_block_blobal",
 *  admin_label = @Translation("Banner block global"),
 * )
 */
class BannerBlockBlobal extends BlockBase {

  /**
   * {@inheritdoc}
   */
  /**
   * {@inheritdoc}
   */
  public function build() {
    $banner = $this->getBanner();
    return [
      '#theme' => ['global_banner_block'],
      '#banner' => $banner,
      '#attached' => [
        'library' => [
          'banner/banner_slider',
        ],
      ],
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * @return \Drupal\banner\Entity\BannerEntity|\Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface|false
   */
  public function getBanner() {
    $current_path = \Drupal::service('path.current')->getPath();
    $path = explode('/', $current_path);
    $type = $path[1];
    if ($type == 'e-pharm') {
      $type = $path[2];
    }
    $ids = \Drupal::entityQuery('banner')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->execute();
    $result = BannerEntity::loadMultiple($ids);
    foreach($result as $entity){
      if($type === $entity->get('field_page')->value){
        return $entity;
      }
      if(strpos($current_path, $entity->get('field_special_path')->value) !== false){
        return $entity;
      }
    }
    return [];
  }

}
