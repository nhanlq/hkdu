<?php

namespace Drupal\advert\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Adsleft' block.
 *
 * @Block(
 *  id = "adsleft",
 *  admin_label = @Translation("Adsleft"),
 * )
 */
class Adsleft extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'ads_list',
      '#ads' => $this->checkLeft(),
      '#cache' => ['max-age' => 0,],
    ];
  }

  public function getAdsLeft() {
    $ids = \Drupal::entityQuery('ads')
      ->condition('status', 1)
      ->condition('field_ads_type', 'left')
      ->sort('field_weight', 'ASC')
      ->execute();
    $result = \Drupal\advert\Entity\Ads::loadMultiple($ids);
    return $result;
  }

  public function checkLeft() {
    $ads = $this->getAdsLeft();

    $current_path = \Drupal::service('path.current')->getPath();
    $pathArr = explode('/', $current_path);
    $data = [];
    foreach ($ads as $key => $ad) {
      if ($ad->get('field_path')->getValue()) {
        foreach ($ad->get('field_path')->getValue() as $path) {
          if ($path['value'] == $current_path) {
            $data[$key] = $ad;
          }
          if ($path['value'] == '<front>') {
            if (\Drupal::service('path.matcher')->isFrontPage()) {
              $data[$key] = $ad;
            }
          }
        }
      }
      if ($ad->get('field_section')->getValue()) {

        foreach ($ad->get('field_section')->getValue() as $section) {

          if ($pathArr[1] == 'e-pharm') {
            if ($pathArr[2] == $section['value']) {
              $data[$key] = $ad;
            }
          }
          elseif ($pathArr[1] == 'cme') {
            if ($pathArr[2] == $section['value']) {
              $data[$key] = $ad;
            }
            if ($pathArr[2] = 'links') {
              if ($section['value'] == 'cme-links') {
                $data[$key] = $ad;
              }
            }
          }
          else {

            if ($pathArr[1] == $section['value']) {
              $data[$key] = $ad;
            }
          }
        }
      }
    }
    return $data;

  }

}
