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

    public function getBanner(){
        $current_path = \Drupal::service('path.current')->getPath();
        $path = explode('/',$current_path);
        $type = $path[1];
        if($type=='e-pharm'){
            $type = $path[2];
        }
        $ids = \Drupal::entityQuery('banner')
            ->condition('status', 1)
            ->condition('field_page',$type)
            ->execute();
        $result = BannerEntity::loadMultiple($ids);
        $banner = reset($result);
        return $banner;
    }

}
