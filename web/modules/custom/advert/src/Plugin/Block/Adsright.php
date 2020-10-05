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
    public function build()
    {
        return [
            '#theme' => 'ads_list',
            '#ads' => $this->checkLeft(),
            '#cache' => [
                'max-age' => 0,
            ],
        ];
    }

    public function getAdsLeft()
    {
        $ids = \Drupal::entityQuery('ads')
            ->condition('status', 1)
            ->condition('field_ads_type', 'right')
            ->sort('field_weight', 'ASC')
            ->execute();
        $result = \Drupal\advert\Entity\Ads::loadMultiple($ids);
        return $result;
    }

    public function checkLeft()
    {
        $ads = $this->getAdsLeft();

        $current_path = \Drupal::service('path.current')->getPath();
        $pathArr = explode('/', $current_path);
        $data = [];
        foreach ($ads as $ad) {
            if ($ad->get('field_path')->getValue()) {
                foreach ($ad->get('field_path')->getValue() as $path) {
                    if ($path['value'] == $current_path) {
                        $data[] = $ad;
                    }
                    if($path['value']=='<front>'){
                        if(\Drupal::service('path.matcher')->isFrontPage()){
                            $data[] = $ad;
                        }
                    }
                }
            }
            if ($ad->get('field_section')->getValue()) {

                foreach ($ad->get('field_section')->getValue() as $section) {

                    if ($pathArr[1] == 'e-pharm') {
                        if ($pathArr[2] == $section['value']) {
                            $data[] = $ad;
                        }
                    }elseif ($pathArr[1] == 'cme') {
                        if ($pathArr[2] == $section['value']) {
                            $data[] = $ad;
                        }
                        if ($pathArr[2] = 'links') {
                            if ($section['value'] == 'cme-links') {
                                $data[] = $ad;
                            }
                        }
                    }else{

                        if($pathArr[1] == $section['value']){
                            $data[] = $ad;
                        }
                    }


                }
            }
        }
        // var_dump($data);
        return $data;

    }

}
